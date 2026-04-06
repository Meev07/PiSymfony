<?php
try {
    $c = new PDO('mysql:host=127.0.0.1;dbname=esprit_wallet', 'root', '');
    echo "--- Database Tables ---\n";
    $s = $c->query('SHOW TABLES');
    while($r = $s->fetch(PDO::FETCH_NUM)) {
        echo $r[0] . "\n";
        echo "  Structure:\n";
        $ss = $c->query("DESCRIBE " . $r[0]);
        while($rr = $ss->fetch(PDO::FETCH_ASSOC)) {
            echo sprintf("    %-15s | %-15s\n", $rr['Field'], $rr['Type']);
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
