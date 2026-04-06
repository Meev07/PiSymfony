<?php
try {
    $c = new PDO('mysql:host=127.0.0.1;dbname=esprit_wallet', 'root', '');
    
    echo "--- Users Table Columns ---\n";
    $s = $c->query('DESCRIBE users');
    while($r = $s->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf("[%s] (%s)\n", $r['Field'], $r['Type']);
    }

    echo "\n--- Data Inspection (First 3 Users) ---\n";
    // Get column names to be safe
    $cols = $c->query('SELECT * FROM users LIMIT 1')->fetch(PDO::FETCH_ASSOC);
    $colNames = array_keys($cols);
    
    $s = $c->query('SELECT * FROM users LIMIT 3');
    while($r = $s->fetch(PDO::FETCH_ASSOC)) {
        echo "Email: " . $r['email'] . "\n";
        foreach ($colNames as $col) {
            if (stripos($col, 'role') !== false) {
                echo "  $col: '" . $r[$col] . "' (Length: " . strlen($r[$col]) . ")\n";
            }
        }
        echo "------------------\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
