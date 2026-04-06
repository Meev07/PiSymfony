<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=esprit_wallet', 'root', '');
    $stmt = $pdo->query('SHOW COLUMNS FROM users');
    $output = "--- USERS TABLE ---\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $output .= "Field: " . $row['Field'] . " | Type: " . $row['Type'] . "\n";
    }
    file_put_contents('db_schema.txt', $output);
    echo "Schema written to db_schema.txt\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
