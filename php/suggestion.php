<?php
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $message = htmlspecialchars($_POST['message']);

    $stmt = $conn->prepare("INSERT INTO suggestions (message) VALUES (?)");
    $stmt->bind_param("s", $message);

    if ($stmt->execute()) {
        echo "Thank you for your suggestion! It has been saved.";
        header("Location: ../index.php");
    } else {
        echo "Failed to save your suggestion. Please try again.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
