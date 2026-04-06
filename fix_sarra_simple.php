<?php
$email = 'sarra@esprit.tn';
$plainPassword = 'sarra123';
$hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

try {
    $c = new PDO('mysql:host=127.0.0.1;dbname=esprit_wallet', 'root', '');
    
    // Find user
    $s = $c->prepare("SELECT * FROM users WHERE email = ?");
    $s->execute([$email]);
    $user = $s->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $upd = $c->prepare("UPDATE users SET password_hash = ?, role = 'USER' WHERE email = ?");
        $upd->execute([$hashedPassword, $email]);

        echo "SUCCESS: Sarra's password has been hashed and role set to USER.\n";
    } else {
        echo "ERROR: User sarra@esprit.tn not found.\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
