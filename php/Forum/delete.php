<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

include "../connection.php";

if ($_SESSION['role'] !== 'agriculturist') {
    echo "You do not have permission to perform this action.";
    exit();
}

if (isset($_GET['type']) && isset($_GET['id'])) {
    $type = $_GET['type'];
    $id = intval($_GET['id']);
    
    if ($type === 'question') {
        $sql = "DELETE FROM questions WHERE question_id = ?";
    } elseif ($type === 'reply') {
        $sql = "DELETE FROM reply WHERE reply_id = ?";
    } else {
        echo "Invalid type.";
        exit();
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header("Location: community.php?status=deleted");
        exit();
    } else {
        echo "Error deleting $type.";
    }
    $stmt->close();
} else {
    echo "Invalid request.";
}
?>
