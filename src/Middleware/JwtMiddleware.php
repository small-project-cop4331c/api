<?php

namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

use Slim\Psr7\Response as SlimResponse;

class JwtMiddleware {

    // The following function is invoked when the middleware is called in the route definition.
    // It was moved from the ContactsController to allow for better separation of concerns
    // and to make it reusable for other routes that might require authentication.

    public function __invoke(Request $request, RequestHandler $handler): Response {
        $authHeader = $request->getHeaderLine('Authorization');

        // Checks if the Authorization header is present and contains a Bearer token
        if(!preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            $response = new SlimResponse();    

            $response->getBody()->write(json_encode([
                'error' => 'Missing token.'
            ]));

            // Returns a 401 Unauthorized response if the authorization header is missing
            // or does not contain a valid Bearer token
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        // Extracts the token from the Authorization header
        $token = $matches[1];

        try {
            // Extracts the user ID from the JWT token
            $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));

            if(!isset($decoded->userId)) {
                throw new \Exception('Invalid token payload.');
            }

            $request = $request->withAttribute('userId', $decoded->userId);
            
            return $handler->handle($request);

        } catch(\Exception $e) {

            $response = new SlimResponse();

            // Returns a 401 Unauthorized response if the token is invalid or expired
            $response->getBody()->write(json_encode([
                'error' => 'Invalid token.'
            ]));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

    }

}