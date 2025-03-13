<?php
require 'db.php';

$sql = "SELECT AVG(rating) as avg_rating FROM ratings";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$averageRating = round($row['avg_rating'], 1);

echo json_encode(["success" => true, "average_rating" => $averageRating]);
?>
