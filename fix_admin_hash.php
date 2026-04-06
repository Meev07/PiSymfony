<?php
require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;

$email = 'tasnimouertani516@gmail.com';

try {
    $c = new PDO('mysql:host=127.0.0.1;dbname=esprit_wallet', 'root', '');
    $s = $c->prepare("SELECT password_hash FROM users WHERE email = ?");
    $s->execute([$email]);
    $u = $s->fetch(PDO::FETCH_ASSOC);

    if (!$u) {
        die("ERROR: User $email not found.\n");
    }

    $currentValue = $u['password_hash'];

    // If it's already a hash (starts with $2y$ or $argon2id$), don't double-hash
    if (str_starts_with($currentValue, '$2y$') || str_starts_with($currentValue, '$argon2id$')) {
        echo "ALREADY HASHED: User $email already has a hashed password.\n";
    } else {
        // Hash the plain-text password
        $factory = new PasswordHasherFactory([
            'common' => ['algorithm' => 'auto'],
        ]);
        $hasher = $factory->getPasswordHasher('common');
        $hashedPassword = $hasher->hash($currentValue);

        echo "Targeting user: $email\n";
        echo "Plain Text: $currentValue\n";
        echo "New Hash: " . substr($hashedPassword, 0, 20) . "...\n";

        $update = $c->prepare("UPDATE users SET password_hash = ?, role = 'ADMIN' WHERE email = ?");
        $update->execute([$hashedPassword, $email]);

        if ($update->rowCount() > 0) {
            echo "SUCCESS: User $email has been updated with a hashed password and ROLE_ADMIN.\n";
        } else {
            echo "WARNING: No rows were updated.\n";
        }
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
