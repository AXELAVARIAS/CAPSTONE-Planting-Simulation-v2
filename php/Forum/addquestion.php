<?php
session_start();

include "../connection.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php"); 
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $body = $_POST['body'];
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("INSERT INTO Questions (user_id, title, body) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $title, $body); 
    if ($stmt->execute()) {
        header("Location: community.php");
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
    $stmt->close();
}
$conn->close();
?>
