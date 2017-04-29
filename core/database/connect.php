<?php
/* Connection parameters */
$database_connection = new StdClass();

$database_connection->server = 'localhost';
$database_connection->username = 'root';
$database_connection->password = 'admin';
$database_connection->name = 'valve-servers-list';

/* Establishing the connection */
$database = new mysqli($database_connection->server, $database_connection->username, $database_connection->password, $database_connection->name);

/* Debugging */
if($database->connect_error) {
    die('The connection to the database failed ! Please edit the "core/database/connect.php" file and make sure your database connection details are correct!');
}
