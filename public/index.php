<?php

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

use Slim\Factory\AppFactory;

$app = AppFactory::create();

$app->addBodyParsingMiddleware();

// CORS Middleware to allow requests from the frontend
$app->options('/{routes:.+}', function ($request, $response) {
	return $response->withStatus(200);
});

// Routes for authentication and contact management
require __DIR__ . '/../src/Routes/authRoutes.php';
require __DIR__ . '/../src/Routes/contactsRoutes.php';

// Endpoint for API health check
$app->get('/api/health', function ($request, $response) {
	$response->getBody()->write(json_encode([
		'status' => 'API is running'
	]));

	return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
