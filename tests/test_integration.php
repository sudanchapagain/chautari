<?php

function runIntegrationTests()
{
    $integrationTestsPassed = 0;
    $integrationTestsFailed = 0;

    if (testSignupIntegration()) {
        $integrationTestsPassed++;
    } else {
        $integrationTestsFailed++;
    }

    echo "Integration Tests: Passed $integrationTestsPassed, Failed $integrationTestsFailed\n";
}

function testSignupIntegration()
{
    $conn = pg_connect("host=localhost dbname=test_db user=test_user password=test_pass");
    pg_query_params($conn, "DELETE FROM users WHERE email = $1", ['test@example.com']);

    $_POST['email'] = "test@example.com";
    $_POST['name'] = "Test User";
    $_POST['password'] = "Password123";
    $_POST['confirm_password'] = "Password123";

    require '../path/to/signup_handler.php';

    $result = pg_query_params($conn, "SELECT * FROM users WHERE email = $1", ['test@example.com']);
    assert(pg_num_rows($result) === 1);

    pg_query_params($conn, "DELETE FROM users WHERE email = $1", ['test@example.com']);
    pg_close($conn);
}
