<?php
include('../connection.php');
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $moduleId = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM modules WHERE module_id = ?");
    $stmt->bind_param("i", $moduleId);

    if ($stmt->execute()) {
        header("Location: adminpage.php#module-management");
    } else {
        header("Location: adminpage.php#module-management");
    }

    $stmt->close();
} else {
    header("Location: adminpage.php");
}
$conn->close();
?>
