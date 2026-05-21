<?php

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

use Slim\Factory\AppFactory;
use App\Controllers\AuthController;

$app = AppFactory::create();

// Endpoint for API health check
$app->get('/api/health', function ($request, $response) {
	$response->getBody()->write(json_encode([
		'status' => 'API is running'
	]));

	return $response->withHeader('ContentType', 'application/json');
});

// Endpoints for user login and sign up
$app->post('/api/login', [AuthController::class, 'login']);
$app->post('/api/register', [AuthController::class, 'register']);

$app->run();
