<?php

namespace App\Models;

use App\Config\Database;

class User {

    // Model function to check if an email already exists in the database
    public static function emailExists(string $email): bool {
        $db = Database::getConnection();

        // Creates statement to check if email exists in the database
        $stmt = $db->prepare(
            "SELECT id FROM Users WHERE email_address = ?"
        );
        $stmt->bind_param("s", $email);

        // Executes the query with the provided email
        $stmt->execute();

        // Gets result of the query and returns true 
        //if the email exists
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();

        return $exists;
    }

    // Model function to create a new user in the database
    public static function createUser(string $firstName, string $lastName, string $email, string $hashedPassword): bool {
        $db = Database::getConnection();

        // Prepares a statement to insert a new user into the database
        $stmt = $db->prepare("INSERT INTO Users (first_name, last_name, email_address, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $firstName, $lastName, $email, $hashedPassword);

        // Executes the query and returns true if the user was created successfully
        $success = $stmt->execute();
        $stmt->close();

        // Returns true if the user was created successfully
        return $success;
    }
}