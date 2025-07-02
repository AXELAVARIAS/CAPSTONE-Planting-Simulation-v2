<?php
include('../connection.php');

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = $conn->real_escape_string($_GET['id']);
    $status = $conn->real_escape_string($_GET['status']);

    $sql = "UPDATE users SET status = '$status' WHERE user_id = '$id'";

    if ($conn->query($sql) === TRUE) {
        echo "Status updated successfully.";
    } else {
        echo "Error updating status: " . $conn->error;
    }
} else {
    echo "Invalid request.";
}
?>
