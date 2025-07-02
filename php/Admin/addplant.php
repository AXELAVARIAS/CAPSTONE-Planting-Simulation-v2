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
            <form action="add_plant.php" method="POST" enctype="multipart/form-data" class="p-3 bg-success rounded-3 mt-5">
                <h2 class="fs-3 mb-4 text-white">Enter Plant Details:</h2>

                <label for="name" class="form-label fw-semibold fs-5 text-white">Plant Name:</label>
                <input type="text" class="form-control mb-3" id="name" name="name" required>

                <label for="description" class="form-label fw-semibold fs-5 text-white">Description:</label>
                <textarea class="form-control mb-3" id="description" name="description" rows="3" required></textarea>

                <label for="image" class="form-label fw-semibold fs-5 text-white">Image:</label>
                <input type="file" class="form-control mb-3" id="image" name="image" accept="image/*" required>

                <label for="container_soil" class="form-label fw-semibold fs-5 text-white">Container & Soil:</label>
                <input type="text" class="form-control mb-3" id="container_soil" name="container_soil" required>

                <label for="watering" class="form-label fw-semibold fs-5 text-white">Watering:</label>
                <input type="text" class="form-control mb-3" id="watering" name="watering" required>

                <label for="sunlight" class="form-label fw-semibold fs-5 text-white">Sunlight:</label>
                <input type="text" class="form-control mb-3" id="sunlight" name="sunlight" required>

                <label for="tips" class="form-label fw-semibold fs-5 text-white">Tips:</label>
                <input type="text" class="form-control mb-3" id="tips" name="tips" required>

                <input type="submit" value="Add Plant" class="btn btn-light mt-3">
                <a href="adminpage.php#plantinder-management" class="btn btn-light mt-3">Back</a>
            </form>
        </div>
    </div>

</body>
</html>
