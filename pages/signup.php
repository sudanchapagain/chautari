<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link rel="stylesheet" href="../assets/css/sign.css">
    <script src="../assets/js/password_match.js"></script>
</head>
<body>
<?php include '../includes/header.php'; ?>

<main>
    <h1>Signup</h1>
    <form action="../handlers/signup_handler.php" method="post" onsubmit="return validateForm()">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <span id="passNoMatch"></span>
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>

        <button type="submit">Signup</button>
    </form>
</main>

<?php include '../includes/footer.php'; ?>
</body>
</html>
