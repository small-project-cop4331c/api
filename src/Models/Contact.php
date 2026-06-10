<?php

namespace App\Models;

use App\Config\Database;

class Contact {

    public static function getAll(int $userId) : array {
        $db = Database::getConnection();

        // Prepares a statement to select all contacts for a specific user from the database
        $stmt = $db->prepare("SELECT * FROM Contacts WHERE user_id = ? ORDER BY first_name ASC, last_name ASC");
        $stmt->bind_param("i", $userId);
        $stmt->execute();

        // Gets the result of the query and adds it to an array of contacts
        $result = $stmt-> get_result();
        $contacts = [];

        // Fetches each row from the result and adds it to the contacts array
        while ($row = $result->fetch_assoc()) {
            $contacts[] = $row;
        }

        $stmt->close();
        return $contacts;
    }

    public static function getFromSearch(int $userId, string $search): array {
        $db = Database::getConnection();

        // Adds wildcard characters for using the search term in a SQL LIKE query
        $search = "%$search%";

        // Prepares a statement to search for contacts that match the search query
        $stmt = $db->prepare("SELECT * FROM Contacts WHERE user_id = ? AND (first_name LIKE ? OR last_name LIKE ? OR email_address LIKE ? OR phone_number LIKE ?) ORDER BY first_name ASC, last_name ASC");
        $stmt->bind_param("issss", $userId, $search, $search, $search, $search);
        $stmt->execute();

        // Gets the result of the query and adds it to an array of contacts
        $result = $stmt->get_result();
        $contacts = [];

        // Fetches each row from the result and adds it to the contacts array
        while($row = $result->fetch_assoc()) {
            $contacts[] = $row;
        }

        $stmt->close();
        
        // Returns the found contacts as an array
        return $contacts;
    }

    // Model function to get a contact by ID from the database
    public static function getById(int $userId, int $id): ?array{
        $db = Database::getConnection();

        // Prepare and execute a statement to select a contact by ID from the database
        $stmt = $db->prepare("SELECT * FROM Contacts WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $id, $userId);
        $stmt->execute();

        $result = $stmt->get_result();
        $contact = $result->fetch_assoc();

        $stmt->close();

        // Returns null if the contact is not found, 
        // otherwise returns the contact data as an array
        return $contact ?: null;
    }

    // Model function to create a new contact in the database
    public static function create(int $userId, string $firstName, string $lastName, string $phoneNumber, string $emailAddress): bool {
        $db = Database::getConnection();
        
        // Prepare a statement to insert a new contact into the database
        $stmt = $db->prepare("INSERT INTO Contacts (first_name, last_name, phone_number, email_address, user_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $firstName, $lastName, $phoneNumber, $emailAddress, $userId);
        
        // Executes the query and returns true if the contact was created successfully
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }
    
    // Model function to update a contact by ID in the database
    public static function updateById(int $userId, int $id, string $firstName, string $lastName, string $phoneNumber, string $emailAddress): array {
        $db = Database::getConnection();

        // Prepares a statement to update a contact by ID in the database
        $stmt = $db->prepare("UPDATE Contacts SET first_name = ?, last_name = ?, phone_number = ?, email_address = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ssssii", $firstName, $lastName, $phoneNumber, $emailAddress, $id, $userId);

        // Executes the query and returns true if the contact was updated successfully
        $success = $stmt->execute();

        // Returns result of the update operation and the number of affected rows
        $affected_rows = $stmt->affected_rows;
        $stmt->close();
        
        return ['success' => $success, 'affectedRows' => $affected_rows];
    }

    // Model function to delete a contact by ID from the database
    public static function deleteById(int $userId, int $id): array {
        $db = Database::getConnection();

        // Prepares a statement to delete a contact by ID from the database
        $stmt = $db->prepare("DELETE FROM Contacts WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $id, $userId);

        // Executes the query and returns true if the contact was deleted successfully
        $success = $stmt->execute();

        // Returns result of the delete operation and the number of affected rows
        $affected_rows = $stmt->affected_rows;
        $stmt->close();

        return ['success' => $success, 'affectedRows' => $affected_rows];
    }
}