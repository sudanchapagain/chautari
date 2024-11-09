<?php

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: /login");
    exit();
}

$tab = $_GET['tab'] ?? 'dashboard';

if (!(userHasPermission($user_id, 'organizer') || userHasPermission($user_id, 'admin'))) {
    header("Location: /explore");
    exit();
}

function userHasPermission($user_id, $permission)
{
    $db = getDbConnection();

    if ($permission === 'admin') {
        $query = "SELECT is_admin FROM users WHERE user_id = $1";
        $result = pg_query_params($db, $query, [$user_id]);

        if ($result) {
            $user = pg_fetch_assoc($result);
            return $user['is_admin'] === 't';
        } else {
            return false;
        }
    }

    if ($permission === 'organizer') {
        $query = "SELECT is_organizer FROM users WHERE user_id = $1";
        $result = pg_query_params($db, $query, [$user_id]);

        if ($result) {
            $user = pg_fetch_assoc($result);
            return $user['is_organizer'] === 't';
        } else {
            return false;
        }

    }
    return false;
}

function isActive($currentTab, $tabName)
{
    return $currentTab === $tabName ? 'active' : '';
}

function safe_htmlspecialchars($value)
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Struggling to discover fun and exciting events near you? Chautari brings the best local events tailored to you so you can join
            local happenings with ease.">
    <meta property="og:title" content="Chautari — Find out about events around you.">
    <link rel="icon" type="image/x-icon" href="../assets/images/favicon.ico" sizes="any">

    <title>Dashboard - Chautari</title>
    <link rel="stylesheet" href="/assets/css/dashboard.css">
    <link rel="stylesheet" href="/assets/css/default.css">
    <link rel="stylesheet" href="/assets/css/index.css">
</head>

