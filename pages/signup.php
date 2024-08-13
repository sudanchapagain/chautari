<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up - Chautari</title>
    <meta name="description" content="Struggling to discover fun and exciting events near you? Chautari brings the best local events tailored to you so you can join
            local happenings with ease.">
    <meta property="og:title" content="Chautari — Find out about events around you.">
    <link rel="icon" type="image/x-icon" href="../assets/images/favicon.ico" sizes="any">

    <link rel="stylesheet" href="../assets/css/default.css">
    <link rel="stylesheet" href="../assets/css/sign.css">
    <script src="../assets/js/password_match.js"></script>
</head>
<body class="centerBody">

<main class="formCenter">
    <img class="smallImageInForm" src="../assets/images/logo-32.svg" alt="" srcset="">
    
    <h1>Sign up</h1>
    
    <p class="snippetSignInfo">Already have an account? <a href="./login.php">Log in</a></p>
    
    <div class="formbody">
        <form action="../handlers/signup_handler.php" method="post" onsubmit="return validateForm()">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" required>
            
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
            
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            
           
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
             
            <span id="passNoMatch"></span>
            <style>
                #passNoMatch {
                    color: #d42f2f;
                }
            </style>
            
            <button type="submit">Sign up</button>
        </form>
    </div>
</main>

</body>
</html>
