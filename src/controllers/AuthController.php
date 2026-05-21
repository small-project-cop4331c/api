<?php

namespace App\Controllers;

use App\Config\Database;

class AuthController {
    // Login function
    public function login($request, $response) {
        
        // Parses request body to get variables
        $data = $request->getParsedBody();

        // Gets email and password from the request body
        $email = $data['email'];
        $password = $data['password'];

        // Returns an error if email or password is empty
        if(empty($email) || empty($password)) {
            return $response->withStatus(0, '');
        }

        // Establishes connection with the database
        $db = Database::getConnection();

        // SQL query to select the user with the input email
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        
        // Binds the email parameter to avoid an SQL injection
        // and executes the query
        $stmt->bind_param("s", $email);
        $stmt->execute();

        // Gets query result
        $result = $stmt->get_result();

        // Checks if user exists
        if($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Verifies the password by comparing it with the hashed password
            if(password_verify($password, $user['password'])) {

                // If password is correct, returns user data in the response
                $response->getBody()->write(json_encode([
                    'message' => 'Login successful',
                    'user' => [
                        'id' => $user['id'],
                        'firstname' => $user['firstname'],
                        'lastname' => $user['lastname'],
                        'email' => $user['email']
                    ]
                ]));

                // Returns successful login response with 200 status code
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            }else{
                // If password is incorrect, returns an error message with 401 status code
                $response->getBody()->write(json_encode([
                    'error' => 'Invalid email or password'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
            }
        }else{
            // If user does not exist, returns an error message with 401 status code
            $response->getBody()->write(json_encode([
                'error' => 'Invalid email or password'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

    }

    public function register($request, $response) {
        $data = $request->getParsedBody();

        $firstName = $data['firstname'];
        $lastName = $data['lastname'];
        $email = $data['email'];
        $password = $data['password'];
        $confirmPassword = $data['confirmpassword'];

        // Check if passwords are equal
        if ($confirmPassword != $password) {
            $response->getBody()->write(json_encode([
                'error' => 'Passwords do not match'
            ]));

            // Return a Bad Request Error Code (400)
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        // Hashes the password before storing it in the database
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    }

}