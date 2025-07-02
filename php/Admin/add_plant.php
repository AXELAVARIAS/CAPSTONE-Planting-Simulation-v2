<?php
include '../connection.php';
session_start();

if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $target_dir = "../../images/"; 
    $image_name = basename($_FILES['image']['name']);
    $unique_name = uniqid() . "_" . $image_name; 
    $target_file = $target_dir . $unique_name; 
    $image_path = "../images/" . $unique_name; 
    $image_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
    if (in_array($image_type, $allowed_types)) {
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $name = $_POST['name'];
            $description = $_POST['description'];
            $container_soil = $_POST['container_soil'];
            $watering = $_POST['watering'];
            $sunlight = $_POST['sunlight'];
            $tips = $_POST['tips'];

            $sql = "INSERT INTO plant (name, description, image, container_soil, watering, sunlight, tips)
                    VALUES ('$name', '$description', '$image_path', '$container_soil', '$watering', '$sunlight', '$tips')";

            if ($conn->query($sql) === TRUE) {
                $role = $_SESSION['role']; 
                if ($role === 'admin') {
                    header("Location: adminpage.php?section=add-plant&status=success");
                } elseif ($role === 'agriculturist') {
                    header("Location: agriculturistpage.php?section=add-plant&status=success");
                } else {
                    echo "Invalid role.";
                }
                exit();
            } else {
                echo "Database Error: " . $conn->error;
            }
        } else {
            echo "Failed to upload the image. Please try again.";
        }
    } else {
        echo "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
    }
} else {
    echo "Please upload an image.";
}
?>
