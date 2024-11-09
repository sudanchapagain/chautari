<?php

function runFuzzTests()
{
    $fuzzTestsPassed = 0;
    $fuzzTestsFailed = 0;

    if (fuzzTestLogin()) {
        $fuzzTestsPassed++;
    } else {
        $fuzzTestsFailed++;
    }

    echo "Fuzz Tests: Passed $fuzzTestsPassed, Failed $fuzzTestsFailed\n";
}


function fuzzTestLogin()
{
    $fuzzInputs = [
        ["email" => "", "password" => "Password123"],
        ["email" => "test@example.com", "password" => ""],
        ["email" => "invalid-email", "password" => "12345678"],
        ["email" => str_repeat("a", 256) . "@example.com", "password" => "Password123"],
        ["email" => "test@example.com", "password" => str_repeat("b", 100)],
    ];

    foreach ($fuzzInputs as $input) {
        $_POST['email'] = $input['email'];
        $_POST['password'] = $input['password'];

        ob_start();
        require '../path/to/login_handler.php';
        $output = ob_get_clean();

        if (empty($input['email'])) {
            assert(strpos($output, "Invalid email format") !== false);
        } elseif (empty($input['password'])) {
            assert(strpos($output, "Password must be at least 8 characters long") !== false);
        } elseif (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
            assert(strpos($output, "Invalid email format") !== false);
        } else {
            assert(true);
        }
    }
}
