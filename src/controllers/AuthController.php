<?php

namespace App\Controllers;

use App\Config\Database;

class AuthController {
    // Login function
    public function login($request, $response) {
        
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

                // If password is correct, returns user data in the response
                $response->getBody()->write(json_encode([
                    'message' => 'Login successful.',
                    'user' => [
                        'id' => $user['id'],
                        'firstName' => $user['first_name'],
                        'lastName' => $user['last_name'],
                        'email' => $user['email_address']
                    ]
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
            // If user does not exist, returns an error message with 401 status code
            $response->getBody()->write(json_encode([
                'error' => 'Invalid email or password.'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

    }

    public function register($request, $response) {
        
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

        // Establishes connection with the database
        $db = Database::getConnection();
    
        // Checks if the inserted email is already registered in the database
        $stmt = $db->prepare("SELECT id FROM Users WHERE email_address = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
    
        $result = $stmt->get_result();

        // If the email is already registered, returns an error message
        if ($result->num_rows > 0) {
            $stmt->close();

            $response->getBody()->write(json_encode([
                'error' => 'Email is already in use.'
            ]));

            // Returns a Conflict Status Code (409)
            return $response->withHeader('Content-Type', 'application/json')->withStatus(409);
        }

        $stmt->close();

        // Insert new user into the database's user table
        $stmt = $db->prepare("INSERT INTO Users (first_name, last_name, email_address, password) VALUES (?, ?, ?, ?)");

        // Binds parameters to avoid SQL injections and executes the query
        $stmt->bind_param("ssss", $firstName, $lastName, $email, $hashedPassword);

        if($stmt->execute()){
            $stmt->close();
            // If registration is successful, returns 201 status code and success message
            $response->getBody()->write(json_encode([
                'message' => 'User registered successfully.'
            ]));

            // Returns request with status code 201 (Created)
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        }else{
            $stmt->close();
            // If user could not be registered, returns status code 500 for internal
            // server error and an error message
            $response->getBody()->write(json_encode([
                'error' => 'Error registering user.'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

}