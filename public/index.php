<?php

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

use Slim\Factory\AppFactory;

$app = AppFactory::create();

$app->addBodyParsingMiddleware();

// CORS and security headers middleware
$app->add(function ($request, $handler) {
	$response = $handler->handle($request);
	return $response
		->withHeader('Access-Control-Allow-Origin', '*')
		->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
		->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
		->withHeader('X-Frame-Options', 'DENY')
		->withHeader('X-Content-Type-Options', 'nosniff')
		->withHeader('Cross-Origin-Opener-Policy', 'same-origin')
		->withHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
});

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
