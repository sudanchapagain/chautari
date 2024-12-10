<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/credentials.php';

$db = getDbConnection();
$event_id = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0;
$action = $_GET['action'] ?? '';

if ($event_id <= 0) {
    header('Location: /explore');
    exit();
}

$imageQuery = "SELECT image_url FROM event_images WHERE event_id = $1 LIMIT 1";
$imageResult = pg_query_params($db, $imageQuery, [$event_id]);
$image = pg_fetch_assoc($imageResult);

$query = "
    SELECT e.*, 
           u.username AS organizer_name,
           u.user_id AS organizer_id,
           COALESCE((
               SELECT COUNT(*) FROM user_event_attendance WHERE event_id = e.event_id
           ), 0) AS current_participants
    FROM events e
    LEFT JOIN users u ON e.organizer_id = u.user_id
    WHERE e.event_id = $1 AND e.is_approved = TRUE
";
$result = pg_query_params($db, $query, [$event_id]);

if (!$result) {
    error_log("Database Error: " . pg_last_error($db));
    $error = "An error occurred while fetching the event.";
} else {
    $event = pg_fetch_assoc($result);
    if (!$event) {
        header('Location: /explore');
        exit();
    }
}

$user_id = $_SESSION['user_id'] ?? null;
$is_admin = $_SESSION['is_admin'] ?? false;
$is_organizer = $user_id === $event['organizer_id'] || $is_admin;

$bookingQuery = "SELECT 1 FROM user_event_attendance WHERE user_id = $1 AND event_id = $2 LIMIT 1";
$bookingResult = pg_query_params($db, $bookingQuery, [$user_id, $event_id]);
$is_booked = pg_num_rows($bookingResult) > 0;

if ($action === 'cancel' && $is_booked) {
    $cancelQuery = "DELETE FROM user_event_attendance WHERE user_id = $1 AND event_id = $2";
    $cancelResult = pg_query_params($db, $cancelQuery, [$user_id, $event_id]);

    if ($cancelResult) {
        $_SESSION['transaction_msg'] = "Your booking has been canceled.";
    } else {
        $_SESSION['transaction_msg'] = "Error canceling your booking.";
    }

    header("Location: /event?event_id=$event_id");
    exit();
}

if ($action === 'pay' && isset($_SESSION['user_id']) && !$is_booked) {
    if ($event['capacity'] > $event['current_participants']) {
        $addQuery = "
            INSERT INTO user_event_attendance (user_id, event_id)
            VALUES ($1, $2)
            ON CONFLICT DO NOTHING
        ";
        pg_query_params($db, $addQuery, [$user_id, $event_id]);
        $_SESSION['transaction_msg'] = "You have successfully registered for the event.";
    } else {
        $_SESSION['transaction_msg'] = "Sorry, the event is fully booked.";
    }
    header("Location: /event?event_id=$event_id");
    exit();
}

if ($action === 'delete' && $is_organizer) {
    $deleteQuery = "DELETE FROM events WHERE event_id = $1";
    $deleteResult = pg_query_params($db, $deleteQuery, [$event_id]);

    if ($deleteResult) {
        $_SESSION['transaction_msg'] = "Event successfully deleted.";
    } else {
        $_SESSION['transaction_msg'] = "Error deleting event.";
    }

    header('Location: /explore');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($event['title'] ?? 'Event Details') ?> - Chautari</title>
    <link rel="stylesheet" href="../assets/css/default.css">
    <link rel="stylesheet" href="../assets/css/index.css">
</head>
<body>
    <?php include __DIR__ . '/../components/header.php'; ?>

    <main>
        <?php if (isset($error)): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php else: ?>
            <div class="event-details boxed-text">
                <?php if ($image && isset($image['image_url'])): ?>
                    <img src="<?= htmlspecialchars($image['image_url']); ?>" width="100%" style="height: 400px;border-radius: 12px;border: 1px solid black;margin-top: 5rem;">
                <?php else: ?>
                    <div style="display: block; height: 100px"></div>
                <?php endif; ?>

                <h2 style="text-align: left;font-size: 3rem;"><?= htmlspecialchars($event['title']) ?></h2>
                <br>
                <div class="event-meta" style="display: grid;grid-template-columns: 1fr 1fr 1fr;">
                    <p><b>Organized by:</b> <?= htmlspecialchars($event['organizer_name'] ?? 'Unknown') ?></p>
                    <p><b>Location:</b> <?= htmlspecialchars($event['location']) ?></p>
                    <p><b>Capacity:</b> <?= $event['capacity'] ?> (Available: <?= $event['capacity'] - $event['current_participants'] ?>)</p>
                </div>

                <br><br>
                <p style="font-size: 1.3rem; text-align: center;"><b>NPR </b><?= htmlspecialchars($event['ticket_price']) ?></p>

                <div class="event-actions" style="margin: 50px auto; max-width: 400px; display: flex; gap: 10px;">
                    <?php if ($is_booked): ?>
                        <p>You have already registered for this event.</p>
                        <form action="/event?event_id=<?= $event_id ?>" method="GET" style="margin: 0;">
                            <input type="hidden" name="event_id" value="<?= $event_id ?>">
                            <input type="hidden" name="action" value="cancel">
                            <button type="submit" class="secondary-button" style="background-color: #E81C30; border:none; color: #fff;">Cancel Booking</button>
                        </form>
                    <?php elseif (!isset($_SESSION['user_id'])): ?>
                        <p><a href="/login" class="secondary-button">Login to register for this event</a></p>
                    <?php else: ?>
                        <form action="/event?event_id=<?= $event_id ?>" method="GET" style="margin: 0;">
                            <input type="hidden" name="event_id" value="<?= $event_id ?>">
                            <input type="hidden" name="action" value="pay">
                            <button type="submit" class="primary-button register-btn" style="border: none;">Book Event</button>
                        </form>
                    <?php endif; ?>

                    <?php if ($is_organizer): ?>
                        <a href="/event/edit?event_id=<?= $event_id ?>" class="primary-button" style="background-color: #007BFF;">Update</a>
                        <form action="/event?event_id=<?= $event_id ?>" method="GET" style="margin: 0;">
                            <input type="hidden" name="event_id" value="<?= $event_id ?>">
                            <input type="hidden" name="action" value="delete">
                            <button type="submit" class="secondary-button" style="background-color: #E81C30; border:none; color: #fff;">Delete</button>
                        </form>
                    <?php elseif ($is_admin): ?>
                        <a href="/event/edit?event_id=<?= $event_id ?>" class="primary-button" style="background-color: #007BFF;">Update</a>
                        <form action="/event?event_id=<?= $event_id ?>" method="GET" style="margin: 0;">
                            <input type="hidden" name="event_id" value="<?= $event_id ?>">
                            <input type="hidden" name="action" value="delete">
                            <button type="submit" class="secondary-button" style="background-color: #E81C30; border:none; color: #fff;">Delete</button>
                        </form>
                    <?php endif; ?>
                </div>

                <div class="event-description">
                    <h2>Description</h2>
                    <p><?= nl2br(htmlspecialchars($event['description'])) ?></p>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <style>
        main {
            max-width: 750px;
            margin: 0 auto;
        }

        .event-description {
            background-color: #f0f0f0;
            border-radius: 12px;
            padding: 1rem;
            margin: 2rem 0;
        }

        .event-description > h2 {
            color: #555;
        }

        .event-actions {
            display: flex;
            justify-content: center;
            gap: 10px;
        }
    </style>

    <?php include __DIR__ . '/../components/footer.php'; ?>
</body>
</html>
