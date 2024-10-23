<?php
require '../config/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../pages/signup.php?signup_error=wrong_email_format");
        exit();
    }

    $name = htmlspecialchars(trim($_POST['name']));
    if (strlen($name) < 2) {
        header("Location: ../pages/signup.php?signup_error=wrong_name_format");
        exit();
    }

    $password = $_POST['password'];
    if (strlen($password) < 8) {
        header("Location: ../pages/signup.php?signup_error=wrong_password_length");
        exit();
    }

    $confirm_password = $_POST['confirm_password'];
    if ($password !== $confirm_password) {
        header("Location: ../pages/signup.php?signup_error=wrong_password_match");
        exit();
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $conn = pg_connect("host=" . DB_HOST . " dbname=" . DB_NAME . " user=" . DB_USER . " password=" . DB_PASS);

    if (!$conn) {
        header("Location: ../pages/signup.php?signup_error=generic");
        exit();
    }

    $email_check_query = "SELECT 1 FROM users WHERE email = $1";
    $email_check_result = pg_query_params($conn, $email_check_query, array($email));

    if (pg_num_rows($email_check_result) > 0) {
        header("Location: ../pages/signup.php?signup_error=email_fail");
        exit();
    }

    $query = "INSERT INTO users (name, email, password_hash) VALUES ($1, $2, $3)";
    $result = pg_query_params($conn, $query, array($name, $email, $password_hash));

    if ($result) {
        header("Location: ../pages/login.php?signup=success");
        exit();
    } else {
        header("Location: ../pages/signup.php?signup_error=generic");
        exit();
    }

    pg_close($conn);
} else {
    die("Invalid request method.");
}
