<?php
// Fetch database credentials from environment variables
$host = getenv('DB_HOST');
$port = getenv('DB_PORT');
$dbname = getenv('DB_NAME');
$user = getenv('DB_USER');
$password = getenv('DB_PASSWORD');

// Set the DSN (Data Source Name) for PostgreSQL
$dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

try {
    // Establish a connection to the database
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // If the connection is successful
    echo "Connected to the Supabase database successfully!";
} catch (PDOException $e) {
    // If the connection fails
    echo "Connection failed: " . $e->getMessage();
}
?>
