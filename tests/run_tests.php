<?php

$testFiles = [
    __DIR__ . '/test_system.php',
    __DIR__ . '/test_fuzz.php',
];

foreach ($testFiles as $file) {
    if (file_exists($file)) {
        include_once $file;
    } else {
        echo "Test file $file does not exist.\n";
    }
}

if (function_exists('runSystemTests')) {
    runSystemTests();
} else {
    echo "runSystemTests function not found.\n";
}

if (function_exists('runUnitTests')) {
    runUnitTests();
} else {
    echo "runUnitTests function not found.\n";
}
