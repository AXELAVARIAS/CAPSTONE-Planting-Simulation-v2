<?php
    include '../connection.php';
    
    if (isset($_GET['id'], $_GET['field'], $_GET['value'])) {
        $id = intval($_GET['id']);
        $field = $_GET['field'];
        $value = $_GET['value'];

        $allowedFields = ['role', 'status'];
        if (!in_array($field, $allowedFields)) {
            http_response_code(400);
            echo "Invalid field.";
            exit;
        }

        $stmt = $conn->prepare("UPDATE users SET $field = ? WHERE user_id = ?");
        $stmt->bind_param('si', $value, $id);
        if ($stmt->execute()) {
            echo "Success";
        } else {
            http_response_code(500);
            echo "Database error: " . $conn->error;
        }
        $stmt->close();
    } else {
        http_response_code(400);
        echo "Invalid request.";
    }
?>
