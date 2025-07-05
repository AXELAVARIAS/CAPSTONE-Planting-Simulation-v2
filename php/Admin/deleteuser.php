<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

include('../connection.php');

// Check if user ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: adminpage.php?error=No user ID provided");
    exit();
}

$user_id = intval($_GET['id']);

// Prevent admin from deleting themselves
if ($user_id == $_SESSION['user_id']) {
    header("Location: adminpage.php?error=You cannot delete your own account");
    exit();
}

// First, check if the user exists and get their information
$stmt = $conn->prepare("SELECT username, role FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    // Start transaction to ensure data consistency
    $conn->begin_transaction();
    
    try {
        // Delete related data first (foreign key constraints)
        
        // Delete user's replies
        $delete_replies = $conn->prepare("DELETE FROM reply WHERE user_id = ?");
        $delete_replies->bind_param("i", $user_id);
        $delete_replies->execute();
        
        // Delete user's questions
        $delete_questions = $conn->prepare("DELETE FROM questions WHERE user_id = ?");
        $delete_questions->bind_param("i", $user_id);
        $delete_questions->execute();
        
        // Delete user's favorites
        $delete_favorites = $conn->prepare("DELETE FROM favorites WHERE user_id = ?");
        $delete_favorites->bind_param("i", $user_id);
        $delete_favorites->execute();
        
        // Finally, delete the user
        $delete_user = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $delete_user->bind_param("i", $user_id);
        $delete_user->execute();
        
        // Commit transaction
        $conn->commit();
        
        // Redirect back to admin page with success message
        header("Location: adminpage.php?success=User '" . htmlspecialchars($user['username']) . "' deleted successfully");
        exit();
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        header("Location: adminpage.php?error=Failed to delete user: " . $e->getMessage());
        exit();
    }
    
} else {
    // User not found
    header("Location: adminpage.php?error=User not found");
    exit();
}

$stmt->close();
$conn->close();
?> 