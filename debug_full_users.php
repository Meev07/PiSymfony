<?php
try {
    $c = new PDO('mysql:host=127.0.0.1;dbname=esprit_wallet', 'root', '');
    $s = $c->query("SELECT email, password_hash, role FROM users");
    $users = $s->fetchAll(PDO::FETCH_ASSOC);
    foreach ($users as $u) {
        printf("Email: %s | Role: %s | Hash Start: %s\n", 
            $u['email'], 
            $u['role'], 
            substr($u['password_hash'], 0, 20)
        );
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
