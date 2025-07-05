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

    $favorite_sql = "SELECT p.* FROM plant p JOIN favorites f ON p.plant_id = f.plant_id WHERE f.user_id = {$_SESSION['user_id']}";
    $favorite_result = $conn->query($favorite_sql);

    $active_section = isset($_GET['section']) ? $_GET['section'] : 'profile'; 

    $profile_pic = (empty($user['profile_picture']) || $user['profile_picture'] === 'clearteenalogo.png') 
        ? '../images/clearteenalogo.png' 
        : '../images/profile_pics/' . htmlspecialchars($user['profile_picture']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/homepage.css">
    <style>
        body {
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
        }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #43a047 0%, #66bb6a 100%);
            color: #fff;
            padding-top: 2rem;
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            z-index: 100;
            box-shadow: 2px 0 12px rgba(60,120,60,0.08);
            margin-top: 80px;
        }
        .sidebar .avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #fff;
            margin-bottom: 1rem;
        }
        .sidebar .username {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
        .sidebar .nav-link {
            color: #fff;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
            border-radius: 30px;
            padding: 0.7rem 1.2rem;
            transition: background 0.2s, color 0.2s;
            display: flex;
            align-items: center;
            gap: 0.7rem;
        }
        .sidebar .nav-link.active, .sidebar .nav-link:hover {
            background: #fff;
            color: #388e3c !important;
        }
        .main-content {
            margin-left: 270px;
            padding: 2rem 1rem 1rem 1rem;
        }
        @media (max-width: 991.98px) {
            .sidebar {
                position: static;
                width: 100%;
                min-height: auto;
                display: flex;
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
                padding: 1rem 1rem 0.5rem 1rem;
            }
            .sidebar .avatar {
                width: 50px;
                height: 50px;
                margin-bottom: 0;
            }
            .sidebar .username {
                margin-bottom: 0;
            }
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
        }
        .card {
            border-radius: 1rem;
            box-shadow: 0 4px 24px rgba(76,175,80,0.08);
        }
        .favorite-plant-card {
            min-width: 220px;
            max-width: 250px;
            margin: 0.5rem;
            display: inline-block;
            vertical-align: top;
        }
        .favorite-plant-img {
            height: 120px;
            object-fit: cover;
            border-radius: 0.7rem 0.7rem 0 0;
        }
        .remove-fav-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #e53935;
            color: #fff;
            border-radius: 50%;
            border: none;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            transition: background 0.2s;
        }
        .remove-fav-btn:hover {
            background: #b71c1c;
        }
        /* Responsive profile card and content */
        @media (max-width: 700px) {
            .main-content {
                padding: 0.5rem;
            }
            .card {
                border-radius: 0.7rem;
                padding: 0.5rem;
            }
            .card-header {
                flex-direction: column;
                align-items: flex-start !important;
                padding: 1rem 1rem 0.5rem 1rem;
            }
            .d-flex.align-items-center.mb-3 {
                flex-direction: column !important;
                align-items: center !important;
                text-align: center;
            }
            .d-flex.align-items-center.mb-3 img.avatar {
                width: 120px;
                height: 120px;
                margin-bottom: 1rem;
            }
            .d-flex.align-items-center.mb-3 > div {
                margin-left: 0 !important;
            }
            .accordion-item, .accordion-button, .accordion-body {
                font-size: 1rem;
            }
            .sidebar {
                flex-direction: column;
                align-items: flex-start;
                padding: 0.5rem 1rem;
            }
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="sidebar d-flex flex-column align-items-center">
    <img src="<?php echo $profile_pic; ?>" class="avatar profile-picture" alt="User Avatar" onerror="this.onerror=null;this.src='../images/clearteenalogo.png';">
    <div class="username">Hi, <?php echo htmlspecialchars($user['name']); ?>!</div>
    <nav class="nav flex-column w-100 mt-4">
        <a class="nav-link <?php echo ($active_section == 'profile') ? 'active' : ''; ?>" href="?section=profile"><i class="bi bi-person-circle"></i> Profile</a>
        <a class="nav-link <?php echo ($active_section == 'settings') ? 'active' : ''; ?>" href="?section=settings"><i class="bi bi-gear"></i> Settings</a>
        <a class="nav-link <?php echo ($active_section == 'favorites') ? 'active' : ''; ?>" href="?section=favorites"><i class="bi bi-heart"></i> Favorites</a>
        <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </nav>
</div>
<div class="main-content">
    <div class="container">
        <?php if ($active_section == 'profile'): ?>
            <div class="card shadow-sm mb-4" data-aos="fade-up">
                <div class="card-header bg-success text-white d-flex align-items-center justify-content-between">
                    <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i>Your Profile</h5>
                    <button class="btn btn-outline-light btn-sm" data-bs-toggle="modal" data-bs-target="#editProfileModal"><i class="bi bi-pencil"></i> Edit</button>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <img src="<?php echo $profile_pic; ?>" class="avatar me-3 profile-picture" alt="User Avatar" onerror="this.onerror=null;this.src='../images/clearteenalogo.png';">
                        <div>
                            <h4 class="mb-0"><?php echo htmlspecialchars($user['name']); ?></h4>
                            <div style="font-size: 1.05em; color: #388e3c; font-weight: 500; margin-bottom: 2px;">
                                <?php echo htmlspecialchars(ucfirst($user['role'])); ?>
                            </div>
                            <div class="text-muted">@<?php echo htmlspecialchars($user['username']); ?></div>
                            <button class="btn btn-outline-secondary btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#uploadProfilePicModal">
                                <i class="bi bi-upload"></i> Change Picture
                            </button>
                        </div>
                    </div>
                    <hr>
                    <h5 class="mb-3">Your Questions in the Farming Community</h5>
                    <?php if ($question_result->num_rows > 0): ?>
                        <div class="accordion mb-3" id="questionsAccordion">
                            <?php $qidx = 0; while ($question = $question_result->fetch_assoc()): ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="qheading<?php echo $qidx; ?>">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#qcollapse<?php echo $qidx; ?>" aria-expanded="false" aria-controls="qcollapse<?php echo $qidx; ?>">
                                            <?php echo htmlspecialchars($question['title']); ?>
                                        </button>
                                    </h2>
                                    <div id="qcollapse<?php echo $qidx; ?>" class="accordion-collapse collapse" aria-labelledby="qheading<?php echo $qidx; ?>" data-bs-parent="#questionsAccordion">
                                        <div class="accordion-body">
                                            <p><?php echo htmlspecialchars($question['body']); ?></p>
                                            <small class="text-muted">Asked on: <?php echo $question['created_at']; ?></small>
                                        </div>
                                    </div>
                                </div>
                            <?php $qidx++; endwhile; ?>
                        </div>
                    <?php else: ?>
                        <p>No questions found.</p>
                    <?php endif; ?>
                    <hr>
                    <h5 class="mb-3">Your Replies in the Farming Community</h5>
                    <?php if ($reply_result->num_rows > 0): ?>
                        <div class="accordion" id="repliesAccordion">
                            <?php $ridx = 0; while ($reply = $reply_result->fetch_assoc()): ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="rheading<?php echo $ridx; ?>">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#rcollapse<?php echo $ridx; ?>" aria-expanded="false" aria-controls="rcollapse<?php echo $ridx; ?>">
                                            In: <?php echo htmlspecialchars($reply['title']); ?>
                                        </button>
                                    </h2>
                                    <div id="rcollapse<?php echo $ridx; ?>" class="accordion-collapse collapse" aria-labelledby="rheading<?php echo $ridx; ?>" data-bs-parent="#repliesAccordion">
                                        <div class="accordion-body">
                                            <p><?php echo htmlspecialchars($reply['body']); ?></p>
                                            <small class="text-muted">Replied on: <?php echo $reply['created_at']; ?></small>
                                        </div>
                                    </div>
                                </div>
                            <?php $ridx++; endwhile; ?>
                        </div>
                    <?php else: ?>
                        <p>No replies found.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php elseif ($active_section == 'settings'): ?>
            <div class="card shadow-sm mb-4" data-aos="fade-up">
                <div class="card-header bg-success text-white">
                    <h5><i class="bi bi-gear me-2"></i>Account Settings</h5>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs mb-3" id="settingsTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="true">Update Profile</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab" aria-controls="password" aria-selected="false">Change Password</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="settingsTabContent">
                        <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                            <form action="User/editprofile.php" method="POST">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>">
                                </div>
                                <button type="submit" class="btn btn-success">Update Profile</button>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
                            <form action="User/changepassword.php" method="POST">
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password">
                                </div>
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password">
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                                </div>
                                <button type="submit" class="btn btn-primary" name="change_password">Change Password</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php elseif ($active_section == 'favorites'): ?>
            <div class="card shadow-sm mb-4" data-aos="fade-up">
                <div class="card-header bg-success text-white d-flex align-items-center">
                    <h5 class="mb-0"><i class="bi bi-heart me-2"></i>Your Favorite Plants</h5>
                </div>
                <div class="card-body">
                    <?php if ($favorite_result && $favorite_result->num_rows > 0): ?>
                        <div class="d-flex flex-wrap">
                            <?php while ($plant = $favorite_result->fetch_assoc()): ?>
                                <div class="card favorite-plant-card position-relative" data-plant-id="<?php echo $plant['plant_id']; ?>">
                                    <img src="../images/<?php echo htmlspecialchars($plant['image']) ? htmlspecialchars($plant['image']) : 'default-plant.png'; ?>" class="favorite-plant-img card-img-top" alt="<?php echo htmlspecialchars($plant['name']); ?>" onerror="this.onerror=null;this.src='../images/default-plant.png';">
                                    <div class="card-body">
                                        <h6 class="card-title mb-1"><?php echo htmlspecialchars($plant['name']); ?></h6>
                                        <button class="remove-fav-btn" title="Remove from Favorites"><i class="bi bi-x"></i></button>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <p>No favorite plants yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="User/editprofile.php" method="POST">
        <div class="modal-body">
          <div class="mb-3">
            <label for="modal_name" class="form-label">Name</label>
            <input type="text" class="form-control" id="modal_name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>">
          </div>
          <div class="mb-3">
            <label for="modal_username" class="form-label">Username</label>
            <input type="text" class="form-control" id="modal_username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Upload Profile Picture Modal -->
<div class="modal fade" id="uploadProfilePicModal" tabindex="-1" aria-labelledby="uploadProfilePicModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" action="User/upload_profile_picture.php" method="POST" enctype="multipart/form-data">
      <div class="modal-header">
        <h5 class="modal-title" id="uploadProfilePicModalLabel">Upload Profile Picture</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="file" class="form-control" name="profile_picture" accept="image/*" required>
        <small class="text-muted">Max size: 2MB. Allowed: JPG, PNG, GIF.</small>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-success">Upload</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
AOS.init();

// AJAX remove favorite
$(function() {
    $('.remove-fav-btn').on('click', function() {
        var card = $(this).closest('.favorite-plant-card');
        var plantId = card.data('plant-id');
        var container = card.parent();
        $.ajax({
            url: 'remove_favorite.php',
            method: 'POST',
            data: { plant_id: plantId },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    card.fadeOut(300, function() {
                        $(this).remove();
                        if (container.find('.favorite-plant-card').length === 0) {
                            container.parent().append('<p>No favorite plants yet.</p>');
                        }
                    });
                } else {
                    alert(response.message || 'Failed to remove favorite.');
                }
            },
            error: function() {
                alert('Failed to remove favorite.');
            }
        });
    });
});
</script>
</body>
</html>
