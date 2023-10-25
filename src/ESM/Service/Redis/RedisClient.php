<?php

declare(strict_types=1);

namespace App\ESM\Service\Redis;

use Redis;

/**
 * Class RedisClient.
 */
class RedisClient
{
    /** @var Redis */
    private $client;

    /**
     * RedisClient constructor.
     */
    public function __construct(string $host = 'localhost', int $port = 6379, ?string $auth = null)
    {
        /*$this->client = new Redis();
        $this->client->connect($host, $port);

        if (null !== $auth) {
            $b = $this->client->auth($auth);
        }*/
    }

    /**
     * @return mixed
     */
    public function get(string $key, bool $isJson = true)
    {
        $r = $this->client->get($key);

        if (false !== $r && $isJson) {
            $r = json_decode($r, true);
        }

        return $r;
    }

    /**
     * @param mixed $value
     * @param int   $timeout in seconds
     */
    public function set(string $key, $value, ?int $timeout = null): void
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }

        if (null === $timeout) {
            $this->client->set($key, $value);
        } else {
            $this->client->setex($key, $timeout, $value);
        }
    }

    public function getLastError(): ?string
    {
        return $this->client->getLastError();
    }

    public function close(): void
    {
        $this->client->close();
    }

    public function delete(string $key): void
    {
        $this->client->del($key);
    }
}
