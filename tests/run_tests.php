<?php

$testFiles = [
    __DIR__ . '/test_integration.php',
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

if (function_exists('runIntegrationTests')) {
    runIntegrationTests();
} else {
    echo "runIntegrationTests function not found.\n";
}

if (function_exists('runSystemTests')) {
    runSystemTests();
} else {
    echo "runSystemTests function not found.\n";
}

if (function_exists('runFuzzTests')) {
    runFuzzTests();
} else {
    echo "runFuzzTests function not found.\n";
}
