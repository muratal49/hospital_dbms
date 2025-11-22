<?php
function getConnection()
{
    $servername = "localhost";
    $username = "mal";
    $password = "6JJbmVjH";
    $dbname = "mal_1";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

?>