<?php
try {
    $c = new PDO('mysql:host=127.0.0.1;dbname=esprit_wallet', 'root', '');
    $c->exec("ALTER TABLE users ADD reset_token VARCHAR(255) DEFAULT NULL, ADD reset_token_expires_at DATETIME DEFAULT NULL");
    echo "COLUMNS ADDED SUCCESSFULLY\n";
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "COLUMNS ALREADY EXIST\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
