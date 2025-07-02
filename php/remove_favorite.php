<?php
session_start();
require 'connection.php'; 

header('Content-Type: application/json');

if (isset($_SESSION['user_id']) && isset($_POST['plant_id'])) {
    $user_id = intval($_SESSION['user_id']);
    $plant_id = intval($_POST['plant_id']);

    $stmt = $conn->prepare("DELETE FROM favorites WHERE user_id = ? AND plant_id = ?");
    $stmt->bind_param("ii", $user_id, $plant_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to remove plant from favorites.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>
