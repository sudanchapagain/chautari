<?php
session_start();
include 'config/config.php'; // Includes credentials and database configuration

function initiatePayment($amount, $purchaseOrderId, $returnUrl) {
    $url = 'https://a.khalti.com/api/v2/epayment/initiate/';
    $data = [
        "return_url" => $returnUrl,
        "website_url" => "https://example.com/",
        "amount" => $amount * 100, // amount in paisa
        "purchase_order_id" => $purchaseOrderId,
        "purchase_order_name" => "Event Booking"
    ];

    $headers = [
        "Authorization: Key " . KHALTI_SECRET_KEY,
        "Content-Type: application/json"
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    curl_close($ch);

    $responseData = json_decode($response, true);
    if (isset($responseData['payment_url'])) {
        header("Location: " . $responseData['payment_url']);
        exit();
    } else {
        echo "Error initiating payment.";
    }
}

if (isset($_GET['amount']) && isset($_GET['purchase_order_id']) && isset($_GET['event_id'])) {
    $amount = intval($_GET['amount']);
    $purchaseOrderId = $_GET['purchase_order_id'];
    $eventId = intval($_GET['event_id']);

    $returnUrl = "https://example.com/payment_callback.php";

    initiatePayment($amount, $purchaseOrderId, $returnUrl);
} else {
    echo "Amount, purchase order ID, or event ID missing.";
}
?>
