<?php

namespace App\Config;

use mysqli;

class Database {
    private static $connection = null;

    public static function getConnection() {
        if(self::$connection === null) {

            //Connects to the database using the credentials in the .env file
            self::$connection = new mysqli(
                $_ENV["DB_HOST"],
                $_ENV["DB_USER"],
                $_ENV["DB_PASS"],
                $_ENV["DB_NAME"]
            );

            // Checks if the connection was successful
            if(self::$connection->connect_error) {
                die("Connection failed: " . self::$connection->connect_error);
            }

        }

        return self::$connection;
    }
}