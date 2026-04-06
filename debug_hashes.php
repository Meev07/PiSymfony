<?php
try {
    $c = new PDO('mysql:host=127.0.0.1;dbname=esprit_wallet', 'root', '');
    $s = $c->query("SELECT email, password_hash FROM users");
    while($r = $s->fetch(PDO::FETCH_ASSOC)) {
        echo "Email: " . $r['email'] . " | Hash: " . substr($r['password_hash'], 0, 15) . "...\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
