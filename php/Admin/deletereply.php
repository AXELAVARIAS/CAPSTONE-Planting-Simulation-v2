<?php
session_start();
include "../connection.php";

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if (isset($_GET['id'])) {
    $reply_id = intval($_GET['id']);

    $delete_replies = $conn->prepare("DELETE FROM reply WHERE reply_id = ?");
    $delete_replies->bind_param("i", $reply_id);
    $delete_replies->execute();

    header("Location: adminpage.php#forum-management");
    exit;
} else {
    echo "No question ID provided.";
}
?>
