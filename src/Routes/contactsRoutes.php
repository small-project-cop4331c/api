<?php

use App\Controllers\ContactsController;
use App\Middleware\JwtMiddleware;

/** @var \Slim\App $app */

// Groups all contact-related endpoints and adds the JwtMiddleware to ensure they
// require authentication.
$app->group('/api/contacts', function ($group) {
    $contactsController = new ContactsController();

    // Endpoint for viewing contact list based on search query
    $group->get('', [$contactsController, 'getContacts']);

    // Endpoint for viewing a single contact by ID
    $group->get('/{id}', [$contactsController, 'getContact']);

    // Endpoint for creating a new contact
    $group->post('', [$contactsController, 'addContact']);

    // Endpoint for updating a contact by ID
    $group->put('/{id}', [$contactsController, 'updateContactById']);

    // Endpoint for deleting a contact by ID
    $group->delete('/{id}', [$contactsController, 'deleteContactById']);

})->add(new JwtMiddleware());