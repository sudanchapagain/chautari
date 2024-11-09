<?php
ob_start();

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header("Location: login");
    exit();
}

include __DIR__ . '/../components/header.php';
$db = getDbConnection();

$query = "SELECT user_id, username, profile_picture, user_phone, email FROM users WHERE user_id = $1";
$params = [$user_id];
$result = pg_query_params($db, $query, $params);

if (!$result || pg_num_rows($result) === 0) {
    header("Location: /settings");
    exit();
}

$user = pg_fetch_assoc($result);

function escape($value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $phone = trim($_POST['user_phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $errors = [];

    if (isset($_POST['delete_account']) && $_POST['delete_account'] === 'true') {
        $deleted_user_query = "SELECT user_id FROM users WHERE username = 'deleted_user'";
        $deleted_user_result = pg_query($db, $deleted_user_query);

        if ($deleted_user_result && pg_num_rows($deleted_user_result) > 0) {
            $deleted_user_id = pg_fetch_result($deleted_user_result, 0, 'user_id');

            $updateRelatedRecordsQuery = "UPDATE user_event_attendance SET user_id = $1 WHERE user_id = $2";
            $updateRelatedRecordsResult = pg_query_params($db, $updateRelatedRecordsQuery, [$deleted_user_id, $user['user_id']]);

            if ($updateRelatedRecordsResult) {
                $deleteUserQuery = "DELETE FROM users WHERE user_id = $1";
                $deleteUserResult = pg_query_params($db, $deleteUserQuery, [$user['user_id']]);

                if ($deleteUserResult) {
                    session_destroy();
                    header("Location: /login");
                    exit();
                } else {
                    $errors[] = "Failed to delete user account.";
                }
            } else {
                $errors[] = "Failed to update related records.";
            }
        } else {
            $errors[] = "Placeholder deleted user not found.";
        }
    } else {
        if (!$username) {
            $errors[] = "Username is required.";
        }
        if ($phone && !preg_match('/^\+?\d{10,15}$/', $phone)) {
            $errors[] = "Phone number is invalid.";
        }
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email address.";
        }

        if (empty($errors)) {
            $updateQuery = "UPDATE users SET username = $1, user_phone = $2, email = $3, profile_picture = NULL";
            $params = [$username, $phone, $email];

            if (!empty($password)) {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $updateQuery .= ", password_hash = $" . (count($params) + 1);
                $params[] = $password_hash;
            }

            $updateQuery .= " WHERE user_id = $" . (count($params) + 1);
            $params[] = $user['user_id'];
            $updateResult = pg_query_params($db, $updateQuery, $params);

            if ($updateResult) {
                header("Location: /settings?update=success");
                exit();
            } else {
                header("Location: /settings?update=failed");
            }
        }
    }
}
?>

<head>
    <title>Settings - Chautari</title>
    <link rel="stylesheet" href="../assets/css/default.css">
    <link rel="stylesheet" href="../assets/css/index.css">
    <link rel="stylesheet" href="../assets/css/sign.css">
</head>

<body>
    <main style="max-width: 550px; margin: 0 auto;">
        <h1 style="font-size: 2.3rem; letter-spacing: normal;  margin-top: 50px;">Settings</h1>

        <?php if (isset($_GET['update']) && $_GET['update'] === 'success'): ?>
            <p class="signupSuccessMessage">Profile updated successfully!</p>
        <?php endif; ?>

        <?php if (isset($_GET['update']) && $_GET['update'] === 'failed'): ?>
            <p class="signupFailMessage">Failed to update profile. Please try again.</p>
        <?php endif; ?>

        <form method="POST" action="/settings" style="display:block; border: 1px solid lightgrey; border-radius: 8px; padding: 30px; margin-bottom: 4rem; background-color: #fafafa;">
            <h2>Personal</h2>
            <br><br>
            <label class="subtitle-profile-detail" for="profile_picture">Profile Picture</label><br><br>
            <input type="file" class="" style="width: 50%; box-sizing: border-box;" id="profile_picture" name="profile_picture" accept="image/*" />
            <br><br>

            <label for="username" class="subtitle-profile-detail">Username</label><br>
            <input type="text" class="data-item-profile-detail <?= !empty($errors) && !$username ? 'border border-danger' : ''; ?>"
                id="username" name="username" style="width: 100%; box-sizing: border-box;" value="<?= escape($user['username']) ?>" required />
            <br><br>

            <label for="user_phone" class="subtitle-profile-detail">Phone Number</label><br>
            <input type="text" class="data-item-profile-detail <?= !empty($errors) && !$phone ? 'border border-danger' : ''; ?>"
                id="user_phone" name="user_phone" style="width: 100%; box-sizing: border-box;" value="<?= escape($user['user_phone']) ?>" />
            <br><br>

            <label for="email" class="subtitle-profile-detail">Email</label><br>
            <input type="email" class="data-item-profile-detail <?= !empty($errors) && !$email ? 'border border-danger' : ''; ?>"
                id="email" name="email" value="<?= escape($user['email']) ?>" required style="width: 100%; box-sizing: border-box;" />
            <br><br>

            <label for="password" class="subtitle-profile-detail">Password</label><br>
            <input type="password" class="data-item-profile-detail" id="password" name="password" style="width: 100%; box-sizing: border-box;" />
            <br><br>

            <button type="submit" class="edit-button-profile">Save Changes</button>

            <form method="POST" action="/settings">
                <button type="submit" name="delete_account" value="true" class="delete-button-profile">Delete Account</button>
            </form>
        </form>

        <style>
            .subtitle-profile-detail {
                font-size: 1rem;
                margin-top: 1rem;
                font-weight: 700;
                text-transform: uppercase;
                color: grey;
            }

            .data-item-profile-detail {
                font-size: 1rem;
                font-weight: 700;
                color: #333;
                text-transform: initial;
                border: 1px solid lightgrey;
                border-radius: 4px;
                padding: 10px;
                margin: 5px 0;
                background-color: #f0f0f0;
            }

            .edit-button-profile {
                display: block;
                width: 100%;
                padding: 10px;
                text-align: center;
                background-color: #ff4a22;
                color: white;
                border: none;
                border-radius: 6px;
                margin-top: 1rem;
                text-decoration: none;
                font-weight: 700;
            }

            .delete-button-profile {
                display: block;
                width: 100%;
                padding: 10px;
                text-align: center;
                color: red;
                border: none;
                border-radius: 6px;
                margin-top: 1rem;
                text-decoration: none;
                font-weight: 700;
            }
        </style>
    </main>
</body>

<?php include __DIR__ . '/../components/footer.php'; ?>