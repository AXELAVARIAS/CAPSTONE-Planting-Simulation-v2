<?php
include 'connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image URL Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .test-image {
            max-width: 200px;
            max-height: 150px;
            border: 2px solid #ddd;
            border-radius: 8px;
        }
        .error-message {
            color: red;
            font-size: 0.9em;
        }
        .success-message {
            color: green;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Module Image URL Test</h2>
        <p class="text-muted">This page helps you test if your image URLs are working correctly.</p>
        
        <div class="row">
            <?php
            $result = $conn->query("SELECT module_id, title, image_path FROM modules ORDER BY module_id");
            
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $image_path = $row['image_path'];
                    $is_url = filter_var($image_path, FILTER_VALIDATE_URL);
                    
                    echo '<div class="col-md-4 mb-4">';
                    echo '<div class="card">';
                    echo '<div class="card-header">';
                    echo '<strong>' . htmlspecialchars($row['title']) . '</strong><br>';
                    echo '<small class="text-muted">ID: ' . $row['module_id'] . '</small>';
                    echo '</div>';
                    echo '<div class="card-body text-center">';
                    
                    if ($is_url) {
                        echo '<p class="success-message">✓ External URL</p>';
                        echo '<img src="' . htmlspecialchars($image_path) . '" class="test-image" alt="Test Image" onerror="this.style.display=\'none\'; this.nextElementSibling.style.display=\'block\';">';
                        echo '<p class="error-message" style="display: none;">❌ Image failed to load</p>';
                        echo '<small class="text-muted">URL: ' . htmlspecialchars($image_path) . '</small>';
                    } else {
                        echo '<p class="text-info">📁 Local File</p>';
                        echo '<img src="' . htmlspecialchars($image_path) . '" class="test-image" alt="Test Image" onerror="this.style.display=\'none\'; this.nextElementSibling.style.display=\'block\';">';
                        echo '<p class="error-message" style="display: none;">❌ Image failed to load</p>';
                        echo '<small class="text-muted">Path: ' . htmlspecialchars($image_path) . '</small>';
                    }
                    
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<div class="col-12"><p class="text-center text-muted">No modules found.</p></div>';
            }
            ?>
        </div>
        
        <div class="mt-4">
            <h4>Test External URL</h4>
            <form method="POST" class="row g-3">
                <div class="col-md-8">
                    <input type="url" class="form-control" name="test_url" placeholder="Enter image URL to test" value="<?php echo isset($_POST['test_url']) ? htmlspecialchars($_POST['test_url']) : ''; ?>">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Test URL</button>
                </div>
            </form>
            
            <?php if (isset($_POST['test_url']) && !empty($_POST['test_url'])): ?>
                <div class="mt-3">
                    <h5>Test Result:</h5>
                    <div class="text-center">
                        <img src="<?php echo htmlspecialchars($_POST['test_url']); ?>" class="test-image" alt="Test Image" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                        <p class="error-message" style="display: none;">❌ This URL failed to load an image</p>
                        <p class="success-message">✓ URL format is valid</p>
                        <small class="text-muted"><?php echo htmlspecialchars($_POST['test_url']); ?></small>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="mt-4">
            <h4>Common Issues & Solutions</h4>
            <div class="alert alert-info">
                <h6>Google Drive Images:</h6>
                <ul>
                    <li><strong>Wrong format:</strong> <code>https://drive.google.com/file/d/FILE_ID/view</code></li>
                    <li><strong>Correct format:</strong> <code>https://drive.google.com/uc?export=view&id=FILE_ID</code></li>
                </ul>
                
                <h6>Other Common Issues:</h6>
                <ul>
                    <li>Make sure the image URL is publicly accessible</li>
                    <li>Check if the URL ends with a valid image extension (.jpg, .png, .gif, etc.)</li>
                    <li>Some websites block direct image linking - try using an image hosting service like Imgur</li>
                    <li>For Google Drive, make sure the file is set to "Anyone with the link can view"</li>
                </ul>
            </div>
        </div>
        
        <div class="mt-3">
            <a href="Admin/adminpage.php" class="btn btn-secondary">Back to Admin</a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 