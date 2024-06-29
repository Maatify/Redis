<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2024-06-29
 * Time: 7:30 AM
 * https://www.Maatify.dev
 */

namespace Maatify\Redis;

use \App\Assist\AppFunctions;
use Redis;
use RedisException;

class RedisDefaultHandler extends RedisHandler
{
    private static self $instance;
    protected string $redis_website_prefix;
    protected string $redis_host;
    protected int $redis_expiry;
    protected int $redis_database;
    protected string $redis_password;
    protected string $redis_port;

    protected Redis $redis;

    public static function obj(string $redis_website_prefix): self
    {
        if (empty(self::$instance)) {
            self::$instance = new self($redis_website_prefix);
        }

        return self::$instance;
    }


    /**
     * @param   string  $redis_website_prefix
     */
    public function __construct(string $redis_website_prefix)
    {
        if(empty($_ENV['REDIS_HOST'])) {
            $_ENV['REDIS_HOST'] = '127.0.0.1';
        }
        if(empty($_ENV['REDIS_PORT'])) {
            $_ENV['REDIS_PORT'] = 6379;
        }
        if(empty($_ENV['REDIS_EXPIRY'])) {
            $_ENV['REDIS_EXPIRY'] = AppFunctions::RedisExpiry();
        }
        if(empty($_ENV['REDIS_PASSWORD'])) {
            $_ENV['REDIS_PASSWORD'] = AppFunctions::RedisPassword();
        }
        if(!isset($_ENV['REDIS_DATABASE'])) {
            $_ENV['REDIS_DATABASE'] = AppFunctions::RedisDataBase();
        }
        if(empty($redis_website_prefix)) {
            $redis_website_prefix = AppFunctions::RedisWebsitePrefix();
        }
        $this->redis_website_prefix = $redis_website_prefix;
        $this->redis_host = $_ENV['REDIS_HOST'];
        $this->redis_port = $_ENV['REDIS_PORT'];
        $this->redis_password = $_ENV['REDIS_PASSWORD'];
        $this->redis_database = $_ENV['REDIS_DATABASE'];
        $this->redis_expiry = $_ENV['REDIS_EXPIRY'];
        parent::__construct($this->redis_host, $this->redis_port, $this->redis_password, $this->redis_database, $this->redis_expiry, $this->redis_website_prefix);
//        $this->redis_handler = new RedisHandler($this->redis_host, $this->redis_port, $this->redis_password, $this->redis_database, $this->redis_expiry, $this->redis_website_prefix);
//        $this->redis = $this->redis_handler->Redis();
//        return $this->Redis();
    }

    public function Redis(): Redis
    {
        return $this->redis;
    }

}