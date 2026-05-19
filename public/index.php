<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;

$app = AppFactory::create();

$app->get('/api/health', function ($request, $response) {
	$response->getBody()->write(json_encode([
		'status' => 'API is running'
	]));

	return $response->withHeader('ContentType', 'application/json');
});

$app->run();
