<?php
    include '../connection.php';

    if (isset($_GET['id'])){
        $id =$_GET['id'];

        $sql = "SELECT * FROM modules WHERE module_id = $id";
        $result=$conn->query($sql);

        if ($result->num_rows > 0){
            $row = $result->fetch_assoc();
            $title = $row['title'];
            $description = $row['description'];
            $type = $row['type'];
            $category = $row['category'];
            $content = $row['content'];
            $image_path = $row['image_path'];
        }
        else{
            echo "No module found";
        }
    }

    if($_SERVER["REQUEST_METHOD"]=="POST"){
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $type = mysqli_real_escape_string($conn, $_POST['type']);
        $category = mysqli_real_escape_string($conn, $_POST['category']);
        $content = mysqli_real_escape_string($conn, $_POST['content']);
        $image_path = mysqli_real_escape_string($conn, $_POST['image_path']);

        $id = $_POST['id'];

        if(!empty($title)&& !empty($description)&& !empty($content)&& !empty($image_path) && !empty($type) && !empty($category)){
            $sql ="UPDATE modules SET title='$title', description='$description', content= '$content', type= '$type', category= '$category', image_path= '$image_path', updated_at=CURRENT_TIMESTAMP  WHERE module_id='$id'";
            
            if($conn->query($sql)=== TRUE){
                echo "<h1 class='text-center fs-3 mt-5'>Modules updated sucessfully!</h1>";
                header("Location: adminpage.php#module-management");
            }
            else{
                echo "<h1 class='text-center fs-3 mt-5'>Update Failed: " . mysqli_error($conn) . "</h1>";
            }
        }
        else{
            echo "<h1 class='text-center fs-3 '>Fill all the fields!</h1>";
        }
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit</title>
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
            <form action="editmodule.php" method="POST" class=" p-5 bg-dark ">
                <h2 class="fs-3 mb-4 text-white">Enter Module Details</h2>

                <input type="hidden" name="id" value="<?php echo $id; ?>">

                <label for="title" class="form-label fw-semibold fs-5 text-white">Module Title:</label>
                <input type="text" class="form-control mb-3" id="title" name="title" value="<?php echo $title;?>">

                <label for="description" class="form-label fw-semibold fs-5 text-white">Description:</label>
                <textarea class="form-control mb-3" id="description" name="description" rows="5"><?php echo $description;?></textarea>

                <label for="type" class="form-label fw-semibold fs-5 text-white">Type:</label>
                <input class="form-control mb-3" id="type" name="type" value="<?php echo $type;?>">

                <label for="category" class="form-label fw-semibold fs-5 text-white">Category:</label>
                <input class="form-control mb-3" id="category" name="category" value="<?php echo $category;?>">

                <label for="content" class="form-label fw-semibold fs-5 text-white">Content:</label>
                <input type="text" class="form-control mb-3" id="content" name="content" value="<?php echo $content;?>">

                <label for="image_path" class="form-label fw-semibold fs-5 text-white">Image Path:</label>
                <input type="text" class="form-control mb-3" id="image_path" name="image_path" value="<?php echo $image_path;?>">

                <input type="submit" value="Edit" class="btn btn-light mt-3">
                <a href="adminpage.php" class="btn btn-light mt-3">Back</a>
            </form>
        </div>
    </div>
</body>
</html>
