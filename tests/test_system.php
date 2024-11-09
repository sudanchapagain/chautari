<?php

function runSystemTests()
{
    $systemTestsPassed = 0;
    $systemTestsFailed = 0;

    if (testLoginSystem()) {
        $systemTestsPassed++;
    } else {
        $systemTestsFailed++;
    }

    echo "System Tests: Passed $systemTestsPassed, Failed $systemTestsFailed\n";
}

function testLoginSystem()
{
    $conn = pg_connect("host=localhost dbname=test_db user=test_user password=test_pass");

    $email = "test@example.com";
    $password = "Password123";
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    pg_query_params($conn, "INSERT INTO users (name, email, password_hash) VALUES ($1, $2, $3)", ["Test User", $email, $password_hash]);

    $_POST['email'] = $email;
    $_POST['password'] = $password;

    require '../path/to/login_handler.php';

    session_start();
    assert($_SESSION['email'] === $email);

    pg_query_params($conn, "DELETE FROM users WHERE email = $1", [$email]);
    pg_close($conn);
}

testLoginSystem();
