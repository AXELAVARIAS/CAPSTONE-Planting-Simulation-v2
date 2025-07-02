<?php
    include '../connection.php';

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    $message = ""; 

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $content = mysqli_real_escape_string($conn, $_POST['content']);

        if (!empty($title) && !empty($description) && !empty($content)) {
            $sql = "INSERT INTO modules (title, description, content) VALUES ('$title','$description','$content')";

            if ($conn->query($sql) === TRUE) {
                $message = "<h3 class='text-success text-center mt-5'>New module added successfully!</h3>";
            } else {
                $message = "<h3 class='text-danger text-center mt-5'>Failed to add new module</h3>";
            }
        } else {
            $message = "<h3 class='text-warning text-center mt-5'>Please fill all the fields</h3>";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            overflow: hidden;
        }

        .d-flex {
            height: 100%;
            justify-content: center;
            align-items: center;
        }


        .footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            color: white;
            text-align: center;
            padding: 10px 0;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-5 fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="adminpage.php">Admin Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>

    <div class="d-flex">
        <div class="container p-5">
            <?php if (!empty($message)) echo $message; ?>
            <form action="addmodule.php" method="POST" class=" p-5 bg-dark ">
                <h2 class="fs-3 mb-4 text-white">Enter Module Details:</h2>

                <label for="title" class="form-label fw-semibold fs-5 text-white">Module Title:</label>
                <input type="text" class="form-control mb-3" id="title" name="title">

                <label for="description" class="form-label fw-semibold fs-5 text-white">Description:</label>
                <textarea class="form-control mb-3" id="description" name="description" rows="5"></textarea>

                <label for="content" class="form-label fw-semibold fs-5 text-white">Content:</label>
                <input type="text" class="form-control mb-3" id="content" name="content">

                <input type="submit" value="Add Module" class="btn btn-light mt-3">
                <a href="adminpage.php#module-management" class="btn btn-light mt-3">Back</a>
            </form>
        </div>
    </div>

    <footer class="footer bg-dark">
        <p>&copy; 2024 Teen-Anim. All rights reserved.</p>
    </footer>
</body>
</html>
