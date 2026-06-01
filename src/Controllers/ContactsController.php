<?php

namespace App\Controllers;

use App\Config\Database;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ContactsController {
    public function getContacts(Request $request, Response $response, array $args) {
        // Implementation for fetching all contacts
    }

    public function getContact(Request $request, Response $response, array $args) {
        // Implementation for fetching a single contact by ID
        
    }

    public function addContact(Request $request, Response $response, array $args) {
        // Implementation for adding a new contact
    }

    public function updateContactByID(Request $request, Response $response, array $args) {
        // Implementation for updating a contact by ID
    }

    public function deleteContactByID(Request $request, Response $response, array $args) {
        // Implementation for deleting a contact by ID
    }

}