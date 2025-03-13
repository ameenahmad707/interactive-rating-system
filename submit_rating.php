<?php
session_start();
require 'db.php'; // Database connection file

$ip_address = $_SERVER['REMOTE_ADDR']; // Get user IP
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;

// Validate rating value
if ($rating < 1 || $rating > 5) {
    exit(json_encode(["success" => false, "message" => "Invalid rating value"]));
}

// Check if the user (or IP) has already rated
$stmt = $conn->prepare("SELECT id FROM ratings WHERE (user_id = ? OR ip_address = ?) LIMIT 1");
$stmt->bind_param("is", $user_id, $ip_address);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    exit(json_encode(["success" => false, "message" => "You have already rated!"]));
}

// Insert rating into the database
$stmt = $conn->prepare("INSERT INTO ratings (user_id, ip_address, rating) VALUES (?, ?, ?)");
$stmt->bind_param("isi", $user_id, $ip_address, $rating);

$response = ["success" => false, "message" => "Something went wrong!"];

if ($stmt->execute()) {
    switch ($rating) {
        case 5:
            $response = ["success" => true, "message" => "🎉 Amazing! Thanks for 5 stars!"];
            break;
        case 4:
            $response = ["success" => true, "message" => "😊 Great! Thanks for 4 stars!"];
            break;
        case 3:
            $response = ["success" => true, "message" => "😐 Thanks! We'll try to improve."];
            break;
        case 2:
            $response = ["success" => true, "message" => "😕 Oh! What could we do better?"];
            break;
        case 1:
            $response = ["success" => true, "message" => "😢 We're sorry! How can we improve?"];
            break;
    }
}

echo json_encode($response);
?>
