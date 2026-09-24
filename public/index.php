<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

$baseDir = dirname(__DIR__);

require $baseDir . '/vendor/autoload.php';

$app = require_once $baseDir . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
