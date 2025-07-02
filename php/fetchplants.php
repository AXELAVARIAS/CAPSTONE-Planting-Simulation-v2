<?php
include "connection.php"; 

try {
    $query = "SELECT * FROM plant";
    $result = $conn->query($query);

    if (!$result) {
        throw new Exception("Database query failed: " . $conn->error);
    }

    while ($row = $result->fetch_assoc()) {
        echo '<div class="card" style="display: none;">
                <img src="' . htmlspecialchars($row['image']) . '" class="card-img-top" alt="' . htmlspecialchars($row['name']) . '">
                <div class="card-body">
                    <h5 class="card-title">' . htmlspecialchars($row['name']) . '</h5>
                    <p class="card-text">' . htmlspecialchars($row['description']) . '</p>
                </div>
                <input type="hidden" value="' . htmlspecialchars($row['plant_id']) . '">
              </div>';
    }

    $result->free();
} catch (Exception $e) {
    echo '<p>Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
}
?>
