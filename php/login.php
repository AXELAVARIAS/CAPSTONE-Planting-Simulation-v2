<?php
session_start();
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: Admin/adminpage.php");
        exit();
    } elseif ($_SESSION['role'] == 'agriculturist') {
        header("Location: Admin/agriculturistpage.php");
        exit();
    } else {
        header("Location: Admin/agriculturistpage.php");
        exit();
    }
}
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include('connection.php');

    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT user_id, password, role, status FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $hashed_password, $role, $status);
        $stmt->fetch();
        
        if ($status == 'inactive') {
            $error_message = "Your account is deactivated. Please contact the administrator.";
        } elseif (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;
            $_SESSION['logged_in'] = true;

            if ($role == 'admin') {
                header("Location: Admin/adminpage.php");
                exit();
            } elseif ($role == 'agriculturist') {
                header("Location: Admin/agriculturistpage.php");
                exit();
            }else {
                header("Location: ../index.php");
                exit();
            }
        } else {
            $error_message = "Invalid password.";
        }
    } else {
        $error_message = "Username not found.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="../css/signup.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
      body {
        min-height: 100vh;
        background: linear-gradient(135deg, #e0eafc 0%, #cfdef3 100%);
        font-family: 'Segoe UI', Arial, sans-serif;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
      }
      .login-container {
        flex: 1 0 auto;
        display: flex;
        align-items: flex-start;
        justify-content: center;
        padding-top: 70px; /* space for navbar + gap */
        padding-bottom: 40px;
      }
      .glass-card {
        background: rgba(255,255,255,0.85);
        box-shadow: 0 8px 32px 0 rgba(76,175,80,0.15);
        border-radius: 2rem;
        border: 1.5px solid #c8e6c9;
        backdrop-filter: blur(8px);
        padding: 2.5rem 2.5rem 2rem 2.5rem;
        max-width: 500px;
        width: 100%;
        position: relative;
        animation: fadeInUp 1s cubic-bezier(.23,1.01,.32,1) 0.1s both;
      }
      @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(40px); }
        to { opacity: 1; transform: none; }
      }
      .login-logo {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #4caf50;
        background: #fff;
        margin-bottom: 1rem;
        display: block;
        margin-left: auto;
        margin-right: auto;
      }
      .login-title {
        text-align: center;
        font-weight: bold;
        color: #388e3c;
        margin-bottom: 0.5rem;
        letter-spacing: 1px;
      }
      .form-floating > .form-control:focus ~ label,
      .form-floating > .form-control:not(:placeholder-shown) ~ label {
        color: #388e3c;
      }
      .form-floating > .form-control {
        border-radius: 1rem;
        border: 1.5px solid #a5d6a7;
        background: rgba(255,255,255,0.95);
      }
      .form-floating > label {
        color: #888;
        font-weight: 500;
      }
      .show-password-toggle {
        position: absolute;
        right: 1.5rem;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #4caf50;
        font-size: 1.2rem;
        z-index: 2;
      }
      .login-btn {
        background: #4caf50;
        color: #fff;
        border-radius: 50px;
        font-weight: 600;
        padding: 0.7rem 2.5rem;
        font-size: 1.1rem;
        border: none;
        box-shadow: 0 2px 8px rgba(76,175,80,0.08);
        transition: background 0.2s, transform 0.2s;
        margin-top: 1rem;
      }
      .login-btn:hover {
        background: #388e3c;
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(76,175,80,0.3);
      }
      .login-btn:active {
        transform: scale(0.97);
      }
      .login-btn[disabled] {
        opacity: 0.7;
        pointer-events: none;
      }
      .login-footer-link {
        text-align: left;
        margin-top: 1rem;
        font-size: 0.98rem;
      }
      .login-footer-link a {
        color: #388e3c;
        text-decoration: underline;
        font-weight: 500;
      }
      .alert {
        margin-top: 0.5rem;
        margin-bottom: 0.5rem;
        animation: shake 0.4s cubic-bezier(.36,.07,.19,.97) both;
      }
      @keyframes shake {
        10%, 90% { transform: translateX(-2px); }
        20%, 80% { transform: translateX(4px); }
        30%, 50%, 70% { transform: translateX(-8px); }
        40%, 60% { transform: translateX(8px); }
      }
      @media (max-width: 600px) {
        .glass-card { padding: 1.5rem 0.7rem 1.2rem 0.7rem; }
        .login-container { padding-top: 60px; }
      }
      .custom-header {
        background: #fff;
        width: 100%;
        padding: 0.25rem 0 0.25rem 0;
        box-shadow: none;
      }
      .header-content {
        display: flex;
        align-items: center;
        padding-left: 1rem;
        height: 60px;
      }
      .header-logo {
        height: 48px;
        width: 48px;
        border-radius: 50%;
        margin-right: 0.75rem;
      }
      .header-title {
        font-size: 2rem;
        font-weight: 700;
        color: #4caf50;
        letter-spacing: 1px;
        font-family: 'Segoe UI', Arial, sans-serif;
      }
      .back-btn {
        background: #4caf50;
        color: #fff;
        border: none;
        border-radius: 25px;
        padding: 0.5rem 1.5rem;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        transition: background 0.2s, transform 0.2s;
        margin-left: auto;
        margin-right: 1rem;
        box-shadow: 0 2px 8px rgba(76,175,80,0.08);
      }
      .back-btn:hover {
        background: #388e3c;
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(76,175,80,0.3);
      }
      .back-btn:active {
        transform: scale(0.97);
      }
    </style>
