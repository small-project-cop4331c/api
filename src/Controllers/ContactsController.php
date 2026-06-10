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
        // Fetches a specific contact by ID for the authenticated user
        try {
            // Gets user ID from the request attributes
            $userId = $request->getAttribute("userId");
            $id = (int) $args['id'];

            $contact = Contact::getById($userId, $id);

            // Returns a response with status code 404 if the contact was not found
            if ($contact === null) {
                $response->getBody()->write(json_encode([
                    'error' => 'Contact not found.'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            // Returns a successful response with status code 200 and the retrieved contact
            $response->getBody()->write(json_encode([
                'message' => 'Contact retrieved successfully.',
                'contact' => $contact
            ]));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            
            // Returns a response with status code 500 if there was an error retrieving the contact from the database
            $response->getBody()->write(json_encode([
                'error' => 'The contact could not be retrieved.'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function addContact(Request $request, Response $response, array $args): Response {
        // Adds a new contact for the authenticated user
        try {
            // Gets user ID from the request attributes
            $userId = $request->getAttribute("userId");

            // Parses the request body to extract contact fields
            $body = $request->getParsedBody();

            $firstName    = trim($body['first_name'] ?? '');
            $lastName     = trim($body['last_name'] ?? '');
            $phoneNumber  = trim($body['phone_number'] ?? '');
            $emailAddress = trim($body['email_address'] ?? '');

            // Returns a response with status code 400 if required fields are missing
            if ($firstName === '' || $lastName === '') {
                $response->getBody()->write(json_encode([
                    'error' => 'First name and last name are required.'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }

            // Inserts the new contact into the database
            $success = Contact::create($userId, $firstName, $lastName, $phoneNumber, $emailAddress);

            if (!$success) {
                throw new \Exception("Insert failed.");
            }

            // Returns a successful response with status code 201 indicating the contact was created
            $response->getBody()->write(json_encode([
                'message' => 'Contact added successfully.'
            ]));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\Exception $e) {
            // Returns a response with status code 500 if there was an error adding the contact to the database
            $response->getBody()->write(json_encode([
                'error' => 'The contact could not be added.'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function updateContactById(Request $request, Response $response, array $args): Response {
        // Updates a specific contact by ID for the authenticated user
        try {
            // Gets user ID from the request attributes and the contact ID from the route arguments
            $userId = $request->getAttribute("userId");
            $id = (int) $args['id'];

            // Parses the request body to extract updated contact fields
            $body = $request->getParsedBody();

            $firstName    = trim($body['first_name'] ?? '');
            $lastName     = trim($body['last_name'] ?? '');
            $phoneNumber  = trim($body['phone_number'] ?? '');
            $emailAddress = trim($body['email_address'] ?? '');

            // Returns a response with status code 400 if required fields are missing
            if ($firstName === '' || $lastName === '') {
                $response->getBody()->write(json_encode([
                    'error' => 'First name and last name are required.'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }

            // Updates the contact in the database
            $result = Contact::updateById($userId, $id, $firstName, $lastName, $phoneNumber, $emailAddress);

            // Returns a response with status code 404 if no matching contact was found
            if ($result['affectedRows'] === 0) {
                $response->getBody()->write(json_encode([
                    'error' => 'Contact not found.'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            // Returns a successful response with status code 200 indicating the contact was updated
            $response->getBody()->write(json_encode([
                'message' => 'Contact updated successfully.'
            ]));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            // Returns a response with status code 500 if there was an error updating the contact in the database
            $response->getBody()->write(json_encode([
                'error' => 'The contact could not be updated.'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function deleteContactById(Request $request, Response $response, array $args): Response {
        // Deletes a specific contact by ID for the authenticated user
        try {
            // Gets user ID from the request attributes and the contact ID from the route arguments
            $userId = $request->getAttribute("userId");
            $id = (int) $args['id'];

            // Deletes the contact from the database
            $result = Contact::deleteById($userId, $id);

            // Returns a response with status code 404 if no matching contact was found
            if ($result['affectedRows'] === 0) {
                $response->getBody()->write(json_encode([
                    'error' => 'Contact not found.'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            // Returns a successful response with status code 200 indicating the contact was deleted
            $response->getBody()->write(json_encode([
                'message' => 'Contact deleted successfully.'
            ]));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            // Returns a response with status code 500 if there was an error deleting the contact from the database
            $response->getBody()->write(json_encode([
                'error' => 'The contact could not be deleted.'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

}