<?php

use Illuminate\Http\Request;

require_once __DIR__.'/../vendor/autoload.php';

$app = new Laravel\Lumen\Application(
    realpath(__DIR__.'/../')
);

$storagePath = __DIR__.'/../storage/server-status-code.json';

$app->router->get('/', function () use ($storagePath) {
    if (! file_exists($storagePath)) {
        return response('Site is up', 200);
    }

    $response = json_decode(file_get_contents($storagePath), true);

    if ($response['statusCode'] == 301) {
        return redirect(url('/notfound'), 301);
    }

    return response($response['body'], $response['statusCode']);
});

$app->router->post('/setServerResponse', function (Request $request) use ($storagePath) {
    $response = json_encode($request->all(), true);

    file_put_contents($storagePath, $response);
});

$app->router->post('/testPost', function (Request $request) {
    if ($request->get('foo') !== 'bar' && $request->header('Content-Type') !== 'application/json') {
        return response(null, 500);
    }

    return response(null, 200);
});

$app->router->get('booted', function () {
    return 'app has booted';
});

$app->run();
