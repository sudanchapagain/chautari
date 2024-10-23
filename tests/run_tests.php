<?php
function includeAllFiles($dir) {
    if (!is_dir($dir)) {
        echo "Directory $dir does not exist.\n";
        return;
    }
    foreach (glob("$dir/*.php") as $file) {
        include_once $file;
    }
}

$directories = [
    __DIR__ . '/integration',
    __DIR__ . '/system',
    __DIR__ . '/fuzz',
];

foreach ($directories as $directory) {
    includeAllFiles($directory);
}

if (function_exists('runIntegrationTests')) {
    runIntegrationTests();
}

if (function_exists('runSystemTests')) {
    runSystemTests();
}

if (function_exists('runFuzzTests')) {
    runFuzzTests();
}
