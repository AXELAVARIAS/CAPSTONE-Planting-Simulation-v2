<?php
   
    session_start();
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: 0");
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    include '../connection.php';

    $sql = "SELECT * FROM users WHERE user_id = {$_SESSION['user_id']}";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        echo "User details not found.";
        exit();
    }

    $question_sql = "SELECT * FROM questions WHERE user_id = {$_SESSION['user_id']} ORDER BY created_at DESC";
    $question_result = $conn->query($question_sql);

    $reply_sql = "SELECT r.*, q.title FROM reply r
                  JOIN questions q ON r.question_id = q.question_id
                  WHERE r.user_id = {$_SESSION['user_id']} ORDER BY r.created_at DESC";
    $reply_result = $conn->query($reply_sql);


    $active_section = isset($_GET['section']) ? $_GET['section'] : 'profile';

    if (isset($_POST['add_plant'])) {
        $plant_name = $_POST['plant_name'];
        $plant_type = $_POST['plant_type'];
        $plant_description = $_POST['plant_description'];

        $insert_plant_sql = "INSERT INTO plants (user_id, name, type, description) VALUES (?, ?, ?, ?)";
        
        if ($stmt = $conn->prepare($insert_plant_sql)) {
            $stmt->bind_param("ssss", $_SESSION['user_id'], $plant_name, $plant_type, $plant_description);
            $stmt->execute();
            $stmt->close();
            header("Location: agriculturistpage.php?section=add-plant&status=success");
            exit();
        } else {
            echo "Error adding plant.";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agriculturist Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../css/homepage.css">
    <style>
      body {
        background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
        min-height: 100vh;
        font-family: 'Poppins', sans-serif;
      }
      @media (max-width: 768px) {
        .admin-header, .admin-nav, .content {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }
        .admin-header {
            flex-direction: column;
            align-items: flex-start;
            font-size: 1rem;
            padding: 0.5rem 1rem;
        }
        .admin-header .logo img {
            height: 28px;
            width: 28px;
        }
        .admin-nav {
            flex-direction: column;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
        }
        .admin-nav .nav-link {
            width: 100%;
            justify-content: flex-start;
            font-size: 1rem;
            padding: 0.5rem 0.75rem;
        }
        .section-title {
            font-size: 1.1rem;
        }
        .content {
            padding: 1rem 0.2rem 1rem 0.2rem;
        }
        .card {
            padding: 1rem !important;
        }
        .table-responsive {
            font-size: 0.95rem;
        }
    }
    </style>
</head>
<body>
<?php include '../navbar.php'; ?>
<div class="agri-sidebar">
    <img src="../../images/clearteenalogo.png" class="avatar" alt="User Avatar">
    <div class="welcome teenanim">Hi, <?php echo htmlspecialchars($user['name']); ?>!</div>
    <nav class="nav flex-column w-100 mt-2">
        <a class="nav-link<?php echo ($active_section == 'profile') ? ' active' : ''; ?>" href="?section=profile"><i class="bi bi-person-circle"></i> Profile</a>
        <a class="nav-link<?php echo ($active_section == 'settings') ? ' active' : ''; ?>" href="?section=settings"><i class="bi bi-gear"></i> Settings</a>
        <a class="nav-link" href="../Forum/community.php"><i class="bi bi-people"></i> Farming Community</a>
        <a class="nav-link<?php echo ($active_section == 'add-plant') ? ' active' : ''; ?>" href="?section=add-plant"><i class="bi bi-plus-circle"></i> Add Plant</a>
        <a class="nav-link" href="../logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </nav>
</div>
<div class="agri-main-content d-flex justify-content-center">
    <div style="max-width: 900px; width: 100%;">
        <?php if ($active_section == 'profile'): ?>
            <div class="agri-card mb-4">
                <div class="agri-card-header">Your Profile</div>
                <div>
                    <h5>Profile Information</h5>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                    <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                </div>
            </div>
            <div class="agri-card mb-2">
                <div class="agri-card-header">Your Replies in the Farming Community</div>
                <div>
                    <?php if ($reply_result->num_rows > 0): ?>
                        <?php while ($reply = $reply_result->fetch_assoc()): ?>
                            <div class="agri-reply-card">
                                <div class="agri-reply-title">
                                    In <a href="../Forum/thread.php?id=<?php echo $reply['question_id']; ?>" class="text-decoration-none text-success fw-bold"><?php echo htmlspecialchars($reply['title']); ?></a>
                                </div>
                                <div class="agri-reply-body"><?php echo htmlspecialchars($reply['body']); ?></div>
                                <div class="agri-reply-date">Replied on: <?php echo date('M d, Y', strtotime($reply['created_at'])); ?></div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No replies found.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php elseif ($active_section == 'settings'): ?>
            <div class="agri-card mb-4">
                <div class="agri-card-header">Account Settings</div>
                <div>
                    <div class="agri-tabs mb-3">
                        <button class="agri-tab-btn active" id="tab-profile" type="button" onclick="showAgriTab('profile')">Update Profile</button>
                        <button class="agri-tab-btn" id="tab-password" type="button" onclick="showAgriTab('password')">Change Password</button>
                    </div>
                    <div id="agri-tab-profile">
                        <form action="User/editprofile.php" method="POST">
                            <div class="mb-3">
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required placeholder=" ">
                                <label for="name" class="form-label">Name</label>
                            </div>
                            <div class="mb-3">
                                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required placeholder=" ">
                                <label for="username" class="form-label">Username</label>
                            </div>
                            <button type="submit" class="btn btn-success">Update Profile</button>
                        </form>
                    </div>
                    <div id="agri-tab-password" style="display:none;">
                        <form action="User/changepassword.php" method="POST">
                            <div class="mb-3">
                                <div class="input-group">
                                    <input type="password" class="form-control" id="current_password" name="current_password" required placeholder=" ">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')"><i class="bi bi-eye"></i></button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="input-group">
                                    <input type="password" class="form-control" id="new_password" name="new_password" required placeholder=" ">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password')"><i class="bi bi-eye"></i></button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required placeholder=" ">
                                    <label for="confirm_password" class="form-label">Confirm Password</label>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirm_password')"><i class="bi bi-eye"></i></button>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary" name="change_password">Change Password</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php elseif ($active_section == 'add-plant'): ?>
            <form action="add_plant.php" method="POST" enctype="multipart/form-data" class="agri-add-plant-form mt-5">
                <h2 class="fs-3 mb-3 text-white">Enter Plant Details:</h2>
                <input type="text" class="form-control mb-3" id="name" name="name" required placeholder=" ">
                <label for="name" class="form-label fw-semibold fs-5 text-white">Plant Name:</label>
                <textarea class="form-control mb-3" id="description" name="description" rows="3" required placeholder=" "></textarea>
                <label for="description" class="form-label fw-semibold fs-5 text-white">Description:</label>
                <input type="file" class="form-control mb-3" id="image" name="image" accept="image/*" required onchange="previewAgriImage(event)">
                <img id="agri-image-preview" class="agri-image-preview" style="display:none;" alt="Image Preview" />
                <input type="text" class="form-control mb-3" id="container_soil" name="container_soil" required placeholder=" ">
                <label for="container_soil" class="form-label fw-semibold fs-5 text-white">Container & Soil:</label>
                <input type="text" class="form-control mb-3" id="watering" name="watering" required placeholder=" ">
                <label for="watering" class="form-label fw-semibold fs-5 text-white">Watering:</label>
                <input type="text" class="form-control mb-3" id="sunlight" name="sunlight" required placeholder=" ">
                <label for="sunlight" class="form-label fw-semibold fs-5 text-white">Sunlight:</label>
                <input type="text" class="form-control mb-3" id="tips" name="tips" required placeholder=" ">
                <label for="tips" class="form-label fw-semibold fs-5 text-white">Tips:</label>
                <input type="submit" value="Add Plant" class="btn btn-light mt-3">
                <a href="adminpage.php#plantinder-management" class="btn btn-light mt-3">Back</a>
            </form>
        <?php endif; ?>
    </div>
</div>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<script>
// Tabs for settings
function showAgriTab(tab) {
  document.getElementById('agri-tab-profile').style.display = (tab === 'profile') ? '' : 'none';
  document.getElementById('agri-tab-password').style.display = (tab === 'password') ? '' : 'none';
  document.getElementById('tab-profile').classList.toggle('active', tab === 'profile');
  document.getElementById('tab-password').classList.toggle('active', tab === 'password');
}
// Show/hide password
function togglePassword(id) {
  var input = document.getElementById(id);
  if (input.type === 'password') {
    input.type = 'text';
  } else {
    input.type = 'password';
  }
}
// Image preview for add plant
function previewAgriImage(event) {
  var output = document.getElementById('agri-image-preview');
  output.src = URL.createObjectURL(event.target.files[0]);
  output.style.display = 'block';
}
</script>
</body>
</html>