<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <div class="logo">
                <a href="/" style="text-decoration: none; color: #000000">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 256 256">
                        <path fill="currentColor" d="M128 187.85a72 72 0 0 0 8 4.62V232a8 8 0 0 1-16 0v-39.53a72 72 0 0 0 8-4.62m70.1-125.26a76 76 0 0 0-140.2 0A71.71 71.71 0 0 0 16 127.8C15.9 166 48 199 86.14 200a72.2 72.2 0 0 0 33.86-7.53v-35.53l-43.58-21.78a8 8 0 1 1 7.16-14.32L120 139.06V88a8 8 0 0 1 16 0v27.06l36.42-18.22a8 8 0 1 1 7.16 14.32L136 132.94v59.53a72.2 72.2 0 0 0 32 7.53h1.82c38.18-1 70.29-34 70.18-72.2a71.71 71.71 0 0 0-41.9-65.21" />
                    </svg><span
                        style="font-weight: 700;">Chautari</span>
                </a>
            </div>

            <hr>

            <ul class="nav-items">
                <li class="<?= isActive($tab, 'dashboard') ?>"><a href="/dashboard?tab=dashboard"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" style="vertical-align: middle; margin-bottom:5px; margin-right: 0.5rem;" viewBox="0 0 20 20">
                            <g fill="currentColor">
                                <g opacity=".2">
                                    <path fill-rule="evenodd" d="M8 4.351c0-.47.414-.851.926-.851h6.148c.512 0 .926.381.926.851V7.65c0 .47-.414.851-.926.851H8.926C8.414 8.5 8 8.119 8 7.649V4.35Z" clip-rule="evenodd" />
                                    <path d="M6.462 19h10.576c.532 0 .962-.448.962-1V6c0-.552-.43-1-.962-1H6.462C5.93 5 5.5 5.448 5.5 6v12c0 .552.43 1 .962 1Z" />
                                </g>
                                <path fill-rule="evenodd" d="M6.175 2.5a.5.5 0 0 1 .5-.5h6.643a.5.5 0 0 1 .5.5v3.875a.5.5 0 0 1-.5.5H6.675a.5.5 0 0 1-.5-.5V2.5Zm1 .5v2.875h5.643V3H7.175Z" clip-rule="evenodd" />
                                <path d="M4.5 5v12h11V5h-2V4h2a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1h-11a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1h2v1h-2Z" />
                            </g>
                        </svg>Dashboard</a></li>

                <li class="<?= isActive($tab, 'bookings') ?>"><a href="/dashboard?tab=bookings"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" style="vertical-align: middle; margin-bottom:3px; middle;margin-right: 0.5rem;" viewBox="0 0 20 20">
                            <g fill="currentColor">
                                <path d="m11 5.79l7.314-1.27a1.5 1.5 0 0 1 .242-.02c.801 0 1.444.664 1.444 1.475v9.786c0 .72-.511 1.34-1.213 1.456l-7.705 1.276a.499.499 0 0 1-.18-.002l-7.647-1.267A1.5 1.5 0 0 1 2 15.744V6.011a1.5 1.5 0 0 1 1.756-1.478L11 5.79Z" opacity=".2" />
                                <path fill-rule="evenodd" d="M10.08 4.304L2.244 3.019A1.5 1.5 0 0 0 .5 4.5v9.738a1.5 1.5 0 0 0 1.268 1.482l8.155 1.275a.5.5 0 0 0 .577-.494V4.797a.5.5 0 0 0-.42-.493Zm-8-.298L9.5 5.222v10.694L1.923 14.73a.5.5 0 0 1-.423-.493V4.5a.5.5 0 0 1 .58-.494Z" clip-rule="evenodd" />
                                <path fill-rule="evenodd" d="M18 3a1.5 1.5 0 0 0-.243.02L9.92 4.303a.5.5 0 0 0-.419.493V16.5a.5.5 0 0 0 .577.494l8.155-1.275a1.5 1.5 0 0 0 1.268-1.482V4.5A1.5 1.5 0 0 0 18 3Zm.077 11.73L10.5 15.916V5.222l7.42-1.216a.501.501 0 0 1 .58.494v9.737a.5.5 0 0 1-.423.493Z" clip-rule="evenodd" />
                            </g>
                        </svg>Bookings</a></li>

                <li class="<?= isActive($tab, 'sales') ?>"><a href="/dashboard?tab=sales"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" style="vertical-align: middle; margin-bottom:5px; margin-right: 0.5rem;" viewBox="0 0 20 20">
                            <g fill="currentColor">
                                <path fill-rule="evenodd" d="M5.219 2.75H4.2a.75.75 0 0 1 0-1.5h1.603a.75.75 0 0 1 .727.566l1.502 5.937a1.998 1.998 0 0 1 .974-.253h7.989a2.012 2.012 0 0 1 1.955 2.468l-.783 3.461A2.009 2.009 0 0 1 16.21 15H9.79a2.008 2.008 0 0 1-1.956-1.57L7.05 9.967a2.058 2.058 0 0 1-.027-.145a.754.754 0 0 1-.05-.14L5.219 2.75ZM9.25 18.5a1.75 1.75 0 1 0 0-3.5a1.75 1.75 0 0 0 0 3.5Zm7 0a1.75 1.75 0 1 0 0-3.5a1.75 1.75 0 0 0 0 3.5Z" clip-rule="evenodd" opacity=".2" />
                                <path d="M3.712 2.5H2.5a.5.5 0 0 1 0-1h1.603a.5.5 0 0 1 .485.379l1.897 7.6a.5.5 0 0 1-.97.242L3.712 2.5Z" />
                                <path fill-rule="evenodd" d="M15.495 7.5h-7.99c-.15 0-.3.017-.447.05A2.02 2.02 0 0 0 5.55 9.969l.783 3.461A2.008 2.008 0 0 0 8.29 15h6.422a2.01 2.01 0 0 0 1.956-1.57l.783-3.462A2.012 2.012 0 0 0 15.495 7.5ZM7.283 8.525a.992.992 0 0 1 .223-.025h7.989a1.013 1.013 0 0 1 .98 1.247l-.784 3.462a1.009 1.009 0 0 1-.98.791H8.29c-.468 0-.875-.328-.98-.791l-.783-3.462a1.02 1.02 0 0 1 .757-1.222Z" clip-rule="evenodd" />
                                <path d="M17 16.75a1.75 1.75 0 1 1-3.5 0a1.75 1.75 0 0 1 3.5 0Zm-7 0a1.75 1.75 0 1 1-3.5 0a1.75 1.75 0 0 1 3.5 0Z" />
                            </g>
                        </svg>Sales</a></li>

                <?php if (userHasPermission($user_id, 'admin')): ?>
                    <li class="<?= isActive($tab, 'moderation') ?>"><a href="/dashboard?tab=moderation"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" style="vertical-align: middle; margin-bottom:5px; margin-right: 0.5rem;" viewBox="0 0 20 20">
                                <g fill="currentColor">
                                    <path d="m8 4.97l.954-.396a4 4 0 0 1 2.908.058l1.482.613a4 4 0 0 0 2.693.13l.893-.271A1.604 1.604 0 0 1 19 6.638V10.7a3.22 3.22 0 0 1-1.66 2.817l-.734.407a4 4 0 0 1-3.88 0l-.453-.251a4 4 0 0 0-3.88 0l-.226.126c-.055.03-.11.056-.167.079V19a1 1 0 1 1-2 0V5a1 1 0 0 1 1-1c.81 0 1 .97 1 .97Z" opacity=".2" />
                                    <path fill-rule="evenodd" d="m6.804 2.632l-.637.264A3.507 3.507 0 0 0 4 6.137v4.386a1.46 1.46 0 0 0 2.167 1.276l.227-.126a4 4 0 0 1 3.88 0l.453.251a4 4 0 0 0 3.88 0l.734-.407A3.222 3.222 0 0 0 17 8.7V4.638a1.605 1.605 0 0 0-2.07-1.534l-.893.272a4 4 0 0 1-2.694-.13l-1.48-.614a4 4 0 0 0-3.059 0ZM5 6.137c0-1.014.611-1.929 1.549-2.317l.638-.264a3 3 0 0 1 2.293 0l1.481.613a5 5 0 0 0 3.367.163l.893-.271a.604.604 0 0 1 .779.577V8.7c0 .807-.438 1.551-1.144 1.943l-.735.407a3 3 0 0 1-2.91 0l-.453-.252a5 5 0 0 0-4.85 0l-.226.126A.46.46 0 0 1 5 10.523V6.137Z" clip-rule="evenodd" />
                                    <path fill-rule="evenodd" d="M5 2a1 1 0 0 1 1 1v14a1 1 0 1 1-2 0V3a1 1 0 0 1 1-1Z" clip-rule="evenodd" />
                                </g>
                            </svg>Moderation</a></li>
                <?php endif; ?>

                <?php if (userHasPermission($user_id, 'admin')): ?>
                    <li class="<?= isActive($tab, 'posts') ?>"><a href="/dashboard?tab=posts"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" style="vertical-align: middle; margin-bottom:5px; margin-right: 0.5rem;" viewBox="0 0 20 20">
                                <g fill="currentColor">
                                    <g opacity=".2">
                                        <path d="M17 3h-7a2 2 0 0 0-2 2v13.5A1.5 1.5 0 0 0 9.5 20H17a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2Z" />
                                        <path fill-rule="evenodd" d="M7 5a3 3 0 0 1 3-3h7a3 3 0 0 1 3 3v13a3 3 0 0 1-3 3H9.5A2.5 2.5 0 0 1 7 18.5V5Zm3-1a1 1 0 0 0-1 1v13.5a.5.5 0 0 0 .5.5H17a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1h-7Z" clip-rule="evenodd" />
                                        <path d="M6.917 10.97c2.164 0 3.918.986 3.918 2.203c0 1.217-1.754 2.203-3.918 2.203C4.754 15.376 3 14.39 3 13.173c0-1.217 1.754-2.204 3.917-2.204Z" />
                                        <path fill-rule="evenodd" d="M4.638 13.86c.533.3 1.337.516 2.28.516c.942 0 1.746-.217 2.28-.517c.564-.317.637-.6.637-.686c0-.087-.073-.37-.638-.687c-.533-.3-1.337-.517-2.28-.517c-.942 0-1.746.217-2.28.517c-.564.318-.637.6-.637.687c0 .086.073.369.638.686Zm-.98 1.742c-.854-.48-1.658-1.3-1.658-2.43c0-1.13.804-1.95 1.657-2.43c.885-.497 2.04-.773 3.26-.773c1.221 0 2.376.276 3.26.774c.854.48 1.658 1.3 1.658 2.43c0 1.13-.804 1.95-1.657 2.43c-.885.497-2.04.773-3.26.773c-1.222 0-2.376-.276-3.26-.774Z" clip-rule="evenodd" />
                                        <path fill-rule="evenodd" d="M6.917 8.6a.9.9 0 0 1 .9.9v1.469a.9.9 0 1 1-1.8 0V9.5a.9.9 0 0 1 .9-.9Z" clip-rule="evenodd" />
                                        <path fill-rule="evenodd" d="M9.053 8.862a.9.9 0 0 1 .706 1.06l-.245 1.224a.9.9 0 0 1-1.765-.353l.245-1.225a.9.9 0 0 1 1.059-.706Zm-4.271 0a.9.9 0 0 0-.706 1.06l.245 1.224a.9.9 0 0 0 1.765-.353l-.245-1.225a.9.9 0 0 0-1.059-.706Z" clip-rule="evenodd" />
                                        <path fill-rule="evenodd" d="M2.537 9.708a.9.9 0 0 0-.309 1.234l.735 1.225a.9.9 0 0 0 1.543-.926l-.734-1.225a.9.9 0 0 0-1.235-.308Zm8.761 0a.9.9 0 0 1 .309 1.234l-.735 1.225a.9.9 0 0 1-1.543-.926l.734-1.225a.9.9 0 0 1 1.235-.308Z" clip-rule="evenodd" />
                                    </g>
                                    <path d="M12.675 15.137a.675.675 0 1 1-1.35 0a.675.675 0 0 1 1.35 0Z" />
                                    <path fill-rule="evenodd" d="M12 14.963a.175.175 0 1 0 0 .35a.175.175 0 0 0 0-.35Zm-1.175.175a1.175 1.175 0 1 1 2.35 0a1.175 1.175 0 0 1-2.35 0Z" clip-rule="evenodd" />
                                    <path fill-rule="evenodd" d="M6 3.5A2.5 2.5 0 0 1 8.5 1h7A2.5 2.5 0 0 1 18 3.5v13a2.5 2.5 0 0 1-2.5 2.5H8a2 2 0 0 1-2-2v-1.5a.5.5 0 0 1 1 0V17a1 1 0 0 0 1 1h7.5a1.5 1.5 0 0 0 1.5-1.5v-13A1.5 1.5 0 0 0 15.5 2h-7A1.5 1.5 0 0 0 7 3.5v3.25a.5.5 0 0 1-1 0V3.5Z" clip-rule="evenodd" />
                                    <path fill-rule="evenodd" d="M2.893 12.795c.62.35 1.512.581 2.524.581c1.013 0 1.904-.232 2.525-.581c.637-.358.893-.775.893-1.122c0-.348-.256-.765-.893-1.123c-.62-.35-1.512-.58-2.525-.58c-1.012 0-1.903.23-2.524.58c-.637.358-.893.775-.893 1.123c0 .347.256.764.893 1.122Zm-.49.872C1.62 13.227 1 12.542 1 11.673c0-.87.621-1.555 1.402-1.994c.797-.449 1.864-.71 3.015-.71c1.152 0 2.219.261 3.016.71c.78.439 1.402 1.124 1.402 1.994c0 .869-.621 1.554-1.402 1.994c-.797.448-1.864.71-3.016.71c-1.15 0-2.218-.262-3.015-.71Z" clip-rule="evenodd" />
                                    <path fill-rule="evenodd" d="M5.417 7.5a.5.5 0 0 1 .5.5v1.47a.5.5 0 0 1-1 0V8a.5.5 0 0 1 .5-.5Zm2.057.255a.5.5 0 0 1 .392.588l-.244 1.224a.5.5 0 0 1-.981-.196l.245-1.224a.5.5 0 0 1 .588-.392Zm-4.114 0a.5.5 0 0 0-.392.588l.245 1.224a.5.5 0 1 0 .98-.196L3.95 8.147a.5.5 0 0 0-.588-.392Zm-2.117.795a.5.5 0 0 0-.172.687l.735 1.224a.5.5 0 1 0 .857-.515L1.93 8.722a.5.5 0 0 0-.686-.171Zm8.349 0a.5.5 0 0 1 .172.687l-.735 1.224a.5.5 0 1 1-.857-.515l.734-1.224a.5.5 0 0 1 .686-.171Z" clip-rule="evenodd" />
                                    <path d="M5.42 10.4a1.25 1.25 0 1 1 0 2.5a1.25 1.25 0 0 1 0-2.5Z" />
                                    <path fill-rule="evenodd" d="M4.67 11.65a.75.75 0 1 0 1.5 0a.75.75 0 0 0-1.5 0Zm.75 1.75a1.75 1.75 0 1 1 0-3.5a1.75 1.75 0 0 1 0 3.5Z" clip-rule="evenodd" />
                                </g>
                            </svg>Manage Posts</a></li>
                <?php endif; ?>

                <li class="<?= isActive($tab, 'settings') ?>"><a href="/dashboard?tab=settings"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" style="vertical-align: middle; margin-bottom:5px; margin-right: 0.5rem;" viewBox="0 0 20 20">
                            <g fill="currentColor" fill-rule="evenodd" clip-rule="evenodd">
                                <path d="M11.558 3.5a.75.75 0 0 1 .685.447l.443 1c.044.1.065.202.065.302a6.2 6.2 0 0 1 1.254.52a.751.751 0 0 1 .219-.151l.97-.443a.75.75 0 0 1 .843.151l.837.838a.75.75 0 0 1 .17.8l-.395 1.02a.748.748 0 0 1-.168.26c.218.398.393.818.52 1.255a.75.75 0 0 1 .261.048l1 .373a.75.75 0 0 1 .488.703v1.184a.75.75 0 0 1-.447.686l-1 .443a.748.748 0 0 1-.302.065a6.227 6.227 0 0 1-.52 1.254c.06.061.112.134.151.219l.444.97a.75.75 0 0 1-.152.843l-.838.837a.75.75 0 0 1-.8.17l-1.02-.395a.749.749 0 0 1-.26-.168a6.225 6.225 0 0 1-1.255.52a.75.75 0 0 1-.048.261l-.373 1a.75.75 0 0 1-.703.488h-1.184a.75.75 0 0 1-.686-.447l-.443-1a.748.748 0 0 1-.065-.302a6.226 6.226 0 0 1-1.254-.52a.752.752 0 0 1-.219.151l-.97.443a.75.75 0 0 1-.843-.151l-.837-.838a.75.75 0 0 1-.17-.8l.395-1.02a.75.75 0 0 1 .168-.26A6.224 6.224 0 0 1 4.999 13a.752.752 0 0 1-.261-.048l-1-.373a.75.75 0 0 1-.488-.703v-1.184a.75.75 0 0 1 .447-.686l1-.443a.748.748 0 0 1 .302-.065a6.2 6.2 0 0 1 .52-1.254a.75.75 0 0 1-.15-.219l-.444-.97a.75.75 0 0 1 .152-.843l.837-.837a.75.75 0 0 1 .801-.17l1.02.395c.102.04.189.097.26.168a6.224 6.224 0 0 1 1.254-.52a.75.75 0 0 1 .048-.261l.373-1a.75.75 0 0 1 .703-.488h1.185Z" opacity=".2" />
                                <path d="M8.232 11.768A2.493 2.493 0 0 0 10 12.5c.672 0 1.302-.267 1.768-.732A2.493 2.493 0 0 0 12.5 10c0-.672-.267-1.302-.732-1.768A2.493 2.493 0 0 0 10 7.5c-.672 0-1.302.267-1.768.732A2.493 2.493 0 0 0 7.5 10c0 .672.267 1.302.732 1.768Zm2.829-.707c-.28.28-.657.439-1.061.439c-.404 0-.78-.16-1.06-.44S8.5 10.405 8.5 10s.16-.78.44-1.06s.656-.44 1.06-.44s.78.16 1.06.44s.44.656.44 1.06s-.16.78-.44 1.06Z" />
                                <path d="m14.216 3.773l-1.27.714a6.213 6.213 0 0 0-1.166-.48l-.47-1.414a.5.5 0 0 0-.474-.343H9.06a.5.5 0 0 0-.481.365l-.392 1.403a6.214 6.214 0 0 0-1.164.486L5.69 3.835a.5.5 0 0 0-.578.094L3.855 5.185a.5.5 0 0 0-.082.599l.714 1.27c-.199.37-.36.76-.48 1.166l-1.414.47a.5.5 0 0 0-.343.474v1.777a.5.5 0 0 0 .365.481l1.403.392c.122.405.285.794.486 1.164l-.669 1.333a.5.5 0 0 0 .094.578l1.256 1.256a.5.5 0 0 0 .599.082l1.27-.714c.37.199.76.36 1.166.48l.47 1.414a.5.5 0 0 0 .474.343h1.777a.5.5 0 0 0 .481-.365l.392-1.403a6.21 6.21 0 0 0 1.164-.486l1.333.669a.5.5 0 0 0 .578-.093l1.256-1.257a.5.5 0 0 0 .082-.599l-.714-1.27c.199-.37.36-.76.48-1.166l1.414-.47a.5.5 0 0 0 .343-.474V9.06a.5.5 0 0 0-.365-.481l-1.403-.392a6.208 6.208 0 0 0-.486-1.164l.669-1.333a.5.5 0 0 0-.093-.578l-1.257-1.256a.5.5 0 0 0-.599-.082Zm-1.024 1.724l1.184-.667l.733.733l-.627 1.25a.5.5 0 0 0 .019.482c.265.44.464.918.59 1.418a.5.5 0 0 0 .35.36l1.309.366v1.037l-1.327.44a.5.5 0 0 0-.328.354a5.216 5.216 0 0 1-.585 1.42a.5.5 0 0 0-.007.502l.667 1.184l-.733.733l-1.25-.627a.5.5 0 0 0-.482.019c-.44.265-.918.464-1.418.59a.5.5 0 0 0-.36.35l-.366 1.309H9.525l-.44-1.327a.5.5 0 0 0-.355-.328a5.217 5.217 0 0 1-1.42-.585a.5.5 0 0 0-.502-.007l-1.184.667l-.733-.733l.627-1.25a.5.5 0 0 0-.019-.482a5.216 5.216 0 0 1-.59-1.418a.5.5 0 0 0-.35-.36l-1.309-.366V9.525l1.327-.44a.5.5 0 0 0 .327-.355c.125-.5.323-.979.586-1.42a.5.5 0 0 0 .007-.502L4.83 5.624l.733-.733l1.25.627a.5.5 0 0 0 .482-.019c.44-.265.918-.464 1.418-.59a.5.5 0 0 0 .36-.35l.366-1.309h1.037l.44 1.327a.5.5 0 0 0 .354.327c.5.125.979.323 1.42.586a.5.5 0 0 0 .502.007Z" />
                            </g>
                        </svg>Settings</a></li>

            </ul>
        </div>

        <div class="main-content">
            <?php
            $db = getDbConnection();
