<?php

function includeAllFiles($dir) {
    foreach (glob("$dir/*.php") as $file) {
        include_once $file;
    }
}

$directories = [
    __DIR__ . '/unit',
    __DIR__ . '/integration',
    __DIR__ . '/system',
];

foreach ($directories as $directory) {
    includeAllFiles($directory);
}

if (function_exists('runTests')) {
    runTests();
} else {
    echo "No tests found or runTests function is not defined.\n";
}

?>