</head>
<body>
    <header class="custom-header">
        <div class="header-content">
            <img src="../images/clearteenalogo.png" alt="TEEN-ANIM Logo" class="header-logo">
            <span class="header-title">TEEN-ANIM</span>
            <a href="../index.php" class="back-btn">
                <i class="bi bi-arrow-left me-1"></i>Go Back to Dashboard
            </a>
        </div>
    </header>
    <div class="login-container">
      <div class="glass-card" data-aos="zoom-in">
        <img src="../images/clearteenalogo.png" alt="Teen-Anim Logo" class="login-logo">
        <div class="login-title">Sign In to Teen-Anim</div>
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger d-flex align-items-center" role="alert">
              <i class="bi bi-exclamation-triangle-fill me-2"></i>
              <div><?= htmlspecialchars($error_message) ?></div>
            </div>
        <?php endif; ?>
        <form action="login.php" method="post" id="loginForm" autocomplete="off">
          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
            <label for="username">Username</label>
          </div>
          <div class="form-floating mb-3 position-relative">
            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
            <label for="password">Password</label>
            <span class="show-password-toggle" onclick="togglePassword()"><i class="bi bi-eye-slash" id="toggleIcon"></i></span>
          </div>
          <button type="submit" class="btn login-btn w-100" id="loginBtn">
            <span id="loginBtnText">Sign In</span>
            <span id="loginSpinner" class="spinner-border spinner-border-sm d-none ms-2" role="status" aria-hidden="true"></span>
          </button>
          <div class="login-footer-link mt-3">
            <span>Don't have an account? <a href="signup.php">Sign up</a></span>
          </div>
        </form>
      </div>
    </div>
    <footer style="flex-shrink:0;">
      <div class="container-fluid footer-bg py-3 mt-5">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center px-5">
          <p class="mb-2 mb-md-0">Copyright 2024</p>
          <img src="../images/clearteenalogo.png" class="teenanimlogo mb-2" alt="TEENANIM LOGO">
          <p class="mb-0">Terms & Conditions / Privacy Policy</p>
        </div>
      </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.js"></script>
    <script>
      AOS.init();
      // Show/hide password
      function togglePassword() {
        const pwd = document.getElementById('password');
        const icon = document.getElementById('toggleIcon');
        if (pwd.type === 'password') {
          pwd.type = 'text';
          icon.classList.remove('bi-eye-slash');
          icon.classList.add('bi-eye');
        } else {
          pwd.type = 'password';
          icon.classList.remove('bi-eye');
          icon.classList.add('bi-eye-slash');
        }
      }
      // Button spinner on submit
      $(function() {
        $('#loginForm').on('submit', function() {
          $('#loginBtn').attr('disabled', true);
          $('#loginBtnText').text('Signing In...');
          $('#loginSpinner').removeClass('d-none');
        });
      });
    </script>
</body>
</html>
