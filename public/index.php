<?php

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

use Slim\Factory\AppFactory;

$app = AppFactory::create();

require __DIR__ . '/../src/routes/authRoutes.php';

// Endpoint for API health check
$app->get('/api/health', function ($request, $response) {
	$response->getBody()->write(json_encode([
		'status' => 'API is running'
	]));

	return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
