<?php

use App\Controllers\AuthController;

/** @var \Slim\App $app */

// Endpoints for user login and sign up
$app->post('/api/login', [AuthController::class, 'login']);

$app->post('/api/register', [AuthController::class, 'register']);