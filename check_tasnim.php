<?php
$email = 'tasnimouertani516@gmail.com';
try {
    $c = new PDO('mysql:host=127.0.0.1;dbname=esprit_wallet', 'root', '');
    $s = $c->prepare("SELECT * FROM users WHERE email = ?");
    $s->execute([$email]);
    $u = $s->fetch(PDO::FETCH_ASSOC);
    if ($u) {
        echo "USER FOUND!\n";
        echo "Email: " . $u['email'] . "\n";
        echo "Password Hash in DB: " . $u['password_hash'] . "\n";
        echo "Role in DB: " . $u['role'] . "\n";
        
        // Let's also check if it looks like a hash
        if (str_starts_with($u['password_hash'], '$2y$') || str_starts_with($u['password_hash'], '$argon2id$')) {
            echo "Format: LIKELY HASHED\n";
        } else {
            echo "Format: LIKELY PLAIN TEXT\n";
        }
    } else {
        echo "USER NOT FOUND: $email\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
