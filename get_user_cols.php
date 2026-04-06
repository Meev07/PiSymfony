<?php
try {
    $c = new PDO('mysql:host=127.0.0.1;dbname=esprit_wallet', 'root', '');
    $s = $c->query("DESCRIBE users");
    $cols = $s->fetchAll(PDO::FETCH_ASSOC);
    foreach($cols as $col) {
        echo $col['Field'] . " | " . $col['Type'] . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
