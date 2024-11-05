<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in - Chautari</title>
    <meta name="description" content="Struggling to discover fun and exciting events near you? Chautari brings the best local events tailored to you so you can join
            local happenings with ease.">
    <meta property="og:title" content="Chautari — Find out about events around you.">
    <link rel="icon" type="image/x-icon" href="../assets/images/favicon.ico" sizes="any">

    <link rel="stylesheet" href="../assets/css/default.css">
    <link rel="stylesheet" href="../assets/css/sign.css">
</head>

<body class="centerBody">

    <main class="formCenter">

        <?php if (isset($_GET['signup']) && $_GET['signup'] == 'success'): ?>
            <p class="signupSuccessMessage">Signup was successful! Please log in.</p>
        <?php endif; ?>

        <img class="smallImageInForm" src="../assets/images/logo-32.svg" alt="" srcset="">

        <h1>Log in</h1>

        <p class="snippetSignInfo">New to Chautari? <a href="./signup.php">Sign up</a></p>

        <div class="formbody">
            <form action="../handlers/login_handler.php" method="post">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required inputmode="email">
                <span id="emailError" class="error"></span>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required inputmode="password">
                <span id="passwordError" class="error"></span>

                <br>

                <button type="submit">Log in</button>
            </form>
        </div>
    </main>

</body>

</html>
