<?php
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include('connection.php');

    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $repassword = $_POST['repassword'];

    if ($password != $repassword) {
        $error_message = "Passwords do not match!";
    } elseif (!preg_match("/^[a-zA-Z0-9]*$/", $username)) {
        $error_message = "Username should contain only letters and numbers.";
    } elseif (strlen($password) < 8 || strlen($password) > 20) {
        $error_message = "Password must be between 8 and 20 characters long.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (name, username, password, role) VALUES (?, ?, ?, 'user')");
        $stmt->bind_param("sss", $fullname, $username, $hashed_password);

        if ($stmt->execute()) {
            echo "Sign up successful!";
            header("Location: userpage.php"); 
            exit();
        } else {
            $error_message = "Error: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="../css/signup.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #e0eafc 0%, #cfdef3 100%);
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .signup-card {
            width: 100%;
            max-width: 480px;
            margin: 0 auto;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
            background: #fff;
            padding: 2.5rem 2rem 2rem 2rem;
            position: relative;
        }
        .form-label {
            font-weight: 500;
        }
        .input-group-text {
            background: #f4f6fa;
            border: none;
        }
        .form-control:focus {
            box-shadow: 0 0 0 2px #a3c9f7;
        }
        .password-strength {
            height: 6px;
            border-radius: 4px;
            margin-top: 4px;
            margin-bottom: 10px;
            transition: width 0.3s;
        }
        .alert {
            transition: opacity 0.5s, transform 0.5s;
        }
        .alert-hide {
            opacity: 0;
            transform: translateY(-20px);
        }
        .toggle-password {
            cursor: pointer;
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
    </style>
</head>
<body>
    <header class="custom-header">
        <div class="header-content">
            <img src="../images/clearteenalogo.png" alt="TEEN-ANIM Logo" class="header-logo">
            <span class="header-title">TEEN-ANIM</span>
        </div>
    </header>
    <div class="container-fluid d-flex justify-content-center" style="padding-top: 40px; padding-bottom: 40px;">
        <div class="signup-card">
            <h3 class="mb-4 text-center">Sign Up</h3>
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger animate__animated animate__fadeInDown" role="alert" id="errorAlert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error_message) ?>
                </div>
            <?php endif; ?>
            <form action="signup.php" method="post" autocomplete="off" id="signupForm">
                <label for="fullname" class="form-label">Full Name:</label>
                <div class="input-group mb-3">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input class="form-control" type="text" name="fullname" placeholder="Full name..." id="fullname" required>
                </div>
                <label for="username" class="form-label">Enter Username:</label>
                <div class="input-group mb-3">
                    <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                    <input class="form-control" type="text" name="username" placeholder="Username..." id="username" required>
                </div>
                <div id="usernameFeedback" class="form-text mb-2 text-danger d-none"></div>
                <label for="password" class="form-label">Password:</label>
                <div class="input-group" id="password-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Type your password..." required>
                    <span class="input-group-text toggle-password" onclick="togglePassword('password', this)"><i class="bi bi-eye"></i></span>
                </div>
                <div class="password-strength bg-secondary" id="passwordStrength"></div>
                <div id="passwordtext" class="form-text mb-3">
                    Your password must be 8-20 characters long, contain letters and numbers, and must not contain spaces, special characters, or emoji.
                </div>
                <label for="repassword" class="form-label">Re-enter Password:</label>
                <div class="input-group mb-3">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" name="repassword" id="repassword" class="form-control" placeholder="Re-enter your password..." required>
                    <span class="input-group-text toggle-password" onclick="togglePassword('repassword', this)"><i class="bi bi-eye"></i></span>
                </div>
                <div id="matchFeedback" class="form-text mb-3 text-danger d-none"></div>
                <div class="row">
                    <div class="col d-flex align-items-end">
                        <a href="login.php" class="link-offset-2 link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover">Already have an account?</a>
                    </div>
                    <div class="col d-flex justify-content-end">
                        <button type="submit" class="btn btn-success mt-2 w-100">Sign Up</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <footer>
        <div class="container-fluid footer-bg fixed-bottom">
            <div class="pt-1 mx-5 d-flex justify-content-around align-items-center">
                <p>Copyright 2024</p>
                <img src="../images/clearteenalogo.png" class="teenanimlogo mb-2" alt="TEENANIM LOGO">
                <p>Terms & Conditions / Privacy Policy</p>
            </div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script>
        // Show/hide password toggle
        function togglePassword(fieldId, el) {
            const input = document.getElementById(fieldId);
            const icon = el.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }
        // Password strength meter
        document.getElementById('password').addEventListener('input', function() {
            const val = this.value;
            const strengthBar = document.getElementById('passwordStrength');
            let strength = 0;
            if (val.length >= 8) strength++;
            if (val.match(/[A-Z]/)) strength++;
            if (val.match(/[0-9]/)) strength++;
            if (val.match(/[a-z]/)) strength++;
            if (val.length >= 12) strength++;
            let color = '#dc3545', width = '20%';
            if (strength >= 4) { color = '#ffc107'; width = '60%'; }
            if (strength >= 5) { color = '#198754'; width = '100%'; }
            strengthBar.style.background = color;
            strengthBar.style.width = width;
        });
        // Password match feedback
        document.getElementById('repassword').addEventListener('input', function() {
            const pass = document.getElementById('password').value;
            const repass = this.value;
            const feedback = document.getElementById('matchFeedback');
            if (repass && pass !== repass) {
                feedback.textContent = 'Passwords do not match!';
                feedback.classList.remove('d-none');
            } else {
                feedback.textContent = '';
                feedback.classList.add('d-none');
            }
        });
        // Username validation
        document.getElementById('username').addEventListener('input', function() {
            const val = this.value;
            const feedback = document.getElementById('usernameFeedback');
            if (!/^[a-zA-Z0-9]*$/.test(val)) {
                feedback.textContent = 'Username should contain only letters and numbers.';
                feedback.classList.remove('d-none');
            } else {
                feedback.textContent = '';
                feedback.classList.add('d-none');
            }
        });
        // Animate error alert
        window.addEventListener('DOMContentLoaded', function() {
            const alert = document.getElementById('errorAlert');
            if (alert) {
                setTimeout(() => {
                    alert.classList.add('alert-hide');
                }, 3500);
            }
        });
    </script>
</body>
</html>
