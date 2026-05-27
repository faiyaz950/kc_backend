<?php

/**
 * KarbalaConnect Backend - Hostinger Shared Hosting Entry Point
 *
 * Place in: ~/domains/karbalaconnect.in/public_html/index.php
 * Laravel:  ~/kc_backend
 */

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Relative path works on Hostinger: /home/u163472436/kc_backend
$laravelPath = __DIR__.'/../../../kc_backend';

if (file_exists($maintenance = $laravelPath.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $laravelPath.'/vendor/autoload.php';

/** @var Application $app */
$app = require_once $laravelPath.'/bootstrap/app.php';

$app->handleRequest(Request::capture());
