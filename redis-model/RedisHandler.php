<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2024-06-29
 * Time: 7:08 AM
 * https://www.Maatify.dev
 */

namespace Maatify\Redis;

use Redis;
use RedisException;

class RedisHandler
{
    protected string $redis_website_prefix;
    protected string $redis_host;
    protected string $redis_port;
    protected string $redis_password;
    protected int $redis_database;
    protected int $redis_expiry;

    protected Redis $redis;

    /**
     * @throws RedisException
     */
    public function __construct(string $redis_host, int $redis_port, string $redis_password, int $redis_database,int $redis_expiry, string $redis_website_prefix)
    {
        $this->redis_website_prefix = $redis_website_prefix;
        $this->redis_host = $redis_host;
        $this->redis_port = $redis_port;
        $this->redis_password = $redis_password;
        $this->redis_database = $redis_database;
        $this->redis_expiry = $redis_expiry;
        $this->redis = new Redis();
        $this->redis->connect($redis_host, $redis_port);
        $this->redis->auth($redis_password);
        $this->redis->select($redis_database); // Use database 0
    }

    public function Redis(): Redis
    {
        return $this->redis;
    }

    /**
     * @throws RedisException
     */
    public function Set(string $key, $value, ?int $expiry = null): void
    {
        $this->redis->set($this->redis_website_prefix . $key, $value, ['ex' => $expiry ? : $this->redis_expiry]);
    }

    /**
     * @throws RedisException
     */
    public function Get(string $key)
    {
        return $this->redis->get($this->redis_website_prefix . $key);
    }

    /**
     * @throws RedisException
     */
    public function TTL(string $key): bool|int|Redis
    {
        return $this->redis->ttl($this->redis_website_prefix . $key);
    }

    /**
     * @throws RedisException
     */
    public function Clear(): bool|Redis
    {
        return $this->redis->flushAll();
    }

    /**
     * @throws RedisException
     */
    public function Delete(array $keys): bool|int|Redis
    {
        $delete_keys = array();
        foreach ($keys as $key) {
            $delete_keys[] = $this->redis_website_prefix . $key;
        }
        return $this->redis->unlink($delete_keys);
    }

    public function unlinkPrefixStartWith(string $key): bool|Redis
    {
        $it = NULL;
        $keys = [];

        while ($keysChunk = $this->redis->scan($it, $this->redis_website_prefix . $key . '*')) {
            $keys = array_merge($keys, $keysChunk);
        }
        if (!empty($keys)) {
            return $this->redis->unlink($keys);
        }
        return false;
    }

    /**
     * @throws RedisException
     */
    public function RenameKey(string $old_key, string $new_key): bool|Redis
    {
        return $this->redis->rename($this->redis_website_prefix . $old_key, $new_key);
    }

    /**
     * @throws RedisException
     */
    public function SetArrayAsJson(string $key, array $array): bool|Redis
    {
        return $this->redis->set($this->redis_website_prefix . $key, json_encode($array));

    }

    /**
     * @throws RedisException
     */
    public function GetArrayAsJson(string $key)
    {
        $result_json = $this->redis->get($this->redis_website_prefix . $key);
        return json_decode($result_json, true);
    }

    /**
     * use as MultipleGet(['key_1', 'key_2', 'key_3'])
     * return should be
     *
     * Array
     * (
     * [0] => value1
     * [1] => value2
     * [2] => value3
     * )
     *
     * @throws RedisException
     */
    public function MultipleGet(array $keys): array|bool|Redis
    {
        $getting_keys = [];
        foreach ($keys as $key) {
            $getting_keys[] = $this->redis_website_prefix . $key;
        }
        return $this->redis->mget($getting_keys);
    }

    /**
     * @throws RedisException
     */
    public function SetSerializeArray(string $key, array $array): void
    {
        // Serialize the array
        $serializedArray = serialize($array);

        // Store the serialized array in Redis with an expiration time of 60 seconds
        $this->Set($key, $serializedArray);
    }

    /**
     * @throws RedisException
     */
    public function GetSerializedArray(string $key)
    {
        // Retrieve the serialized array from Redis
        $retrievedArray = $this->Get($key);

        // UnSerialize the array
        return unserialize($retrievedArray);
    }


    /**
     * @throws RedisException
     */
    public function ListPush(string $key, array $array): void
    {
        foreach ($array as $value) {
            $this->redis->rpush($this->redis_website_prefix . $key, $value);
        }
    }

    /**
     * @throws RedisException
     */
    public function ListRange(string $key, int $start = 0, int $end = -1): array|Redis
    {
        return $this->redis->lrange($this->redis_website_prefix . $key, $start, $end);
    }

    /**
     * @throws RedisException
     */
    public function ListLength(string $key): bool|int|Redis
    {
        // Get the length of the list
        return $this->redis->lLen($this->redis_website_prefix . $key);
    }

    /**
     * @throws RedisException
     */
    public function ListByIndex(string $key, int $index)
    {
        return $this->redis->lIndex($this->redis_website_prefix . $key, $index);
        
    }

    /**
     * @throws RedisException
     */
    public function IsKeyExist(string $key): bool|int|Redis
    {
        return $this->redis->exists($this->redis_website_prefix . $key);
    }

    /**
     * @throws RedisException
     */
    public function Info(): bool|array|Redis
    {
        return $this->redis->info();
    }

    /**
     * @throws RedisException
     */
    public function UsedMemorySize()
    {
        $info = $this->Info();
        return $info['used_memory'];
    }

    /**
     * @throws RedisException
     */
    public function MasterLinkStatus()
    {
        $info = $this->Info();
        return $info['master_link_status'];
    }

    /**
     * @throws RedisException
     */
    public function AllKeys(): array|bool|Redis
    {
        return $this->redis->keys('*');
    }

    /**
     * @throws RedisException
     */
    public function AllKeysAndValues(): array
    {
        $result = array();
        $keys = $this->AllKeys();
        if(!empty($keys)) {
            foreach ($keys as $key) {
                $result[$key] = $this->redis->get($key);
            }
        }
        return $result;
    }

    /**
     * @throws RedisException
     */
    public function hSet(string $key, string $h_key_name, array $array): bool|int|Redis
    {
        return $this->redis->hSet($this->redis_website_prefix . $key,
            $h_key_name,
            json_encode($array, JSON_UNESCAPED_UNICODE));
    }

    /**
     * @throws RedisException
     */
    public function hGet(string $key, string $h_key_name)
    {
        return $this->redis->hGet($this->redis_website_prefix . $key, $h_key_name);
    }

    /**
     * @throws RedisException
     */
    public function hGetAll(string $key): array|bool|Redis
    {
        return $this->redis->hGetAll($this->redis_website_prefix . $key);
    }

    /**
     * @throws RedisException
     */
    public function hDel(string $key, string $h_key_name): bool|int|Redis
    {
        return $this->redis->hDel($this->redis_website_prefix . $key, $h_key_name);
    }

    /**
     * @throws RedisException
     */
    public function publish(string $channel, string $message): bool|int|Redis
    {
        return $this->redis->publish($this->redis_website_prefix . $channel, $message);
    }

}