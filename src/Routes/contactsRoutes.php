<?php

use App\Controllers\ContactsController;
use App\Models\Contact;
use App\Models\User;

/** @var \Slim\App $app */

$contactsController = new ContactsController();

// Endpoint for viewing contact list
$app->get("/api/contacts", [$contactsController, 'getContacts']);

// Endpoint for viewing a single contact by ID
$app->get("/api/contact/{id}", [$contactsController, 'getContact']);

// Endpoint for creating a new contact
$app->post("/api/contacts", [$contactsController, 'createContact']);

// Endpoint for updating a contact by ID
$app->put('/api/contact/{id}', [$contactsController, 'updateContactByID']);

// Endpoint for deleting a contact by ID
$app->delete("/api/contact/{id}", [$contactsController, 'deleteContactByID']);