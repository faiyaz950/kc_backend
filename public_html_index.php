<?php

/**
 * KarbalaConnect Backend - BigRock Shared Hosting Entry Point
 * 
 * Place this file in: /home2/hospi5ad/karbalconnect.com/index.php
 * Domain: karbalconnect.com
 */

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Path to Laravel installation
// Laravel is in: /home2/hospi5ad/kc_backend
$laravelPath = '/home2/hospi5ad/kc_backend';

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = $laravelPath.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require $laravelPath.'/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once $laravelPath.'/bootstrap/app.php';

$app->handleRequest(Request::capture());
