<?php

namespace App\Controllers;

use App\Config\Database;
use Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Models\User;

class AuthController {
    // Login function
    public function login(Request $request, Response $response): Response {
        
        // Parses request body to get variables
        $data = $request->getParsedBody();

        // Extracts email and password from the request body
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';

        // Returns an error if email or password is empty
        if(empty($email) || empty($password)) {
            $response->getBody()->write(json_encode([
                'error' => 'Email and password are required.'
            ]));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        // Validates email format and returns an error if it's invalid
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $response->getBody()->write(json_encode([
                'error' => 'Invalid email format.'
            ]));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        // Establishes connection with the database
        $db = Database::getConnection();

        // SQL query to select the user with the input email
        $stmt = $db->prepare("SELECT id, first_name, last_name, email_address, password FROM Users WHERE email_address = ?");
        
        // Binds the email parameter to avoid an SQL injection
        // and executes the query
        $stmt->bind_param("s", $email);
        $stmt->execute();

        // Gets query result
        $result = $stmt->get_result();
        
        // Checks if user exists
        if($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $stmt->close();

            // Verifies the password by comparing it with the hashed password
            if(password_verify($password, $user['password'])) {

                // Creates a payload to send with the response
                $payload = [
                    'userId' => $user['id'],
                    'email' => $user['email_address'],
                    'iat' => time(), // Issued at time
                    'exp' => time() + (60 * 60) // Token expires in 1 hour
                ];

                // Encodes the payload into a JWT token using the .env secret key
                $token = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');

                // If password is correct, returns user data in the response
                $response->getBody()->write(json_encode([
                    'message' => 'Login successful.',
                    'user' => [
                        'id' => $user['id'],
                        'firstName' => $user['first_name'],
                        'lastName' => $user['last_name'],
                        'email' => $user['email_address']
                    ],
                    'token' => $token
                ]));

                // Returns successful login response with 200 status code
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            }else{
                // If password is incorrect, returns an error message with 401 status code
                $response->getBody()->write(json_encode([
                    'error' => 'Invalid email or password.'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
            }
        }else{
            $stmt->close();
            // If user does not exist, returns an error message with 401 status code
            $response->getBody()->write(json_encode([
                'error' => 'Invalid email or password.'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

    }

    public function register(Request $request, Response $response): Response {
        
        // Parses request body to get variables
        $data = $request->getParsedBody();

        // Extracts user data from the request body
        $firstName = trim($data['firstName'] ?? '');
        $lastName = trim($data['lastName'] ?? '');
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';
        $confirmPassword = $data['confirmPassword'] ?? '';

        // Verifies that all fields are filled before registering the user
        if(empty($firstName) || empty($lastName) || empty($email) || empty($password) || empty($confirmPassword)) {
            $response->getBody()->write(json_encode([
                'error' => 'All fields are required.'
            ]));

            // Returns a Bad Request Status Code (400) if any fields are empty
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        // Validates email format and returns an error if it's invalid
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $response->getBody()->write(json_encode([
                'error' => 'Invalid email format.'
            ]));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        // Check if password and confirmation are equal
        if ($confirmPassword !== $password) {
            $response->getBody()->write(json_encode([
                'error' => 'Passwords do not match.'
            ]));

            // Returns a Bad Request Error Code (400)
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        // Checks for minimum password length requirement
        if(strlen($password) < 8) {
            $response->getBody()->write(json_encode([
                'error' => 'Password must be at least 8 characters long.'
            ]));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        // Returns an error if the password contains whitespace characters
        if(preg_match('/\s/', $password) || preg_match('/\s/', $confirmPassword)) {
            $response->getBody()->write(json_encode([
                'error' => 'Password cannot contain whitespace characters.'
            ]));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        // Hashes the password before storing it in the database
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Uses the User model to verify if the email is already in use and returns
        // an error if it is
        if(User::emailExists($email)) {
            $response->getBody()->write(json_encode([
                'error' => 'Email is already in use.'
            ]));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(409);
        }

        // Uses the User model to create a new user in the database and checks if the registration was successful
        if(User::createUser($firstName, $lastName, $email, $hashedPassword)) {
            // If registration is sucessful, returns 201 status code and success message
            $response->getBody()->write(json_encode([
                'message' => 'User registered successfully.'
            ]));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        }else{
            // If user could not be registered, returns status code 500 for internal
            // server error and an error message
            $response->getBody()->write(json_encode([
                'error' => 'Error registering user.'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

}