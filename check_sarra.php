<?php
try {
    $c = new PDO('mysql:host=127.0.0.1;dbname=esprit_wallet', 'root', '');
    $s = $c->prepare("SELECT * FROM users WHERE email = ?");
    $s->execute(['sarra@esprit.tn']);
    $user = $s->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        print_r($user);
    } else {
        echo "USER NOT FOUND\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
