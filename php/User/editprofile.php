<?php
    include '../connection.php';
    session_start();
    
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login.php");
        exit();
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user_id = $_SESSION['user_id'];
        $name = $conn->real_escape_string($_POST['name']);
        $username = $conn->real_escape_string($_POST['username']);
    
        $sql = "UPDATE users SET name = '$name', username = '$username' WHERE user_id = $user_id";
    
        if ($conn->query($sql) === TRUE) {
            $_SESSION['name'] = $name;
            $_SESSION['username'] = $username;
            header("Location: ../userpage.php");
        } else {
            echo "Error updating profile: " . $conn->error;
        }
    }

?>