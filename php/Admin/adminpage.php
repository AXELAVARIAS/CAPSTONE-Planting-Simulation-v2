<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}
include('../connection.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Irish+Grover&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../css/homepage.css">
    <style>
        body {
            background: #f8fcf7;
            font-family: 'Poppins', Arial, sans-serif;
        }
        .admin-header {
            width: 100%;
            background: #fff;
            color: #388e3c;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 2rem;
            font-size: 1.2rem;
            box-shadow: 0 2px 8px rgba(60,120,60,0.08);
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 3px solid #4caf50;
        }
        .admin-header .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-family: 'Irish Grover', cursive;
            font-size: 1.5rem;
            color: #388e3c;
        }
        .admin-header .logo img {
            height: 36px;
            width: 36px;
            border-radius: 8px;
            border: 2px solid #4caf50;
            background: #fff;
        }
        .admin-header .logout-link {
            color: #fff;
            background: #4caf50;
            border-radius: 50px;
            padding: 0.5rem 1.5rem;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            transition: background 0.2s, color 0.2s;
            font-weight: 600;
            border: none;
        }
        .admin-header .logout-link:hover {
            background: #388e3c;
            color: #f7c35f;
        }
        .admin-nav {
            background: #4caf50;
            display: flex;
            gap: 2rem;
            padding: 0.5rem 2rem 0.5rem 2rem;
            border-bottom: 1px solid #388e3c;
            box-shadow: 0 1px 4px rgba(60,120,60,0.03);
            position: sticky;
            top: 56px;
            z-index: 99;
        }
        .admin-nav .nav-link {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: background 0.2s, color 0.2s;
            font-size: 1.08rem;
        }
        .admin-nav .nav-link.active, .admin-nav .nav-link:hover {
            background: #f7c35f;
            color: #388e3c;
        }
        .content {
            padding: 2.5rem 1vw 2rem 1vw;
            max-width: 98vw;
            margin: 0 auto;
            margin-top: 1px;
        }
        .card {
            margin-bottom: 2rem;
            box-shadow: 0 2px 12px rgba(60,120,60,0.06);
            border-radius: 18px;
            width: 100%;
            background: #fff;
        }
        .content-section {
            display: none;
            opacity: 0;
            transition: opacity 0.4s;
        }
        .content-section.active {
            display: block;
            opacity: 1;
            animation: fadeIn 0.5s;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .table thead {
            background: #4caf50;
            color: #fff;
        }
        .btn-dark, .btn-warning, .btn-danger {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-family: 'Poppins', Arial, sans-serif;
        }
        .btn-dark {
            background: #388e3c;
            color: #f7c35f;
            border: none;
        }
        .btn-dark:hover {
            background: #256029;
            color: #fff;
        }
        .btn-warning {
            background: #f7c35f;
            color: #388e3c;
            border: none;
        }
        .btn-warning:hover {
            background: #ffe082;
            color: #256029;
        }
        .btn-danger {
            background: #e53935;
            color: #fff;
            border: none;
        }
        .btn-danger:hover {
            background: #b71c1c;
            color: #fff;
        }
        .btn-sm {
            font-size: 0.875rem;
            padding: 0.25rem 0.5rem;
        }
        .form-control {
            border-radius: 8px;
            font-family: 'Poppins', Arial, sans-serif;
        }
        .section-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            font-size: 1.5rem;
            margin-bottom: 1.2rem;
            color: #388e3c;
            font-family: 'Irish Grover', cursive;
        }
        /* Responsive header for small screens */
        @media (max-width: 768px) {
            .admin-header {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
                padding: 0.5rem 1rem;
                font-size: 1rem;
                gap: 0.5rem;
                position: relative;
            }
            .header-left {
                flex: 1 1 0;
                display: flex;
                align-items: center;
            }
            .header-center {
                position: absolute;
                left: 50%;
                transform: translateX(-50%);
                z-index: 201;
            }
            .header-right {
                flex: 1 1 0;
                display: flex;
                justify-content: flex-end;
                align-items: center;
            }
            .admin-header .logo img {
                height: 28px;
                width: 28px;
            }
            .admin-header .logout-link {
                align-self: center;
                margin-top: 0;
                font-size: 0.95rem;
            }
            .admin-nav-toggle {
                display: block;
                margin: 0;
            }
            .admin-nav {
                flex-direction: column;
                gap: 0.5rem;
                padding: 0.5rem 1rem;
                display: none;
                background: #4caf50;
                position: sticky;
                top: 48px;
                z-index: 99;
            }
            .admin-nav.show {
                display: flex;
                margin-top: 0.5rem;
            }
            .content {
                margin-top: 20px;
            }
        }
        /* Responsive nav for small screens */
        @media (max-width: 768px) {
            .admin-nav {
                flex-direction: column;
                gap: 0.5rem;
                padding: 0.5rem 1rem;
                display: none;
                background: #4caf50;
                position: relative;
                margin-top: 0;
            }
            .admin-nav.show {
                display: flex;
                margin-top: 0.5rem;
            }
            .admin-nav-toggle {
                display: block;
                position: relative;
                z-index: 200;
                margin-top: 0.5rem;
                margin-left: 0.5rem;
            }
            .admin-nav .nav-link {
                width: 100%;
                justify-content: flex-start;
                font-size: 1rem;
                padding: 0.75rem 1rem;
                background: #4caf50;
                color: #fff;
                border-radius: 4px;
            }
        }
        @media (min-width: 769px) {
            .admin-nav-toggle {
                display: none;
            }
            .admin-nav {
                display: flex !important;
                position: sticky;
                margin-top: 0;
            }
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <div class="header-left">
            <span class="logo">
                <img src="../../images/clearteenalogo.png" alt="Logo" class="img-fluid">
                Admin Dashboard
            </span>
        </div>
        <div class="header-center">
            <button class="admin-nav-toggle d-md-none" id="adminNavToggle" aria-label="Toggle navigation" style="background:none;border:none;color:#fff;font-size:2rem;">
                <i class="bi bi-list"></i>
            </button>
        </div>
        <div class="header-right">
            <a href="../logout.php" class="logout-link"><i class="bi bi-box-arrow-right"></i>Logout</a>
        </div>
    </div>
    <nav class="admin-nav" id="adminNav">
        <a class="nav-link" href="#" onclick="showSection('user-management'); setActiveNav(this); return false;" id="nav-user"><i class="bi bi-people"></i>User Management</a>
        <a class="nav-link" href="#" onclick="showSection('module-management'); setActiveNav(this); return false;" id="nav-module"><i class="bi bi-journal-text"></i>Module Management</a>
        <a class="nav-link" href="#" onclick="showSection('forum-management'); setActiveNav(this); return false;" id="nav-forum"><i class="bi bi-chat-dots"></i>Forum Management</a>
        <a class="nav-link" href="#" onclick="showSection('plantinder-management'); setActiveNav(this); return false;" id="nav-plantinder"><i class="bi bi-flower1"></i>Plantinder Management</a>
        <a class="nav-link" href="#" onclick="showSection('suggestions'); setActiveNav(this); return false;" id="nav-suggestions"><i class="bi bi-lightbulb"></i>Suggestions</a>
    </nav>
    <div class="content">
        <div id="toast-container" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100;"></div>
        <?php if (isset($_GET['success'])): ?>
            <script>window.addEventListener('DOMContentLoaded', function() { showToast('<?php echo htmlspecialchars($_GET['success']); ?>', 'success'); });</script>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <script>window.addEventListener('DOMContentLoaded', function() { showToast('<?php echo htmlspecialchars($_GET['error']); ?>', 'danger'); });</script>
        <?php endif; ?>
        <section id="user-management" class="content-section card p-4">
            <div class="section-title"><i class="bi bi-people"></i>User Management</div>
            <div class="mb-3">
                <input type="text" id="search" class="form-control" placeholder="Search user profiles...">
            </div>
            <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Date Created</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="userTable">
                    <?php
                    $result = $conn->query("SELECT * FROM users");
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                        echo "<td>
                                    <select class='form-select' onchange='updateUser(this, " . $row['user_id'] . ", \"role\")'>
                                        <option value='admin'" . ($row['role'] == 'admin' ? ' selected' : '') . ">Admin</option>
                                        <option value='student'" . ($row['role'] == 'student' ? ' selected' : '') . ">Student</option>
                                        <option value='agriculturist'" . ($row['role'] == 'agriculturist' ? ' selected' : '') . ">Agriculturist</option>
                                    </select>
                                </td>";
                        echo "<td>" . htmlspecialchars($row['date_created']) . "</td>";
                        echo "<td>
                            <select class='form-select' onchange='updateUser(this, " . $row['user_id'] . ", \"status\")'>
                                <option value='active'" . ($row['status'] == 'active' ? ' selected' : '') . ">Active</option>
                                <option value='inactive'" . ($row['status'] == 'inactive' ? ' selected' : '') . ">Inactive</option>
                            </select>
                        </td>";
                        echo "<td>";
                        // Don't show delete button for the current admin user
                        if ($row['user_id'] != $_SESSION['user_id']) {
                            echo "<a href='deleteuser.php?id=" . $row['user_id'] . "' class='btn btn-sm btn-danger' onclick='return confirmDelete(\"" . htmlspecialchars($row['username']) . "\", " . $row['user_id'] . ")'><i class='bi bi-trash'></i>Delete</a>";
                        } else {
                            echo "<span class='text-muted'>Current User</span>";
                        }
                        echo "</td>";
                    }
                    ?>
                </tbody>
            </table>
            </div>
        </section>
        <section id="module-management" class="content-section card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="section-title mb-0"><i class="bi bi-journal-text"></i>Module Management</div>
                <a href="addmodule.php" class="btn btn-dark"><i class="bi bi-plus-circle"></i>Add New</a>
            </div>
            <div class="mb-3">
                <input type="text" id="module-search" class="form-control" placeholder="Search modules...">
            </div>
            <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Type</th>
                        <th>Category</th>
                        <th>Content</th>
                        <th>Image</th>
                        <th>Created_at</th>
                        <th>Updated_at</th>
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody id="moduleTable">
                    <?php
                    $result2 = $conn->query("SELECT * FROM modules");
                    while ($row = $result2->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['type']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['content']) . "</td>";
                        // Display image preview in admin table
                        $image_path = $row['image_path'];
                        if (filter_var($image_path, FILTER_VALIDATE_URL)) {
                            // External URL
                            echo "<td>
                                    <img src='" . htmlspecialchars($image_path) . "' alt='Module Image' style='width: 50px; height: 50px; object-fit: cover; border-radius: 5px;' onerror='this.style.display=\"none\"; this.nextElementSibling.style.display=\"block\";'>
                                    <span style='display: none; font-size: 0.8em; color: #666;'>External URL</span>
                                  </td>";
                        } else {
                            // Local file
                            echo "<td>
                                    <img src='../../" . htmlspecialchars($image_path) . "' alt='Module Image' style='width: 50px; height: 50px; object-fit: cover; border-radius: 5px;' onerror='this.style.display=\"none\"; this.nextElementSibling.style.display=\"block\";'>
                                    <span style='display: none; font-size: 0.8em; color: #666;'>Local File</span>
                                  </td>";
                        }
                        echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['updated_at']) . "</td>";
                        echo "<td>
                                <a href='editmodule.php?id=" . $row['module_id'] . "' class='btn btn-sm btn-warning'><i class='bi bi-pencil-square'></i>Edit</a>
                              </td>";
                        echo "<td>
                                <a href='deletemodule.php?id=" . $row['module_id'] . "' class='btn btn-sm btn-danger'><i class='bi bi-archive'></i>Archive</a>
                              </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
            </div>
        </section>
        <section id="forum-management" class="content-section card p-4">
            <div class="section-title"><i class="bi bi-chat-dots"></i>Forum Management</div>
            <h5 class="mb-3"><i class="bi bi-question-circle"></i> Manage Questions</h5>
            <div class="mb-3">
                <input type="text" id="question-search" class="form-control" placeholder="Search questions...">
            </div>
            <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Body</th>
                        <th>User</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="questionTable">
                    <?php
                    $questions = $conn->query("SELECT q.*, u.username FROM questions q JOIN users u ON q.user_id = u.user_id");
                    while ($row = $questions->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['question_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['body']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                        echo "<td>
                                <a href='deletequestions.php?id=" . $row['question_id'] . "' class='btn btn-sm btn-danger'><i class='bi bi-trash'></i>Delete</a>
                            </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
            </div>
            <h5 class="mb-3 mt-4"><i class="bi bi-reply"></i> Manage Replies</h5>
            <div class="mb-3">
                <input type="text" id="reply-search" class="form-control" placeholder="Search replies...">
            </div>
            <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Question ID</th>
                        <th>Body</th>
                        <th>User</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="replyTable">
                    <?php
                        $replies = $conn->query("SELECT r.*, u.username FROM reply r JOIN users u ON r.user_id = u.user_id");
                        while ($row = $replies->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['reply_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['question_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['body']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                            echo "<td>
                                    <a href='deletereply.php?id=" . $row['reply_id'] . "' class='btn btn-sm btn-danger'><i class='bi bi-trash'></i>Delete</a>
                                </td>";
                            echo "</tr>";
                        }
                    ?>
                </tbody>
            </table>
            </div>
        </section>
        <section id="plantinder-management" class="content-section card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="section-title mb-0"><i class="bi bi-flower1"></i>Plant Management</div>
                <a href="addplant.php" class="btn btn-dark"><i class="bi bi-plus-circle"></i>Add New</a>
            </div>
            <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Plant Name</th>
                        <th>Description</th>
                        <th>Image Path</th>
                        <th>Container & Soil</th>
                        <th>Watering</th>
                        <th>Sunlight</th>
                        <th>Tips</th>
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody id="plantTable">
                    <?php
                    $result = $conn->query("SELECT * FROM plant");
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['plant_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['image']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['container_soil']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['watering']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['sunlight']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['tips']) . "</td>";
                        echo "<td>
                                <a href='editplant.php?id=" . $row['plant_id'] . "' class='btn btn-sm btn-warning'><i class='bi bi-pencil-square'></i>Edit</a>
                              </td>";
                        echo "<td>
                                <a href='deleteplant.php?id=" . $row['plant_id'] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure you want to delete this plant?\")'><i class='bi bi-trash'></i>Delete</a>
                              </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
            </div>
        </section>
        <section id="suggestions" class="content-section card p-4">
            <div class="section-title"><i class="bi bi-lightbulb"></i>Suggestions</div>
            <div class="mb-3">
                <input type="text" id="suggestion-search" class="form-control" placeholder="Search suggestions...">
            </div>
            <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Message</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="suggestionTable">
                    <?php
                    $suggestions = $conn->query("SELECT * FROM suggestions");
                    while ($row = $suggestions->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['suggestion_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['message']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                        echo "<td>
                                <select class='form-select' onchange='updateStatus1(this, " . $row['suggestion_id'] . ")'>
                                    <option value='pending'" . ($row['status'] == 'pending' ? ' selected' : '') . ">Pending</option>
                                    <option value='approved'" . ($row['status'] == 'approved' ? ' selected' : '') . ">Approved</option>
                                    <option value='rejected'" . ($row['status'] == 'rejected' ? ' selected' : '') . ">Rejected</option>
                                </select>
                            </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
            </div>
        </section>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script>
        document.getElementById('search').addEventListener('keyup', function() {
            var searchValue = this.value.toLowerCase();
            var rows = document.getElementById('userTable').getElementsByTagName('tr');
            for (var i = 0; i < rows.length; i++) {
                var cells = rows[i].getElementsByTagName('td');
                var match = false;
                for (var j = 0; j < cells.length; j++) {
                    if (cells[j].innerText.toLowerCase().includes(searchValue)) {
                        match = true;
                        break;
                    }
                }
                rows[i].style.display = match ? '' : 'none';
            }
        });
        document.getElementById('module-search').addEventListener('keyup', function() {
            var searchValue = this.value.toLowerCase();
            var rows = document.getElementById('moduleTable').getElementsByTagName('tr');
            for (var i = 0; i < rows.length; i++) {
                var cells = rows[i].getElementsByTagName('td');
                var match = false;
                for (var j = 0; j < cells.length; j++) {
                    if (cells[j].innerText.toLowerCase().includes(searchValue)) {
                        match = true;
                        break;
                    }
                }
                rows[i].style.display = match ? '' : 'none';
            }
        });
        function updateUser(select, userId, field) {
            var value = select.value;
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "updateuser.php?id=" + userId + "&field=" + field + "&value=" + value, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4) {
                    if (xhr.status == 200) {
                        showToast(field.charAt(0).toUpperCase() + field.slice(1) + " updated successfully.", 'success');
                    } else {
                        showToast("Error updating " + field + ": " + xhr.responseText, 'danger');
                    }
                }
            };
            xhr.send();
        }
        document.getElementById('question-search').addEventListener('keyup', function() {
            var searchValue = this.value.toLowerCase();
            var rows = document.getElementById('questionTable').getElementsByTagName('tr');
            for (var i = 0; i < rows.length; i++) {
                var cells = rows[i].getElementsByTagName('td');
                var match = false;
                for (var j = 0; j < cells.length; j++) {
                    if (cells[j].innerText.toLowerCase().includes(searchValue)) {
                        match = true;
                        break;
                    }
                }
                rows[i].style.display = match ? '' : 'none';
            }
        });
        document.getElementById('reply-search').addEventListener('keyup', function() {
            var searchValue = this.value.toLowerCase();
            var rows = document.getElementById('replyTable').getElementsByTagName('tr');
            for (var i = 0; i < rows.length; i++) {
                var cells = rows[i].getElementsByTagName('td');
                var match = false;
                for (var j = 0; j < cells.length; j++) {
                    if (cells[j].innerText.toLowerCase().includes(searchValue)) {
                        match = true;
                        break;
                    }
                }
                rows[i].style.display = match ? '' : 'none';
            }
        });
        document.getElementById('suggestion-search').addEventListener('keyup', function() {
            var searchValue = this.value.toLowerCase();
            var rows = document.getElementById('suggestionTable').getElementsByTagName('tr');
            for (var i = 0; i < rows.length; i++) {
                var cells = rows[i].getElementsByTagName('td');
                var match = false;
                for (var j = 0; j < cells.length; j++) {
                    if (cells[j].innerText.toLowerCase().includes(searchValue)) {
                        match = true;
                        break;
                    }
                }
                rows[i].style.display = match ? '' : 'none';
            }
        });
        function showSection(sectionId) {
            var sections = document.querySelectorAll('.content-section');
            sections.forEach(function(section) {
                section.classList.remove('active');
            });
            var activeSection = document.getElementById(sectionId);
            activeSection.classList.add('active');
        }
        function setActiveNav(element) {
            document.querySelectorAll('.admin-nav .nav-link').forEach(function(nav) {
                nav.classList.remove('active');
            });
            element.classList.add('active');
        }
        window.onload = function() {
            // Check if there's a hash in the URL to determine which section to show
            var hash = window.location.hash.substring(1);
            if (hash && document.getElementById(hash)) {
                showSection(hash);
                // Set the corresponding nav link as active
                var navId = 'nav-' + hash.replace('-management', '');
                if (document.getElementById(navId)) {
                    setActiveNav(document.getElementById(navId));
                }
            } else {
                showSection('user-management');
                setActiveNav(document.getElementById('nav-user'));
            }
        };
        function updateStatus1(select, suggestionId) {
            var newStatus = select.value;
            var formData = new FormData();
            formData.append('suggestion_id', suggestionId);
            formData.append('status', newStatus);
            fetch('suggestionstatus.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                if (data === 'success') {
                    showToast('Status updated successfully!', 'success');
                } else {
                    showToast('Error updating status.', 'danger');
                }
            })
            .catch(error => showToast('Error: ' + error, 'danger'));
        }
        // Confirmation dialog for user deletion
        function confirmDelete(username, userId) {
            if (confirm('Are you sure you want to delete user "' + username + '"?\n\nThis action will permanently delete:\n• User account\n• All user questions and replies\n• All user favorites\n\nThis action cannot be undone.')) {
                window.location.href = 'deleteuser.php?id=' + userId;
                return true;
            }
            return false;
        }
        
        // Toast function
        function showToast(message, type) {
            var toastContainer = document.getElementById('toast-container');
            var toastId = 'toast-' + Date.now();
            var toast = document.createElement('div');
            toast.className = 'toast align-items-center text-bg-' + (type === 'danger' ? 'danger' : 'success') + ' border-0 show';
            toast.id = toastId;
            toast.setAttribute('role', 'alert');
            toast.setAttribute('aria-live', 'assertive');
            toast.setAttribute('aria-atomic', 'true');
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close" onclick="document.getElementById('${toastId}').remove();"></button>
                </div>
            `;
            toastContainer.appendChild(toast);
            setTimeout(function() {
                if (document.getElementById(toastId)) {
                    document.getElementById(toastId).remove();
                }
            }, 3500);
        }
        // Hamburger menu toggle
        document.getElementById('adminNavToggle').addEventListener('click', function() {
            var nav = document.getElementById('adminNav');
            nav.classList.toggle('show');
        });
        
        // Handle hash changes (for browser back/forward buttons)
        window.addEventListener('hashchange', function() {
            var hash = window.location.hash.substring(1);
            if (hash && document.getElementById(hash)) {
                showSection(hash);
                var navId = 'nav-' + hash.replace('-management', '');
                if (document.getElementById(navId)) {
                    setActiveNav(document.getElementById(navId));
                }
            }
        });
    </script>
</body>
</html>
