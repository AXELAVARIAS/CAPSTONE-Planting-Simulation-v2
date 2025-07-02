<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Determine base path for links and images
$base = '';
if (strpos($_SERVER['PHP_SELF'], 'Admin/') !== false) {
    $base = '../../';
} elseif (strpos($_SERVER['PHP_SELF'], 'Forum/') !== false) {
    $base = '../../';
} elseif (strpos($_SERVER['PHP_SELF'], 'php/') !== false) {
    $base = '../';
} else {
    $base = '';
}

// Always define this variable!
$is_login_page = strpos($_SERVER['PHP_SELF'], 'login.php') !== false;
?>
<style>
  body {
    padding-top: 80px !important;
  }
  .navbar-modern {
    background: #fff !important;
    box-shadow: 0 2px 12px rgba(60, 120, 60, 0.08);
    border-bottom: 3px solid #4caf50;
    padding-top: 0.5rem;
    padding-bottom: 0.5rem;
    z-index: 1050;
  }
  .navbar-modern .navbar-brand {
    display: flex;
    align-items: center;
    font-weight: bold;
    font-size: 1.5rem;
    color: #388e3c !important;
    letter-spacing: 1px;
  }
  .navbar-modern .teenanimlogo {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 12px;
    border: 2px solid #4caf50;
    background: #fff;
  }
  .navbar-modern .navbar-nav .nav-link {
    color: #388e3c !important;
    font-weight: 500;
    font-size: 1.1rem;
    margin: 0 0.5rem;
    position: relative;
    transition: color 0.2s;
  }
  .navbar-modern .navbar-nav .nav-link::after {
    content: '';
    display: block;
    width: 0;
    height: 2px;
    background: #4caf50;
    transition: width 0.3s;
    position: absolute;
    left: 0;
    bottom: -4px;
  }
  .navbar-modern .navbar-nav .nav-link:hover,
  .navbar-modern .navbar-nav .nav-link.active {
    color: #256029 !important;
  }
  .navbar-modern .navbar-nav .nav-link:hover::after,
  .navbar-modern .navbar-nav .nav-link.active::after {
    width: 100%;
  }
  .navbar-modern .btn-signin, .navbar-modern .btn-profile {
    background: #4caf50;
    color: #fff;
    border-radius: 50px;
    padding: 0.5rem 1.5rem;
    font-weight: 600;
    border: none;
    transition: background 0.2s, color 0.2s;
    box-shadow: 0 2px 8px rgba(76,175,80,0.08);
  }
  .navbar-modern .btn-signin:hover, .navbar-modern .btn-profile:hover {
    background: #388e3c;
    color: #fff;
  }
  .navbar-toggler {
    border: none;
    outline: none;
  }
  .navbar-toggler:focus {
    box-shadow: 0 0 0 2px #4caf50;
  }
  @media (max-width: 991.98px) {
    .navbar-modern .navbar-nav .nav-link {
      margin: 0.5rem 0;
      font-size: 1.2rem;
    }
    .navbar-modern .navbar-collapse {
      background: #fff;
      border-radius: 0 0 1rem 1rem;
      box-shadow: 0 8px 24px rgba(76,175,80,0.08);
      padding: 1rem 0;
    }
  }
</style>
<nav class="navbar navbar-expand-lg fixed-top navbar-modern w-100">
  <div class="container-fluid">
    <?php if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true): ?>
      <?php
        $is_login_page = strpos($_SERVER['PHP_SELF'], 'login.php') !== false;
      ?>
      <div class="d-flex w-100 align-items-center">
        <a class="navbar-brand me-auto" href="<?php echo $base; ?>index.php">
          <img src="<?php echo $base; ?>images/clearteenalogo.png" class="teenanimlogo" alt="home logo">
          TEEN-ANIM
        </a>
        <?php if (!$is_login_page): ?>
        <div class="flex-grow-1 d-flex justify-content-center">
          <a href="<?php echo $base; ?>php/login.php" class="btn btn-signin">Sign In</a>
        </div>
        <div style="width: 120px;"></div>
        <?php else: ?>
        <div class="flex-grow-1"></div>
        <div style="width: 120px;"></div>
        <?php endif; ?>
        <?php if ($is_login_page): ?>
          <div class="ms-2">
            <a href="<?php echo $base; ?>index.php" class="btn btn-success" style="border-radius: 50px; font-weight: 600; padding: 0.5rem 1.5rem;">Go Back to Dashboard</a>
          </div>
        <?php endif; ?>
      </div>
    <?php else: ?>
      <a class="navbar-brand" href="<?php echo $base; ?>index.php">
        <img src="<?php echo $base; ?>images/clearteenalogo.png" class="teenanimlogo" alt="home logo">
        TEEN-ANIM
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto align-items-lg-center">
          <li class="nav-item">
            <a class="nav-link" href="<?php echo $base; ?>php/Forum/community.php">Farming Community</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo $base; ?>php/simulator.php">Simulation</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo $base; ?>php/plantinder.php">Plantinder</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo $base; ?>php/modulepage.php">Module</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo $base; ?>php/userpage.php">Profile</a>
          </li>
        </ul>
        <?php if ($is_login_page): ?>
          <?php
            $dashboard_link = $base . 'index.php';
            if (isset($_SESSION['role'])) {
              if ($_SESSION['role'] === 'admin') {
                $dashboard_link = $base . 'php/Admin/adminpage.php';
              } elseif ($_SESSION['role'] === 'agriculturist') {
                $dashboard_link = $base . 'php/Admin/agriculturistpage.php';
              }
            }
          ?>
          <div class="ms-2">
            <a href="<?php echo $dashboard_link; ?>" class="btn btn-success" style="border-radius: 50px; font-weight: 600; padding: 0.5rem 1.5rem;">Go Back to Dashboard</a>
          </div>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>
</nav> 