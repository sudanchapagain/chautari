<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../assets/sign.css">
</head>
<body>
<?php include '../includes/header.php'; ?>

<main>
    <h1>Login</h1>
    <form action="../handlers/login_handler.php" method="post">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Login</button>
    </form>
</main>

<?php include '../includes/footer.php'; ?>
</body>
</html>
