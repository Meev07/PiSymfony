<?php

require_once __DIR__.'/vendor/autoload.php';

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;
use App\Entity\User;

$dotenv = new Dotenv();
$dotenv->loadEnv(__DIR__.'/.env');

$kernel = new Kernel($_SERVER['APP_ENV'] ?? 'dev', (bool) ($_SERVER['APP_DEBUG'] ?? true));
$kernel->boot();
$container = $kernel->getContainer();
$entityManager = $container->get('doctrine')->getManager();

$user = $entityManager->getRepository(User::class)->findOneBy(['email' => 'mouhib.sahli@gmail.cim']);

if ($user) {
    $user->setRoles(['ROLE_ADMIN']);
    $entityManager->flush();
    echo "User role updated to ROLE_ADMIN successfully.\n";
} else {
    echo "User not found.\n";
}
