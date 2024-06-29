<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2024-06-29
 * Time: 7:08 AM
 * https://www.Maatify.dev
 */

namespace Maatify\Redis;

use Maatify\Json\Json;
use Maatify\Logger\Logger;
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

    public function Redis(): \Redis
    {
        return $this->redis;
    }

    public function Set(string $key, $value): void
    {
        $this->redis->set($this->redis_website_prefix . $key, $value, ['ex' => $this->redis_expiry]);
    }

    public function Get(string $key)
    {
        return $this->redis->get($this->redis_website_prefix . $key);
    }

    public function TTL(string $key)
    {
        return $this->redis->ttl($this->redis_website_prefix . $key);
    }

    public function Clear()
    {
        return $this->redis->flushAll();
    }

    public function Delete($keys)
    {
        $delete_keys = array();
        foreach ($keys as $key) {
            $delete_keys[] = $this->redis_website_prefix . $key;
        }
        return $this->redis->unlink($delete_keys);
    }

    public function RenameKey(string $old_key, string $new_key)
    {
        return $this->redis->rename($this->redis_website_prefix . $old_key, $new_key);
    }

    public function SetArrayAsJson(string $key, array $array)
    {
        return $this->redis->set($this->redis_website_prefix . $key, json_encode($array));

    }

    public function GetArrayAsJson(string $key)
    {
        $result_json = $this->redis->get($this->redis_website_prefix . $key);
        return json_decode($result_json, true);
    }

    /**
     * use as MultipleGet(['key_1', 'key_2', 'key_3'])
     * return should be
     *
Array
(
    [0] => value1
    [1] => value2
    [2] => value3
)

     * */
    public function MultipleGet(array $keys)
    {
        $getting_keys = [];
        foreach ($keys as $key) {
            $getting_keys[] = $this->redis_website_prefix . $key;
        }
        return $this->redis->mget($getting_keys);
    }

    public function SetSerializeArray(string $key, array $array): void
    {
        // Serialize the array
        $serializedArray = serialize($array);

        // Store the serialized array in Redis with an expiration time of 60 seconds
        $this->Set($key, $serializedArray);
    }

    public function GetSerializedArray(string $key)
    {
        // Retrieve the serialized array from Redis
        $retrievedArray = $this->Get($key);

        // UnSerialize the array
        return unserialize($retrievedArray);
    }



    public function ListPush(string $key, array $array): void
    {
        foreach ($array as $value) {
            $this->redis->rpush($this->redis_website_prefix . $key, $value);
        }
    }

    public function ListRange(string $key, int $start = 0, int $end = -1)
    {
        return $this->redis->lrange($this->redis_website_prefix . $key, $start, $end);
    }

    public function ListLength(string $key)
    {
        // Get the length of the list
        return $this->redis->lLen($this->redis_website_prefix . $key);
    }

    public function ListByIndex(string $key, int $index)
    {
        return $this->redis->lIndex($this->redis_website_prefix . $key, $index);
        
    }


}