<?php

use App\Controllers\AuthController;

/** @var \Slim\App $app */

$authController = new AuthController();

// Endpoints for user login and sign up
$app->post('/api/login', [$authController, 'login']);

$app->post('/api/register', [$authController, 'register']);