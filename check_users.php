<?php
try {
    $c = new PDO('mysql:host=127.0.0.1;dbname=esprit_wallet', 'root', '');
    $s = $c->query("DESCRIBE users");
    echo "--- Users Structure ---\n";
    while($r = $s->fetch(PDO::FETCH_ASSOC)) {
        echo $r['Field'] . " (" . $r['Type'] . ")\n";
    }
    
    echo "\n--- Recent Users ---\n";
    $s = $c->query("SELECT id_user, email, password_hash, role FROM users ORDER BY id_user DESC LIMIT 3");
    while($r = $s->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: " . $r['id_user'] . " | Email: " . $r['email'] . " | Role: " . $r['role'] . "\n";
        echo "Password: " . substr($r['password_hash'], 0, 10) . "...\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
