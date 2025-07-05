<?php
    include '../connection.php';

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    $message = ""; 

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $type = mysqli_real_escape_string($conn, $_POST['type']);
        $category = mysqli_real_escape_string($conn, $_POST['category']);
        
        // Handle content input (file upload or URL)
        $content = "";
        if (!empty($_POST['content_url']) && filter_var($_POST['content_url'], FILTER_VALIDATE_URL)) {
            $content = $_POST['content_url'];
        } elseif (isset($_FILES['content_file']) && $_FILES['content_file']['error'] == 0) {
            $allowed_content_types = [
                'image/jpeg', 'image/jpg', 'image/png', 'application/pdf',
                'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation'
            ];
            $content_file_type = $_FILES['content_file']['type'];
            $content_file_size = $_FILES['content_file']['size'];
            $content_file_name = $_FILES['content_file']['name'];
            if (!in_array($content_file_type, $allowed_content_types)) {
                $message = "<div class='alert alert-danger'>Invalid content file type. Allowed: JPEG, PNG, PDF, DOC, DOCX, PPT, PPTX.</div>";
            } elseif ($content_file_size > 20 * 1024 * 1024) {
                $message = "<div class='alert alert-danger'>Content file size too large. Max 20MB.</div>";
            } else {
                $content_file_extension = pathinfo($content_file_name, PATHINFO_EXTENSION);
                $new_content_filename = 'module_content_' . time() . '_' . rand(1000, 9999) . '.' . $content_file_extension;
                $content_upload_path = '../../html/modulefiles/' . $new_content_filename;
                if (move_uploaded_file($_FILES['content_file']['tmp_name'], $content_upload_path)) {
                    $content = '../html/modulefiles/' . $new_content_filename;
                } else {
                    $message = "<div class='alert alert-danger'>Failed to upload content file. Please try again.</div>";
                }
            }
        } else {
            $message = "<div class='alert alert-warning'>Please provide either a content file or content URL.</div>";
        }

        // Handle image input (file upload or URL)
        $image_path = "";
        
        // Check if user provided an image URL
        if(!empty($_POST['image_url']) && filter_var($_POST['image_url'], FILTER_VALIDATE_URL)) {
            $image_path = $_POST['image_url'];
        }
        // Check if user uploaded a file
        elseif(isset($_FILES['module_image']) && $_FILES['module_image']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/avif', 'image/webp'];
            $file_type = $_FILES['module_image']['type'];
            $file_size = $_FILES['module_image']['size'];
            $file_name = $_FILES['module_image']['name'];
            
            // Validate file type
            if(!in_array($file_type, $allowed_types)) {
                $message = "<div class='alert alert-danger'>Invalid file type. Only JPG, PNG, GIF, AVIF, and WebP images are allowed.</div>";
            }
            // Validate file size (5MB limit)
            elseif($file_size > 5 * 1024 * 1024) {
                $message = "<div class='alert alert-danger'>File size too large. Maximum size is 5MB.</div>";
            }
            else {
                // Generate unique filename
                $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
                $new_filename = 'module_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
                $upload_path = '../../html/moduleimages/' . $new_filename;
                
                // Upload file
                if(move_uploaded_file($_FILES['module_image']['tmp_name'], $upload_path)) {
                    $image_path = '../html/moduleimages/' . $new_filename;
                } else {
                    $message = "<div class='alert alert-danger'>Failed to upload image. Please try again.</div>";
                }
            }
        } else {
            $message = "<div class='alert alert-warning'>Please provide either an image file or image URL.</div>";
        }

        if (!empty($title) && !empty($description) && !empty($content) && !empty($type) && !empty($category) && !empty($image_path) && empty($message)) {
            $sql = "INSERT INTO modules (title, description, type, category, content, image_path) VALUES ('$title','$description','$type','$category','$content','$image_path')";

            if ($conn->query($sql) === TRUE) {
                $message = "<div class='alert alert-success'>New module added successfully!</div>";
                // Redirect after a short delay
                echo "<script>setTimeout(function(){ window.location.href='adminpage.php#module-management'; }, 1500);</script>";
            } else {
                $message = "<div class='alert alert-danger'>Failed to add new module: " . mysqli_error($conn) . "</div>";
            }
        } elseif(empty($message)) {
            $message = "<div class='alert alert-warning'>Please fill all the required fields and provide an image!</div>";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Module</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Irish+Grover&display=swap" rel="stylesheet">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            overflow-x: hidden;
            background: #e8f5e9;
            font-family: 'Poppins', Arial, sans-serif;
        }
        .d-flex {
            min-height: 100vh;
            justify-content: center;
            align-items: center;
            padding: 80px 0 20px 0;
        }
        .form-container {
            background: #f1fdf6;
            border-radius: 22px;
            box-shadow: 0 8px 32px 0 rgba(56,142,60,0.18);
            border: 2.5px solid #43a047;
            padding: 2.5rem 2.5rem 2rem 2.5rem;
            max-width: 900px;
            margin: 0 auto;
            animation: fadeInUp 0.7s cubic-bezier(.39,.575,.565,1) both;
        }
        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(30px); }
            100% { opacity: 1; transform: none; }
        }
        .section-title {
            font-family: 'Irish Grover', cursive;
            color: #2e7d32;
            font-size: 2.1rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.7rem;
        }
        .form-label {
            color: #2e7d32;
            font-weight: 600;
            font-size: 1.1rem;
        }
        .form-control, textarea.form-control {
            border-radius: 10px;
            border: 1.5px solid #43a047;
            background: #e8f5e9;
            font-size: 1.08rem;
            margin-bottom: 1.1rem;
        }
        .form-control:focus, textarea.form-control:focus {
            border-color: #388e3c;
            box-shadow: 0 0 0 2px #43a04733;
        }
        .input-tabs, .image-input-tabs, .content-input-tabs {
            display: flex;
            margin-bottom: 15px;
            border-radius: 7px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(56,142,60,0.10);
        }
        .input-tab, .image-input-tab, .content-input-tab {
            flex: 1;
            padding: 12px 0;
            background: #c8e6c9;
            color: #2e7d32;
            text-align: center;
            cursor: pointer;
            transition: background 0.3s, color 0.3s;
            border: none;
            font-weight: 600;
            font-size: 1.08rem;
        }
        .input-tab.active, .image-input-tab.active, .content-input-tab.active {
            background: #43a047;
            color: #fff;
        }
        .input-tab:hover, .image-input-tab:hover, .content-input-tab:hover {
            background: #388e3c;
            color: #fff;
        }
        .input-content, .image-input-content {
            display: none;
        }
        .input-content.active, .image-input-content.active {
            display: block;
            animation: fadeIn 0.5s;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .image-preview, .content-preview {
            max-width: 220px;
            max-height: 150px;
            border-radius: 10px;
            border: 2px solid #43a047;
            margin-bottom: 0.5rem;
            background: #e8f5e9;
        }
        .file-input-wrapper {
            position: relative;
            display: inline-block;
            width: 100%;
        }
        .file-input-wrapper input[type=file] {
            position: absolute;
            left: -9999px;
        }
        .file-input-label {
            display: block;
            padding: 10px 15px;
            background: #388e3c;
            color: #fff;
            border-radius: 7px;
            cursor: pointer;
            text-align: center;
            transition: background 0.3s;
            font-weight: 600;
        }
        .file-input-label:hover {
            background: #2e7d32;
        }
        .current-image-info {
            background: #c8e6c9;
            padding: 10px;
            border-radius: 7px;
            margin-bottom: 10px;
            color: #2e7d32;
            font-size: 1rem;
        }
        .url-example {
            background: #f1fdf6;
            padding: 10px;
            border-radius: 7px;
            margin-top: 10px;
            font-size: 0.98em;
            color: #2e7d32;
        }
        .url-example code {
            color: #2e7d32;
            background: #c8e6c9;
            padding: 2px 4px;
            border-radius: 3px;
        }
        .btn-success {
            background: #43a047;
            color: #fff;
            border-radius: 50px;
            font-weight: 600;
            padding: 0.7rem 2.5rem;
            font-size: 1.1rem;
            border: none;
            box-shadow: 0 2px 8px rgba(56,142,60,0.13);
            transition: background 0.2s, transform 0.2s;
        }
        .btn-success:hover {
            background: #2e7d32;
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(56,142,60,0.18);
        }
        .btn-secondary {
            border-radius: 50px;
            font-weight: 600;
            padding: 0.7rem 2.5rem;
            font-size: 1.1rem;
        }
        @media (max-width: 900px) {
            .form-container { padding: 1.2rem 0.5rem; }
        }
        @media (max-width: 600px) {
            .form-container { padding: 0.5rem 0.2rem; }
            .section-title { font-size: 1.3rem; }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-5 fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="adminpage.php"><i class="bi bi-arrow-left"></i> Admin Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>

    <div class="d-flex">
        <div class="container p-5">
            <?php if (!empty($message)) echo $message; ?>
            
            <form action="addmodule.php" method="POST" enctype="multipart/form-data" class="p-5 form-container">
                <div class="section-title mb-4"><i class="bi bi-plus-circle"></i> Add New Module</div>

                <div class="row">
                    <div class="col-md-8">
                        <label for="title" class="form-label fw-semibold fs-5 ">Module Title:</label>
                        <input type="text" class="form-control mb-3" id="title" name="title" required>

                        <label for="description" class="form-label fw-semibold fs-5 ">Description:</label>
                        <textarea class="form-control mb-3" id="description" name="description" rows="4" required></textarea>

                        <div class="row">
                            <div class="col-md-6">
                                <label for="type" class="form-label fw-semibold fs-5 ">Type:</label>
                                <input class="form-control mb-3" id="type" name="type" required>
                            </div>
                            <div class="col-md-6">
                                <label for="category" class="form-label fw-semibold fs-5 ">Category:</label>
                                <input class="form-control mb-3" id="category" name="category" required>
                            </div>
                        </div>

                        <label for="content" class="form-label fw-semibold fs-5 ">Module Content:</label>
                        <div class="content-input-tabs mb-2">
                            <button type="button" class="content-input-tab active" onclick="switchContentInput('upload')">Upload File</button>
                            <button type="button" class="content-input-tab" onclick="switchContentInput('url')">Use URL</button>
                        </div>
                        <!-- Upload File Content -->
                        <div id="content-upload-content" class="content-input-content active" style="display: block;">
                            <div class="file-input-wrapper">
                                <label for="content_file" class="file-input-label">
                                    <i class="bi bi-upload"></i> Choose Content File
                                </label>
                                <input type="file" id="content_file" name="content_file" accept=".jpeg,.jpg,.png,.pdf,.doc,.docx,.ppt,.pptx">
                            </div>
                            <p class="text-muted small mt-2">
                                <i class="bi bi-info-circle"></i> Allowed: JPEG, PNG, PDF, DOC, DOCX, PPT, PPTX<br>
                                Max size: 20MB
                            </p>
                        </div>
                        <!-- URL Input Content -->
                        <div id="content-url-content" class="content-input-content" style="display: none;">
                            <input type="url" class="form-control mb-3" id="content_url" name="content_url" placeholder="https://example.com/content or https://drive.google.com/file/d/..." >
                            <div class="url-example">
                                <i class="bi bi-info-circle"></i> <strong>Examples:</strong><br>
                                • Google Drive: <code>https://drive.google.com/file/d/YOUR_FILE_ID/view</code><br>
                                • External URL: <code>https://example.com/module-content</code>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label fw-semibold fs-5 ">Module Image:</label>
                        <!-- Image Input Tabs -->
                        <div class="image-input-tabs">
                            <button type="button" class="image-input-tab active" onclick="switchImageInput('upload')">Upload File</button>
                            <button type="button" class="image-input-tab" onclick="switchImageInput('url')">Use URL</button>
                        </div>
                        <!-- Upload File Content -->
                        <div id="upload-content" class="image-input-content active">
                            <div class="file-input-wrapper">
                                <label for="module_image" class="file-input-label">
                                    <i class="bi bi-upload"></i> Choose Image File
                                </label>
                                <input type="file" id="module_image" name="module_image" accept="image/*">
                            </div>
                            <div id="image-preview-container" class="mt-3" style="display: none;">
                                <p class="text-white mb-2"><strong>Image Preview:</strong></p>
                                <img id="image-preview" src="" alt="Image preview" class="image-preview">
                                <p id="file-info" class="text-muted small"></p>
                            </div>
                            <p class="text-muted small mt-2">
                                <i class="bi bi-info-circle"></i> 
                                Supported formats: JPG, PNG, GIF, AVIF, WebP<br>
                                Maximum size: 5MB
                            </p>
                        </div>
                        <!-- URL Input Content -->
                        <div id="url-content" class="image-input-content">
                            <input type="url" class="form-control mb-3" id="image_url" name="image_url" placeholder="https://example.com/image.jpg">
                            <div id="url-preview-container" class="mt-3" style="display: none;">
                                <p class=" mb-2"><strong>URL Image Preview:</strong></p>
                                <img id="url-preview" src="" alt="URL image preview" class="image-preview">
                                <p id="url-info" class="text-muted small"></p>
                            </div>
                            <div class="url-example">
                                <i class="bi bi-info-circle"></i> <strong>Examples:</strong><br>
                                • Google Drive: <code>https://drive.google.com/uc?export=view&id=YOUR_FILE_ID</code><br>
                                • Imgur: <code>https://i.imgur.com/example.jpg</code><br>
                                • Any direct image URL
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-success me-2">
                        <i class="bi bi-plus-circle"></i> Add Module
                    </button>
                    <a href="adminpage.php#module-management" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Admin
                    </a>
                </div>
            </form>
        </div>
    </div>

    <footer class="footer bg-dark">
        <p>&copy; 2024 Teen-Anim. All rights reserved.</p>
    </footer>

    <script>
        // Switch between upload and URL input for image
        function switchImageInput(type) {
            const tabs = document.querySelectorAll('.image-input-tab');
            tabs.forEach(tab => tab.classList.remove('active'));
            if (type === 'upload') {
                tabs[0].classList.add('active');
                document.getElementById('upload-content').classList.add('active');
                document.getElementById('upload-content').style.display = 'block';
                document.getElementById('url-content').classList.remove('active');
                document.getElementById('url-content').style.display = 'none';
            } else {
                tabs[1].classList.add('active');
                document.getElementById('upload-content').classList.remove('active');
                document.getElementById('upload-content').style.display = 'none';
                document.getElementById('url-content').classList.add('active');
                document.getElementById('url-content').style.display = 'block';
            }
        }
        // Switch between upload and URL input for content
        function switchContentInput(type) {
            const tabs = document.querySelectorAll('.content-input-tab');
            tabs.forEach(tab => tab.classList.remove('active'));
            if (type === 'upload') {
                tabs[0].classList.add('active');
                document.getElementById('content-upload-content').classList.add('active');
                document.getElementById('content-upload-content').style.display = 'block';
                document.getElementById('content-url-content').classList.remove('active');
                document.getElementById('content-url-content').style.display = 'none';
            } else {
                tabs[1].classList.add('active');
                document.getElementById('content-upload-content').classList.remove('active');
                document.getElementById('content-upload-content').style.display = 'none';
                document.getElementById('content-url-content').classList.add('active');
                document.getElementById('content-url-content').style.display = 'block';
            }
        }
        
        // Image preview functionality for file upload
        document.getElementById('module_image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const previewContainer = document.getElementById('image-preview-container');
            const preview = document.getElementById('image-preview');
            const fileInfo = document.getElementById('file-info');
            
            if (file) {
                // Show file info
                const fileSize = (file.size / 1024 / 1024).toFixed(2);
                fileInfo.textContent = `${file.name} (${fileSize} MB)`;
                
                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewContainer.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                previewContainer.style.display = 'none';
            }
        });
        
        // URL preview functionality
        document.getElementById('image_url').addEventListener('input', function(e) {
            const url = e.target.value;
            const previewContainer = document.getElementById('url-preview-container');
            const preview = document.getElementById('url-preview');
            const urlInfo = document.getElementById('url-info');
            
            if (url && isValidUrl(url)) {
                urlInfo.textContent = `URL: ${url}`;
                preview.src = url;
                previewContainer.style.display = 'block';
                
                // Handle image load error
                preview.onerror = function() {
                    urlInfo.textContent = `URL: ${url} (Image not accessible)`;
                    preview.style.display = 'none';
                };
                preview.onload = function() {
                    preview.style.display = 'block';
                };
            } else {
                previewContainer.style.display = 'none';
            }
        });
        
        // URL validation function
        function isValidUrl(string) {
            try {
                new URL(string);
                return true;
            } catch (_) {
                return false;
            }
        }
    </script>
</body>
</html>
