<?php
function getConnection()
{
    $servername = "localhost";
    $username = "ezhupa";
    $password = "...";
    $dbname = "ezhupa_1";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

?>