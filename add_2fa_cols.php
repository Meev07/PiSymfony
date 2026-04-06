<?php
try {
    $c = new PDO('mysql:host=127.0.0.1;dbname=esprit_wallet', 'root', '');
    $c->exec("ALTER TABLE users ADD otp_code VARCHAR(6) DEFAULT NULL, ADD otp_expires_at DATETIME DEFAULT NULL");
    echo "2FA COLUMNS ADDED SUCCESSFULLY\n";
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "2FA COLUMNS ALREADY EXIST\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
