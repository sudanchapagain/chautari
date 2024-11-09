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
           COALESCE((
               SELECT COUNT(*) FROM user_event_attendance WHERE event_id = e.event_id
           ), 0) AS current_participants,
           e.capacity - COALESCE((
               SELECT COUNT(*) FROM user_event_attendance WHERE event_id = e.event_id
           ), 0) AS remaining_capacity,
           (
               SELECT MIN(ticket_price) FROM tickets WHERE event_id = e.event_id
           ) AS min_ticket_price
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

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    if ($action === 'pay') {
        if ($event['remaining_capacity'] > 0) {
            if (!$event['min_ticket_price'] || $event['min_ticket_price'] == 0) {
                $addQuery = "
                    INSERT INTO user_event_attendance (user_id, event_id)
                    VALUES ($1, $2)
                    ON CONFLICT DO NOTHING
                ";
                pg_query_params($db, $addQuery, [$user_id, $event_id]);
                $_SESSION['transaction_msg'] = "You have successfully registered for the event.";
                header("Location: /event/$event_id");
                exit();
            } else {
                $amount = $event['min_ticket_price'] * 100;
                $postFields = [
                    "return_url" => "http://localhost/src/routes/event.php?event_id=$event_id&action=response",
                    "website_url" => "http://localhost/",
                    "amount" => $amount,
                    "purchase_order_id" => "event_$event_id",
                    "purchase_order_name" => $event['title'],
                    "customer_info" => [
                        "name" => $_SESSION['user_name'],
                        "email" => $_SESSION['user_email'],
                        "phone" => $_SESSION['user_phone']
                    ]
                ];

                $jsonData = json_encode($postFields);
                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL => 'https://a.khalti.com/api/v2/epayment/initiate/',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => $jsonData,
                    CURLOPT_HTTPHEADER => [
                        'Authorization: Key ' . KHALTI_SECRET_KEY,
                        'Content-Type: application/json'
                    ]
                ]);

                $response = curl_exec($curl);
                curl_close($curl);

                $responseArray = json_decode($response, true);
                if (isset($responseArray['payment_url'])) {
                    header('Location: ' . $responseArray['payment_url']);
                    exit();
                } else {
                    $error = "Payment initiation failed. Please try again.";
                }
            }
        } else {
            $error = "Sorry, the event is fully booked.";
        }
    } elseif ($action === 'response') {
        $pidx = $_GET['pidx'] ?? null;
        if ($pidx) {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://a.khalti.com/api/v2/epayment/lookup/',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode(['pidx' => $pidx]),
                CURLOPT_HTTPHEADER => [
                    'Authorization: Key ' . KHALTI_SECRET_KEY,
                    'Content-Type: application/json'
                ]
            ]);

            $response = curl_exec($curl);
            curl_close($curl);

            $responseArray = json_decode($response, true);
            if ($responseArray['status'] === 'Completed') {
                $addQuery = "
                    INSERT INTO user_event_attendance (user_id, event_id)
                    VALUES ($1, $2)
                    ON CONFLICT DO NOTHING
                ";
                pg_query_params($db, $addQuery, [$user_id, $event_id]);
                $_SESSION['transaction_msg'] = "Payment successful. You are now registered for the event.";
            } else {
                $_SESSION['transaction_msg'] = "Payment failed or canceled.";
            }
            header("Location: /events/$event_id");
            exit();
        }
    }
} else {
    $_SESSION['transaction_msg'] = "You must log in to register for the event.";
    header("Location: /login");
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

                <div class="event-meta" style="display: grid;grid-template-columns: 1fr 1fr 1fr;">
                    <p>Organized by: <?= htmlspecialchars($event['organizer_name'] ?? 'Unknown') ?></p>
                    <p>Location: <?= htmlspecialchars($event['location']) ?></p>

                    <?php if (!empty($categories)): ?><p>Categories: <?= htmlspecialchars(implode(', ', $categories)) ?></p><?php endif; ?>

                    <?php if ($event['capacity'] > 0): ?>
                        <div class="event-registration">
                            <p>Available Spots: <?= $event['capacity'] - $event['current_participants'] ?></p>
                        </div>
                <?php endif; ?>
                </div>

                <?php if ($event['capacity'] > 0): ?>
                    <div class="event-registration" style="margin: 50px auto; max-width: 200px;">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <form action="/event?event_id=<?= $event_id ?>" method="GET">
                                <input type="hidden" name="event_id" value="<?= $event_id ?>">
                                <input type="hidden" name="action" value="pay">
                                <button type="submit" class="primary-button register-btn" style="border: none;">Register for Event</button>
                            </form>
                        <?php else: ?>
                            <p><a href="/login" class="secondary-button">Login to register for this event</a></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="event-description">
                    <h2>Description</h2>
                    <p><?= nl2br(htmlspecialchars($event['description'])) ?></p>

                    <hr>

                    <?php if ($event['terms_and_conditions']): ?>
                        <div class="event-terms">
                            <h2>Terms and Conditions</h2>
                            <p><?= nl2br(htmlspecialchars($event['terms_and_conditions'])) ?></p>
                        </div>
                    <?php endif; ?>
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

        .event-terms > h2 {
            color: #555;
        }
    </style>

    <?php include __DIR__ . '/../components/footer.php'; ?>
</body>
</html>