$user_id = $_SESSION['user_id'];
$query = "SELECT is_admin FROM users WHERE user_id = $1";
$result = pg_query_params($db, $query, [$user_id]);
$is_admin = false;

if ($result) {
    $user = pg_fetch_assoc($result);
    if (userHasPermission($user_id, 'admin')) {
        $is_admin = true;
    }
} else {
    echo "<p>Error fetching user information.</p>";
    exit();
}

switch ($tab) {
    case 'bookings':
        echo "<h1>Bookings</h1>";
        // TODO: SHOW ATTENDEE LIST IN A TABLE with user details, and price.
        break;

    case 'sales':
        echo "<h1>Sales</h1>";
        // TODO: SHOW TOTAL REVENUE
        break;

    case 'moderation':
        echo "<h1>Moderation</h1>";
        if (!$is_admin) {
            echo "<p>You do not have permission to access this page.</p>";
            break;
        }

        $action = $_POST['action'] ?? '';
        $target_user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
        $message = '';

        if ($target_user_id && ($action === 'promote' || $action === 'demote' || $action === 'delete')) {
            if ($action === 'promote') {
                $query = "UPDATE users SET is_admin = TRUE WHERE user_id = $1";
                $result = pg_query_params($db, $query, [$target_user_id]);
                $message = $result ? "User promoted to admin." : "Error promoting user.";
            } elseif ($action === 'demote') {
                $query = "UPDATE users SET is_admin = FALSE WHERE user_id = $1";
                $result = pg_query_params($db, $query, [$target_user_id]);
                $message = $result ? "Admin rights removed from user." : "Error demoting user.";
            } elseif ($action === 'delete') {
                $deleted_user_query = "SELECT user_id FROM users WHERE username = 'deleted_user'";
                $deleted_user_result = pg_query($db, $deleted_user_query);

                if ($deleted_user_result && pg_num_rows($deleted_user_result) > 0) {
                    $deleted_user_id = pg_fetch_result($deleted_user_result, 0, 'user_id');

                    $updateRelatedRecordsQuery = "UPDATE user_event_attendance SET user_id = $1 WHERE user_id = $2";
                    $updateRelatedRecordsResult = pg_query_params($db, $updateRelatedRecordsQuery, [$deleted_user_id, $target_user_id]);

                    if ($updateRelatedRecordsResult) {
                        $deleteUserQuery = "DELETE FROM users WHERE user_id = $1";
                        $deleteUserResult = pg_query_params($db, $deleteUserQuery, [$target_user_id]);
                        $message = $deleteUserResult ? "User successfully deleted." : "Error deleting user.";
                    } else {
                        $message = "Error updating related records for user deletion.";
                    }
                } else {
                    $message = "Placeholder deleted user not found.";
                }
            }
        }

        echo "<div class='message' style='text-align: center; margin-bottom: 1rem'>$message</div>";

        $query = "SELECT user_id, username, email, is_admin FROM users WHERE username != 'deleted_user'";
        $usersResult = pg_query($db, $query);

        if ($usersResult) {
            $users = pg_fetch_all($usersResult);
            if ($users) {
                echo "<div class='user-list'>";
                foreach ($users as $user) {
                    if (userHasPermission($user['user_id'], 'admin')) {
                        $user['is_admin'] = true;
                    } else {
                        $user['is_admin'] = false;
                    }

                    echo "<div class='user-item'>
                                <h3>" . htmlspecialchars($user['username']) . "</h3>
                                <p>Email: " . htmlspecialchars($user['email']) . "</p>
                                <p>Admin: " . ($user['is_admin'] ? 'Yes' : 'No') . "</p>
                                <form method='POST' action='/dashboard?tab=moderation'>
                                <input type='hidden' name='user_id' value='" . $user['user_id'] . "'>";

                    if ($user['is_admin']) {
                        echo "<button class='moderate-users-button' type='submit' name='action' value='demote'>Remove Admin Rights</button>";
                    } else {
                        echo "<button class='moderate-users-button' type='submit' name='action' value='promote'>Make Admin</button>";
                    }

                    echo "<button class='moderate-users-button-delete' type='submit' name='action' value='delete' onclick='return confirm(\"Are you sure you want to delete this user?\")'>Delete User</button>
                                </form>
                                </div>";
                }
                echo "</div>";
            } else {
                echo "<p>No users found.</p>";
            }
            echo '
                    <style>

                    .user-list {
                        display: flex;
                        flex-wrap: wrap;
                        gap: 10px;
                        justify-content: flex-start;
                    }

                    .user-item {
                        flex: 1 1 400px;
                        box-sizing: border-box;
                        border: 1px solid #ccc;
                        border-radius: 12px;
                        padding: 1rem;
                        margin-bottom: 1rem;
                    }

                    .user-item h3 {
                        margin-top: 0;
                    }

                    .moderate-users-button-delete {
                        margin-top: 1rem;
                        padding: 0.5rem 1rem;
                        background-color: #000000;
                        color: white;
                        border: none;
                        border-radius: 4px;
                        cursor: pointer;
                        margin-left: 1rem;
                    }

                    .moderate-users-button {
                        margin-top: 1rem;
                        padding: 0.5rem 1rem;
                        background-color: #ec5353;
                        color: white;
                        border: none;
                        border-radius: 4px;
                        cursor: pointer;
                    }

                    moderate-users-button-delete:hover {
                        background-color: #ff6c4a;
                    }

                    </style>
                    ';
        } else {
            echo "<p>Error fetching user list.</p>";
        }
        break;

    case 'posts':
        echo "<h1>Manage Posts</h1>";
        $event_id = isset($_POST['event_id']) ? intval($_POST['event_id']) : 0;
        $action = isset($_POST['action']) ? $_POST['action'] : '';

        if ($event_id && ($action === 'approve' || $action === 'reject')) {
            if ($action === 'approve') {
                $query = "UPDATE events SET is_approved = TRUE WHERE event_id = $1";
                $result = pg_query_params($db, $query, [$event_id]);

                if ($result) {
                    $message = "Event successfully approved!";
                } else {
                    $message = "Error approving the event.";
                }
            } elseif ($action === 'reject') {
                $deleteTicketsQuery = "DELETE FROM tickets WHERE event_id = $1";
                $deleteEventQuery = "DELETE FROM events WHERE event_id = $1";

                pg_query_params($db, $deleteTicketsQuery, [$event_id]);
                $deleteResult = pg_query_params($db, $deleteEventQuery, [$event_id]);

                if ($deleteResult) {
                    $message = "Event successfully rejected and deleted!";
                } else {
                    $message = "Error rejecting and deleting the event.";
                }
            }
        } else {
            $message = "";
        }

        echo "<div class='message'>$message</div>";

        $query = "SELECT event_id, title, description, location FROM events WHERE is_approved = FALSE";
        $eventsResult = pg_query($db, $query);

        if ($eventsResult) {
            $events = pg_fetch_all($eventsResult);
            if ($events) {
                echo "<div class='event-list'>";
                foreach ($events as $event) {
                    echo "<div class='event-item'>
                                    <h3>" . safe_htmlspecialchars($event['title']) . "</h3>
                                    <p class='descrip'>" . safe_htmlspecialchars($event['description']) . "</p>
                                    <p>Location: " . safe_htmlspecialchars($event['location']) . "</p>
                                    <br>
                                    <form method='POST' action='/dashboard?tab=posts'>
                                        <input type='hidden' name='event_id' value='" . $event['event_id'] . "'>
                                        <button style='background-color: #22c55e; color: #fff; padding: 8px 12px; border-radius: 20px; border: none; font-weight: 700; ' type='submit' name='action' value='approve'>Approve</button>
                                        <button style='background-color: #ff4d4d; color: #fff; padding: 8px 12px; border-radius: 20px; border: none; font-weight: 700; 'type='submit' name='action' value='reject'>Reject and Delete</button>
                                    </form>
                                </div>";
                }
                echo "</div>";
                echo "
                <style>
                    .event-list {
                        max-width: 750px;
                        margin: 5rem auto 0 auto;
                    }

                    .event-item {
                        border: 1px solid grey;
                        border-radius: 12px;
                        padding: 1rem;
                        margin: 1rem 0;
                    }

                    .event-item > h3 {
                        font-size: 1.5rem;
                    }

                    .descrip {
                        color: grey;
                    }
                </style>";
            } else {
                echo "<p>No unapproved events found.</p>";
            }
        } else {
            echo "<p>Error fetching unapproved events.</p>";
        }
        break;

    case 'settings':
        echo "<h1>Settings</h1>";
        break;

    case 'dashboard':
    default:
        echo "<h1>Dashboard</h1>";
        echo "<div>
        <style>
    .bento-container {
      display: grid;
      grid-template-columns: 1fr 1fr 1fr;
      grid-template-rows: auto;
      gap: 16px;
      max-width: 750px;
      padding: 24px;
      background: #ffffff;
      border-radius: 24px;
      margin: 0 auto;
    }

    .bento-item {
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      text-align: center;
      padding: 24px;
      background: #f5f5f7;
      border: 1px solid #e0e0e0;
      border-radius: 16px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
      transition: background 0.3s ease, box-shadow 0.3s ease;
    }

    .bento-item:hover {
      background: #f0f0f0;
    }

    .bento-item h2 {
      margin-bottom: 8px;
      color: #000;
    }

    .bento-item p {
      font-size: 14px;
      color: #6e6e73;
      line-height: 1.6;
      margin-bottom: 16px;
    }

    .bento-item.booking {
      grid-column: span 2;
      grid-row: span 1;
    }

    .bento-item.booking h2 {
      font-size: 24px;
      font-weight: 700;
    }

    .bento-item.booking button {
      width: 100%;
      padding: 12px;
      font-size: 14px;
      font-weight: 600;
      color: #fff;
      background-color: #000;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .bento-item.booking button:hover {
      background-color: #222;
    }

    .bento-item.sales {
      grid-column: span 1;
      background: #000;
      color: #fff;
    }

    .bento-item.sales h2 {
      font-size: 20px;
      font-weight: 600;
    }

    .bento-item.sales button {
      font-size: 14px;
      font-weight: 600;
      background: #ff4a22;
      color: #fff;
      border: none;
      border-radius: 8px;
      padding: 12px 20px;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .bento-item.sales button:hover {
      background: #e0431f;
    }

    .bento-item.explore, .bento-item.settings, .bento-item.profile {
      grid-column: span 1;
    }

    .bento-item.explore h2,
    .bento-item.profile h2 {
      font-size: 18px;
      font-weight: 600;
    }

    .bento-item.settings h2 {
      font-size: 16px;
      font-weight: 500;
    }

    .bento-item button {
      font-size: 14px;
      font-weight: 600;
      color: #fff;
      background-color: #000;
      border: none;
      border-radius: 8px;
      padding: 10px 20px;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .bento-item button:hover {
      background-color: #222;
    }

    @media (max-width: 1000px) {
      .bento-container {
        grid-template-columns: 1fr;
      }

      .bento-item.booking {
        grid-column: span 1;
      }

      .bento-item.sales {
        grid-column: span 1;
      }
    }
  </style>
  <div class='bento-container'>
    
    <div class='bento-item booking'>
      <h2>See Booking on Your Events</h2>
      <p>Check who's attending and manage bookings with ease.</p>
      <a href='/dashboard?tab=bookings'><button>View Bookings</button></a>
    </div>

    
    <div class='bento-item sales'>
      <h2 style='color: #fff;'>Sales on Your Event</h2>
      <p>See ticket sales and revenue trends.</p>
      <a href='/dashboard?tab=sales'><button>View Sales</button></a>
    </div>

    
    <div class='bento-item explore'>
      <h2>Explore Other Events</h2>
      <p>Discover exciting events curated just for you.</p>
      <a href='/explore'><button style='background-color: grey; width: 100%'>Explore</button></a>
    </div>

    
    <div class='bento-item profile'>
      <h2>View Your Profile</h2>
      <p>Manage your personal details and preferences.</p>
      <a href='/profile'><button style='background-color: grey; width: 100%'>View Profile</button></a>
    </div>

    
    <div class='bento-item settings'>
      <h2>View Settings</h2>
      <p>Manage your account and preferences with ease.</p>
      <a href='/settings'><button style='background-color: grey; width: 100%'>Settings</button></a>
    </div>
  </div>
  </div>
  ";
        break;
}
?>
        </div>
    </div>

</body>

</html>
