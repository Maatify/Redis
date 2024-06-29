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


}