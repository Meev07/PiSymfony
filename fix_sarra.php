<?php
use App\Kernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Dotenv\Dotenv;

require __DIR__.'/vendor/autoload.php';

(new Dotenv())->bootEnv(__DIR__.'/.env');

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();

$container = $kernel->getContainer();
$hasher = $container->get('security.user_password_hasher');

$email = 'sarra@esprit.tn';
$plainPassword = 'sarra123';

try {
    $c = new PDO('mysql:host=127.0.0.1;dbname=esprit_wallet', 'root', '');
    
    // Find user
    $s = $c->prepare("SELECT * FROM users WHERE email = ?");
    $s->execute([$email]);
    $user = $s->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Enforce ROLE_USER and hash password
        // We need a User entity instance for the hasher
        $userEntity = new \App\Entity\User();
        $hashedPassword = $hasher->hashPassword($userEntity, $plainPassword);

        $upd = $c->prepare("UPDATE users SET password_hash = ?, role = 'USER' WHERE email = ?");
        $upd->execute([$hashedPassword, $email]);

        echo "SUCCESS: Sarra's password has been hashed and role set to USER.\n";
    } else {
        echo "ERROR: User sarra@esprit.tn not found.\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
