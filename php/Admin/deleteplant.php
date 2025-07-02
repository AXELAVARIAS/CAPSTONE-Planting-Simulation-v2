<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

include('../connection.php');

// Check if plant ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: adminpage.php");
    exit();
}

$plant_id = $_GET['id'];

// First, get the plant information to delete the image file
$stmt = $conn->prepare("SELECT image FROM plant WHERE plant_id = ?");
$stmt->bind_param("i", $plant_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $plant = $result->fetch_assoc();
    $image_path = $plant['image'];
    
    // Delete the plant from database
    $delete_stmt = $conn->prepare("DELETE FROM plant WHERE plant_id = ?");
    $delete_stmt->bind_param("i", $plant_id);
    
    if ($delete_stmt->execute()) {
        // If database deletion is successful, try to delete the image file
        if (!empty($image_path) && file_exists("../../" . $image_path)) {
            unlink("../../" . $image_path);
        }
        
        // Redirect back to admin page with success message
        header("Location: adminpage.php?success=Plant deleted successfully");
        exit();
    } else {
        // Redirect back to admin page with error message
        header("Location: adminpage.php?error=Failed to delete plant");
        exit();
    }
} else {
    // Plant not found
    header("Location: adminpage.php?error=Plant not found");
    exit();
}

$stmt->close();
$conn->close();
?> 