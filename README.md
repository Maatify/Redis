[![Current version](https://img.shields.io/packagist/v/maatify/redis)][pkg]
[![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/maatify/redis)][pkg]
[![Monthly Downloads](https://img.shields.io/packagist/dm/maatify/redis)][pkg-stats]
[![Total Downloads](https://img.shields.io/packagist/dt/maatify/redis)][pkg-stats]
[![Stars](https://img.shields.io/packagist/stars/maatify/redis)](https://github.com/maatify/Redis/stargazers)

[pkg]: <https://packagist.org/packages/maatify/redis>
[pkg-stats]: <https://packagist.org/packages/maatify/redis/stats>

# DB-Model

maatify.dev MySql Database PDO Model handler, known by our team

# Installation

```shell
composer require maatify/rides
```

# Usage
#### Create Default Connection 
```PHP
<?php

$redis = new RedisDefaultHandler('maatify:');
$key_name = 'test';
$redis->Set($key_name, 'test:' . date('Y-m-d H:i:s'));

echo $redis->Get($key_name);


echo '<br><hr><br>';

echo $redis->TTL($key_name);
```
#### Create specified Connection 
```PHP
<?php

$redis = new RedisHandler(
(string) $redis_host = '127.0.0.1', 
(int) $redis_port = 6379, 
(string) $redis_password = 'Your Redis Password', 
(int) $redis_database = 1,
(int) $redis_expiry = 5*60, 
(string) $redis_website_prefix = 'maatify:'
);
$key_name = 'test_maatify';
$redis->Set($key_name, 'test_maatify:' . date('Y-m-d H:i:s'));

echo $redis->Get($key_name);


echo '<br><hr><br>';

echo $redis->TTL($key_name);
```
