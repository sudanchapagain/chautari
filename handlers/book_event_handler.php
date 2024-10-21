<?php
session_start();
include 'config/config.php';

function bookEvent($userId, $eventId) {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT * FROM events WHERE id = ? AND available_slots > 0");
    $stmt->bind_param("i", $eventId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $stmt = $conn->prepare("INSERT INTO bookings (user_id, event_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $userId, $eventId);
        $stmt->execute();

        $stmt = $conn->prepare("UPDATE events SET available_slots = available_slots - 1 WHERE id = ?");
        $stmt->bind_param("i", $eventId);
        $stmt->execute();

        $event = $result->fetch_assoc();
        $amount = $event['price'];

        $purchaseOrderId = uniqid();
        header("Location: payment_handler.php?amount=$amount&purchase_order_id=$purchaseOrderId&event_id=$eventId");
        exit();
    } else {
        echo "Event not available or no slots left.";
    }

    $conn->close();
}

if (isset($_SESSION['user_id']) && isset($_GET['event_id'])) {
    $userId = $_SESSION['user_id'];
    $eventId = intval($_GET['event_id']);
    bookEvent($userId, $eventId);
} else {
    echo "User not logged in or event ID missing.";
}
?>
