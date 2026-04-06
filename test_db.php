<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=banque', 'root', '');
    echo "Connected OK\n";
    echo "MySQL version: " . $pdo->query('SELECT VERSION()')->fetchColumn() . "\n";
}
catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
