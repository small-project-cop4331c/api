# Contacts App
This is a the API for our full-stack contacts management application developed for the COP 4331 class. The application allows users to register, authenticate using JWT tokens, and manage personal contacts through a secure REST API.

## Base URL
Production:
http://stealingyourinfo.xyz

## Team Members
- Tiago Sabbioni (Project Manager and Backend Developer)
- William Goodale (Database Manager)
- Jonathan Denker (Frontend Developer)
- Aryan Shah (Backend Developer)

## Features
- User registration
- User login and JWT Authentication
- Create contacts
- View contacts
- Search contacts
- Update contacts
- Delete contacts
- Protected API endpoints
- Secure database access using prepared statements

## Technologies Used
### Backend
- PHP 8
- Slim Framework
- JWT Authentication
- Composer
### Database
- MySQL
### Deployment
- Apache2
- Ubuntu
- DigitalOcean

## Architecture
The application follows a layered architecture that separates responsibilities between components:
### Controllers
The controllers handle incoming HTTP requests, validate user input, call the appropriate Model functions, and return HTTP responses.
### Models
The models are responsible for interacting with the database. They contain SQL queries and database-related operations for users and contacts.
### Routes
The routes define the API's endpoints and map HTTP request methods to controller methods, adding middleware to it.
### Middleware
The middleware processes requests before they reach the controllers. This application uses a JWT Middleware to authenticate users and protect secured endpoints.
### Config
Configuration files manage shared resources such as the database connection and environment settings.
### Public
The public directory contains the application's entry point (`index.php`), where routes and middleware are registered and the Slim application is started.


## Project Structure
```
api/
├── public/
├── src/
│   ├── Controllers/
│   ├── Middleware/
│   ├── Models/
│   ├── Routes/
│   └── Config/
├── vendor/
└── composer.json
```

## API Endpoints
### Authentication
| Method | Endpoint | Description |
|---------|---------|-------------|
| POST | /api/login | Login user |
| POST | /api/register | Register user |

### Contacts
| Method | Endpoint | Description |
|---------|---------|-------------|
| GET | /api/contacts | Get all contacts |
| GET | /api/contacts?search=value | Search contacts |
| POST | /api/contacts | Create a contact |
| PUT | /api/contacts/{id} | Update a contact |
| DELETE | /api/contacts/{id} | Delete a contact |

## Authentication
Protected endpoints require a JWT token.
Example:
```http
Authorization: Bearer <jwt_token>
```

The token is returned by the login endpoint.

## Installation
### 1. Clone the Repository
```Bash
git clone https://github.com/small-project-cop4331c/api.git
cd api
```
### 2. Install Dependencies
```Bash
composer install
```
### 3. Configure Environment Variables
Create a .env file:
```env
DB_HOST=localhost
DB_NAME=COP4331
DB_USER=username
DB_PASS=password
JWT_SECRET=your_secret_key
```
### 4. Start the Backend
```Bash
php -S localhost:8000 -t public
```

## Database Schema
### Users
| Column | Type |
|--------|------|
| id | INT |
| first_name | VARCHAR(50) |
| last_name | VARCHAR(50) |
| email_address | VARCHAR(50) |
| password | VARCHAR(60) |
### Contacts
| Column | Type |
|--------|------|
| id | INT |
| first_name | VARCHAR(50) |
| last_name | VARCHAR(50) |
| phone_number | VARCHAR(25) |
| email_address | VARCHAR(50) |
| date_created | DATE |
| user_id | INT (FK → Users.id) |

## API Documentation
The application's Swagger documentation is available in:
```md
docs/api.yaml
```

## License
This project was developed for educational purposes as part of COP 4331 at the University of Central Florida.