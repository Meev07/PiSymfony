<?php
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('src'));
foreach ($it as $file) {
    if ($file->isDir()) continue;
    echo $file->getPathname() . "\n";
}
