<?php
    include 'connection.php';
    session_start();
    // Prevent caching
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    $Type = isset($_POST['Type']) ? $conn->real_escape_string($_POST['Type']) : '';
    $Category = isset($_POST['Category']) ? $conn->real_escape_string($_POST['Category']) : '';
    $sortOption = isset($_POST['sortOption']) ? $conn->real_escape_string($_POST['sortOption']) : '';

    $sql = "SELECT * FROM modules WHERE 1=1";

    if ($Type != '') {
        $sql .= " AND type = '$Type'";
    }
    if ($Category != '') {
        $sql .= " AND category = '$Category'";
    }
    if ($sortOption != '') {
        if ($sortOption == 'title') {
            $sql .= " ORDER BY title ASC";
        } elseif ($sortOption == 'date') {
            $sql .= " ORDER BY created_at DESC";
        }
    }

    $result = $conn->query($sql);

    if ($result === false) {
        die("Error: " . $conn->error);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modules</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/homepage.css">
    <style>
        body {
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
        }
        .module-sidebar {
            background: linear-gradient(135deg, #43a047 0%, #66bb6a 100%);
            color: #fff;
            border-radius: 0.7rem;
            padding: 1rem 0.5rem;
            min-height: 100vh;
            box-shadow: 0 4px 24px rgba(76,175,80,0.08);
            font-size: 0.97rem;
        }
        .module-sidebar h4 {
            font-size: 1.2rem;
            margin-bottom: 1.2rem;
        }
        .module-sidebar .form-label {
            color: #fff;
            font-size: 0.98rem;
            margin-bottom: 0.2rem;
        }
        .module-sidebar .form-select {
            border-radius: 1.2rem;
            border: none;
            margin-bottom: 0.7rem;
            font-size: 0.97rem;
            padding: 0.35rem 1.5rem 0.35rem 1rem;
            min-width: 120px;
        }
        .module-card {
            border-radius: 1rem;
            box-shadow: 0 4px 24px rgba(76,175,80,0.10);
            transition: transform 0.18s, box-shadow 0.18s;
            overflow: hidden;
            background: #fff;
            min-height: 420px;
        }
        .module-card:hover {
            transform: translateY(-8px) scale(1.03);
            box-shadow: 0 8px 32px rgba(76,175,80,0.18);
        }
        .module-card-img {
            height: 180px;
            object-fit: cover;
            border-radius: 1rem 1rem 0 0;
        }
        .badge-type {
            background: #388e3c;
            color: #fff;
            font-size: 0.9rem;
            border-radius: 0.5rem;
            margin-right: 0.5rem;
        }
        .badge-category {
            background: #a5d6a7;
            color: #256029;
            font-size: 0.9rem;
            border-radius: 0.5rem;
        }
        .module-card-title {
            font-weight: 700;
            color: #388e3c;
        }
        .module-card-desc {
            color: #444;
            min-height: 60px;
        }
        .view-btn {
            background: #43a047;
            color: #fff;
            border-radius: 2rem;
            font-weight: 600;
            transition: background 0.2s;
        }
        .view-btn:hover {
            background: #256029;
            color: #fff;
        }
        @media (max-width: 991.98px) {
            .module-sidebar {
                min-height: auto;
                margin-bottom: 2rem;
            }
        }
        .filter-bar {
            background: linear-gradient(135deg, #43a047 0%, #66bb6a 100%);
            border-radius: 0.7rem;
            padding: 1rem 1.5rem 0.5rem 1.5rem;
            box-shadow: 0 4px 24px rgba(76,175,80,0.08);
        }
        @media (max-width: 991.98px) {
            .filter-bar .row > div {
                margin-bottom: 0.7rem;
            }
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container py-4">
    <form method="POST" action="modulepage.php" id="filterForm" class="filter-bar mb-4">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label for="Type" class="form-label mb-1">Module Type</label>
                <select id="Type" class="form-select" name="Type" onchange="document.getElementById('filterForm').submit()">
                    <option value="">All</option>
                    <option value="Concept/Overview" <?= $Type == 'Concept/Overview' ? 'selected' : '' ?>>Concept/Overview</option>
                    <option value="Practical/Hands-on" <?= $Type == 'Practical/Hands-on' ? 'selected' : '' ?>>Practical/Hands-on</option>
                    <option value="Strategy/Planning" <?= $Type == 'Strategy/Planning' ? 'selected' : '' ?>>Strategy/Planning</option>
                    <option value="Management/Prevention" <?= $Type == 'Management/Prevention' ? 'selected' : '' ?>>Management/Prevention</option>
                    <option value="Community/Social" <?= $Type == 'Community/Social' ? 'selected' : '' ?>>Community/Social</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="Category" class="form-label mb-1">Category</label>
                <select id="Category" class="form-select" name="Category" onchange="document.getElementById('filterForm').submit()">
                    <option value="">All</option>
                    <option value="Urban Agriculture Fundamentals" <?= $Category == 'Urban Agriculture Fundamentals' ? 'selected' : '' ?>>Urban Agriculture Fundamentals</option>
                    <option value="Design and Planning" <?= $Category == 'Design and Planning' ? 'selected' : '' ?>>Design and Planning</option>
                    <option value="Techniques and Practices" <?= $Category == 'Techniques and Practices' ? 'selected' : '' ?>>Techniques and Practices</option>
                    <option value="Management/Prevention" <?= $Category == 'Management/Prevention' ? 'selected' : '' ?>>Management/Prevention</option>
                    <option value="Strategy/Planning" <?= $Category == 'Strategy/Planning' ? 'selected' : '' ?>>Strategy/Planning</option>
                    <option value="Community/Social" <?= $Category == 'Community/Social' ? 'selected' : '' ?>>Community/Social</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="sortOptions" class="form-label mb-1">Sort by</label>
                <select id="sortOptions" class="form-select w-auto" name="sortOption" onchange="document.getElementById('filterForm').submit()">
                    <option value="">Sort by</option>
                    <option value="title" <?= $sortOption == 'title' ? 'selected' : '' ?>>Title</option>
                </select>
            </div>
        </div>
    </form>
    <div class="row g-4">
        <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="col-md-6 col-lg-4" data-aos="fade-up">';
                    echo '<div class="card module-card h-100">';
                    // Handle image display for both local files and external URLs
                    $image_path = $row['image_path'];
                    if (filter_var($image_path, FILTER_VALIDATE_URL)) {
                        // External URL - use directly
                        echo '<img src="' . htmlspecialchars($image_path) . '" class="module-card-img card-img-top" alt="' . htmlspecialchars($row['title']) . '" onerror="this.src=\'../images/default-module.jpg\'; this.onerror=null;">';
                    } else {
                        // Local file - add proper path
                        echo '<img src="' . htmlspecialchars($image_path) . '" class="module-card-img card-img-top" alt="' . htmlspecialchars($row['title']) . '" onerror="this.src=\'../images/default-module.jpg\'; this.onerror=null;">';
                    }
                    echo '<div class="card-body d-flex flex-column">';
                    echo '<div class="mb-2">';
                    echo '<span class="badge badge-type">' . htmlspecialchars($row['type']) . '</span>';
                    echo '<span class="badge badge-category">' . htmlspecialchars($row['category']) . '</span>';
                    echo '</div>';
                    echo '<h5 class="module-card-title">' . htmlspecialchars($row['title']) . '</h5>';
                    echo '<p class="module-card-desc flex-grow-1">' . htmlspecialchars($row['description']) . '</p>';
                    echo '<a href="module_viewer.php?id=' . $row['module_id'] . '" class="btn view-btn mt-auto"><i class="bi bi-book me-1"></i>Start Learning</a>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo "<div class='col-12'><h3 class='text-center text-muted mt-5'>No modules found.</h3></div>";
            }
        ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
AOS.init();
</script>
</body>
</html>
