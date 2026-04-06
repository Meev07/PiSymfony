<?php
try {
    $c = new PDO('mysql:host=127.0.0.1;dbname=esprit_wallet', 'root', '');
    echo "--- Users Table Structure ---\n";
    $s = $c->query('DESCRIBE users');
    while($r = $s->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf("%-15s | %-15s\n", $r['Field'], $r['Type']);
    }
    echo "\n--- Sample Users ---\n";
    $s = $c->query('SELECT email, role FROM users LIMIT 10');
    if (!$s) {
        // Try 'roles' if 'role' fails
        $s = $c->query('SELECT email, roles FROM users LIMIT 10');
    }
    while($r = $s->fetch(PDO::FETCH_ASSOC)) {
        print_r($r);
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
