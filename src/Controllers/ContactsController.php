<?php

namespace App\Controllers;

use App\Models\Contact;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ContactsController {
    public function getContacts(Request $request, Response $response, array $args): Response {

        try {

            $userId = $request->getAttribute("userId");

            // Gets query parameters from the request
            $queryParams = $request->getQueryParams();
            $search = trim($queryParams['search'] ?? '');

            // Fetches contacts for the user from the database
            // based on the search query
            $contacts = ($search === '') ? Contact::getAll($userId) : Contact::getFromSearch($userId, $search);

            // Writes a success message and the retrieved contacts to the response body
            $response->getBody()->write(json_encode([
                'message' => 'The user\'s contacts were retrieved successfully.',
                'contacts' => $contacts
            ]));

            // Returns a 200 OK response with the retrieved contacts
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch(\Exception $e) {
            // Writes an error message to the response body if there was an
            // issue retrieving the contacts from the database
            $response->getBody()->write(json_encode([
                'error' => 'The user\'s contacts could not be retrieved.'
            ]));

            // Returns response with a 500 Internal Server Error
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
        
    }

    public function getContact(Request $request, Response $response, array $args): Response {
        // Implementation for fetching a single contact by ID
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        
    }

    public function addContact(Request $request, Response $response, array $args): Response {
        // Implementation for adding a new contact
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function updateContactById(Request $request, Response $response, array $args): Response {
        // Implementation for updating a contact by ID
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function deleteContactById(Request $request, Response $response, array $args): Response {
        // Implementation for deleting a contact by ID
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

}