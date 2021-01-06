<?php

namespace Spatie\UptimeMonitor\Test;

use GuzzleHttp\Client;

class Server
{
    const ENV_SERVER_PORT = 'TEST_SERVER_PORT';

    /** @var \GuzzleHttp\Client */
    protected $client;

    public function __construct(Client $client)
    {
        static::boot();

        $this->client = $client;

        $this->up();
    }

    public function up()
    {
        $this->setResponseBody('Site is up', 200);
    }

    public function down()
    {
        $this->setResponseBody('Site is down', 503);
    }

    public function setResponseBody(string $text, int $statusCode = 200)
    {
        $this->client->post(static::getServerUrl('setServerResponse'), [
            'form_params' => [
                'statusCode' => $statusCode,
                'body' => $text,
            ],
        ]);
    }

    public static function boot()
    {
        if (empty(getenv(self::ENV_SERVER_PORT))) {
            throw new \InvalidArgumentException(sprintf('`%s` environment variable is not set', self::ENV_SERVER_PORT));
        }

        if (! file_exists(__DIR__.'/server/vendor')) {
            exec('cd "'.__DIR__.'/server"; composer install');
        }

        if (static::serverHasBooted()) {
            return;
        }

        $pid = exec('php -S '.static::getServerUrl().' -t ./tests/server/public > /dev/null 2>&1 & echo $!');
        while (! static::serverHasBooted()) {
            usleep(1000);
        }

        register_shutdown_function(function () use ($pid) {
            exec('kill '.$pid);
        });
    }

    public static function getServerUrl(string $endPoint = ''): string
    {
        return rtrim(sprintf('localhost:%s/%s', getenv('TEST_SERVER_PORT'), $endPoint), '/');
    }

    public static function serverHasBooted(): bool
    {
        return @file_get_contents('http://'.self::getServerUrl('booted')) != false;
    }
}
