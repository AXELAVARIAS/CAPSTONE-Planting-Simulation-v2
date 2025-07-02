<?php
session_start();

include 'connection.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plant_id = isset($_POST['plant_id']) ? intval($_POST['plant_id']) : 0;

    $user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

    if ($plant_id > 0 && $user_id > 0) {
        $query = "SELECT name FROM plant WHERE plant_id = $plant_id";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $plant = $result->fetch_assoc();
            $plant_name = $plant['name'];

            $query = "SELECT * FROM favorites WHERE user_id = $user_id AND plant_id = $plant_id";
            $result = $conn->query($query);

            if ($result->num_rows === 0) {
                $query = "INSERT INTO favorites (user_id, plant_id) VALUES ('$user_id', '$plant_id')";
                if ($conn->query($query) === TRUE) {
                    echo json_encode(['status' => 'success', 'message' => 'Plant added to favorites.']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to add plant to favorites.']);
                }
            } else {
                echo json_encode(['status' => 'info', 'message' => 'Plant is already in favorites.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Plant not found in database.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid data.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
$conn->close();
?>
