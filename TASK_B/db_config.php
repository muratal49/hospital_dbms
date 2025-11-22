<?php
function getConnection()
{
    $servername = "localhost";
    $username = "mal";
    $password = "....";
    $dbname = "mal_1";
    // Load local .env if present (keeps secrets out of repo)
    $envPath = __DIR__ . '/.env.example';
    if (file_exists($envPath)) {
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            if (strpos($line, '=') === false) continue;
            list($k, $v) = explode('=', $line, 2);
            $k = trim($k);
            $v = trim($v, " \t\n\r\0\x0B\"'");
            putenv("$k=$v");
            $_ENV[$k] = $v;
            $_SERVER[$k] = $v;
        }
    }

    $servername = getenv('DB_HOST') ?: 'localhost';
    $username = getenv('DB_USER');
    $password = getenv('DB_PASS');
    $dbname = getenv('DB_NAME');

    // Require credentials to be set via environment or TASK B/.env
    if (!$username || !$password || !$dbname) {
        die("Database credentials missing. Create TASK B/.env with DB_USER, DB_PASS, and DB_NAME or set the environment variables.");
    }

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

?>