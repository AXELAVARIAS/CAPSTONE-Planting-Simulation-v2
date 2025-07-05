<?php
include '../connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    $user_id = $_SESSION['user_id'];
    $file = $_FILES['profile_picture'];
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 2 * 1024 * 1024; // 2MB

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = 'File upload error.';
    } elseif (!in_array($file['type'], $allowed_types)) {
        $error = 'Invalid file type. Only JPG, PNG, and GIF allowed.';
    } elseif ($file['size'] > $max_size) {
        $error = 'File too large. Max 2MB.';
    } else {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'user_' . $user_id . '_' . time() . '.' . $ext;
        $target_dir = '../../images/profile_pics/';
        $target_path = $target_dir . $filename;

        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            // Save filename in DB
            $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE user_id = ?");
            $stmt->bind_param("si", $filename, $user_id);
            $stmt->execute();
            $stmt->close();
            header('Location: ../userpage.php?section=profile&upload=success');
            exit();
        } else {
            $error = 'Failed to move uploaded file.';
        }
    }
    header('Location: ../userpage.php?section=profile&upload=error&message=' . urlencode($error));
    exit();
}
?> 