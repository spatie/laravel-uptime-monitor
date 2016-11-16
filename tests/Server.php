<?php

namespace Spatie\UptimeMonitor\Test;

use GuzzleHttp\Client;

class Server
{
    /** @var \GuzzleHttp\Client */
    protected $client;

    public function __construct()
    {
        $this->client = new Client();

        $this->up();
    }

    public function setResponseBody(string $text, int $statusCode = 200)
    {
        $this->client->post('http://localhost:8080/setServerResponse', [
            'form_params' => [
                'statusCode' => $statusCode,
                'body' => $text,
            ],
        ]);
    }

    public function up()
    {
        $this->setResponseBody('Site is up', 200);
    }

    public function down()
    {
        $this->setResponseBody('Site is down', 503);
    }
}
