<?php
try {
    $c = new PDO('mysql:host=127.0.0.1;dbname=esprit_wallet', 'root', '');
    $s = $c->query("DESCRIBE users");
    $allCols = $s->fetchAll(PDO::FETCH_ASSOC);
    $targetCols = ['otp_code', 'otp_expires_at'];
    $found = [];
    foreach($allCols as $col) {
        if (in_array($col['Field'], $targetCols)) {
            $found[] = $col['Field'];
        }
    }
    echo "Found columns in users table: " . implode(", ", $found) . "\n";
    
    // Also check if any otp_codes table exists
    $s = $c->query("SHOW TABLES LIKE 'otp_codes'");
    if ($s->fetch()) {
        echo "Table 'otp_codes' exists.\n";
    } else {
        echo "Table 'otp_codes' does not exist.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
