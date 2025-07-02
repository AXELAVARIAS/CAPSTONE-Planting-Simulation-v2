<?php
include('../connection.php'); 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['suggestion_id']) && isset($_POST['status'])) {
        $suggestionId = $_POST['suggestion_id'];
        $status = $_POST['status'];

        $stmt = $conn->prepare("UPDATE suggestions SET status = ? WHERE suggestion_id = ?");
        $stmt->bind_param("si", $status, $suggestionId);

        if ($stmt->execute()) {
            echo 'success';
        } else {
            echo 'error';
        }
    } else {
        echo 'Invalid parameters';
    }
}
?>
