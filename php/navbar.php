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

// Get current page for active navigation
$current_page = basename($_SERVER['PHP_SELF']);
$current_path = $_SERVER['PHP_SELF'];

// Function to check if a link should be active
function isActiveLink($link_path, $current_path, $current_page) {
    // Check for exact page match
    if (strpos($current_path, $link_path) !== false) {
        return true;
    }
    
    // Special cases for different sections
    if ($link_path === 'community.php' && strpos($current_path, 'Forum/') !== false) {
        return true;
    }
    
    if ($link_path === 'index.php' && $current_page === 'index.php') {
        return true;
    }
    
    return false;
}
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
    transition: background 0.2s, color 0.2s, box-shadow 0.2s;
    border-radius: 10px;
    padding: 0.25rem 0.75rem;
    cursor: pointer;
    height: 56px;
    line-height: 48px;
    min-width: 0;
    box-sizing: border-box;
  }
  .navbar-modern .navbar-brand:hover, .navbar-modern .navbar-brand:focus {
    background: rgba(76, 175, 80, 0.10);
    color: #256029 !important;
    box-shadow: 0 2px 8px rgba(76, 175, 80, 0.10);
    text-decoration: none;
  }
  .navbar-modern .navbar-brand:hover .teenanimlogo, .navbar-modern .navbar-brand:focus .teenanimlogo {
    border-color: #256029;
    filter: brightness(0.95);
    transition: border-color 0.2s, filter 0.2s;
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
    padding: 0.5rem 1rem !important;
    border-radius: 8px;
  }
  .navbar-modern .navbar-nav .nav-link::after {
    content: '';
    display: block;
    width: 0;
    height: 3px;
    background: #4caf50;
    transition: width 0.3s ease;
    position: absolute;
    left: 50%;
    bottom: -2px;
    transform: translateX(-50%);
    border-radius: 2px;
  }
  .navbar-modern .navbar-nav .nav-link:hover,
  .navbar-modern .navbar-nav .nav-link.active {
    color: #256029 !important;
    background-color: rgba(76, 175, 80, 0.1);
  }
  .navbar-modern .navbar-nav .nav-link:hover::after,
  .navbar-modern .navbar-nav .nav-link.active::after {
    width: 80%;
  }
  .navbar-modern .navbar-nav .nav-link.active {
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(76, 175, 80, 0.15);
  }
  .navbar-modern .btn-signin, .navbar-modern .btn-profile {
    background: #4caf50;
    color: #fff;
    border-radius: 50px;
    padding: 0.5rem 1.5rem;
    font-weight: 600;
    border: none;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(76,175,80,0.08);
  }
  .navbar-modern .btn-signin:hover, .navbar-modern .btn-profile:hover {
    background: #388e3c;
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(76,175,80,0.2);
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
  .navbar-modern .navbar-nav {
    justify-content: center !important;
    width: 100%;
  }
  .profile-dropdown-topright {
    position: fixed;
    top: 200px;
    right: 40px;
    z-index: 2000;
  }
  .custom-profile-dropdown {
    min-width: 320px;
    top: 2px !important;
    right: 0 !important;
    left: auto !important;
    position: absolute !important;
    z-index: 2001;
    border-radius: 1.2rem;
    margin-top: 0 !important;
    background: #fff;
    color: #388e3c;
    padding-top: 0.5rem;
    padding-bottom: 0.5rem;
    border: solid 3px #4caf50;
  }
  .custom-profile-dropdown .dropdown-header {
    padding: 1rem 1rem 0.5rem 1rem;
    background: transparent;
    color: #388e3c;
  }
  .custom-profile-dropdown .dropdown-item {
    color: #388e3c;
    border-radius: 0.7rem;
    margin: 0 0.5rem;
    padding: 0.6rem 1rem;
    transition: background 0.2s, color 0.2s, padding 0.2s, font-size 0.2s, transform 0.2s;
    font-weight: 500;
  }
  .custom-profile-dropdown .dropdown-item:hover {
    background: #e8f5e9;
    color: #256029;
    transform: translateX(5px);
  }
  
  .points-badge {
    background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
    color: #333;
    border-radius: 20px;
    padding: 0.25rem 0.75rem;
    font-size: 0.8rem;
    font-weight: 700;
    margin-left: 0.5rem;
    box-shadow: 0 2px 8px rgba(255,215,0,0.3);
  }
  
  .streak-badge {
    background: linear-gradient(135deg, #ff6b35 0%, #ff8a65 100%);
    color: white;
    border-radius: 20px;
    padding: 0.25rem 0.75rem;
    font-size: 0.8rem;
    font-weight: 700;
    margin-left: 0.5rem;
    box-shadow: 0 2px 8px rgba(255,107,53,0.3);
  }
    padding: 0.9rem 1.3rem;
    font-size: 1.12rem;
    transform: scale(0.90);
  }
  .custom-profile-dropdown .dropdown-divider {
    border-top: 1px solid #c8e6c9;
    margin: 0.3rem 0;
  }
  /* Remove Bootstrap dropdown arrow/caret for profile dropdown */
  #profileDropdownTop::after {
    display: none !important;
  }
  
  /* Profile picture hover effects */
  .profile-pic-navbar {
    transition: all 0.3s ease;
    cursor: pointer;
  }
  
  .profile-pic-navbar:hover {
    transform: scale(1.1);
    border-color: #256029 !important;
    box-shadow: 0 4px 16px rgba(76, 175, 80, 0.3);
  }
  
  /* Profile dropdown button hover effects */
  #profileDropdownTop {
    transition: all 0.3s ease;
    border-radius: 50%;
    padding: 4px;
  }
  
  #profileDropdownTop:hover {
    background: rgba(76, 175, 80, 0.1) !important;
    transform: scale(1.05);
  }
  
  /* Profile dropdown header image hover */
  .custom-profile-dropdown .dropdown-header img {
    transition: all 0.3s ease;
  }
  
  .custom-profile-dropdown .dropdown-header:hover img {
    transform: scale(1.05);
    border-color: #256029;
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
        <ul class="navbar-nav justify-content-center w-100 align-items-lg-center">
          <li class="nav-item">
            <a class="nav-link <?php echo isActiveLink('dashboard.php', $current_path, $current_page) ? 'active' : ''; ?>" href="<?php echo $base; ?>php/dashboard.php">
              <i class="bi bi-speedometer2"></i> Dashboard
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo isActiveLink('leaderboard.php', $current_path, $current_page) ? 'active' : ''; ?>" href="<?php echo $base; ?>php/leaderboard.php">
              <i class="bi bi-trophy"></i> Leaderboard
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo isActiveLink('community.php', $current_path, $current_page) ? 'active' : ''; ?>" href="<?php echo $base; ?>php/Forum/community.php">Farming Community</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo isActiveLink('simulator.php', $current_path, $current_page) ? 'active' : ''; ?>" href="<?php echo $base; ?>php/simulator.php">Simulation</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo isActiveLink('plantinder.php', $current_path, $current_page) ? 'active' : ''; ?>" href="<?php echo $base; ?>php/plantinder.php">Plantinder</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo isActiveLink('modulepage.php', $current_path, $current_page) ? 'active' : ''; ?>" href="<?php echo $base; ?>php/modulepage.php">Module</a>
          </li>
        </ul>
      </div>
    <?php endif; ?>
  </div>
</nav>
<?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
  <?php
    include_once dirname(__FILE__) . '/connection.php';
    include_once dirname(__FILE__) . '/gamification.php';
    $user_id = $_SESSION['user_id'];
    $profile_pic = '';
    $user_name = '';
    $user_role = '';
    
    // Get user info
    $sql = "SELECT name, profile_picture, role FROM users WHERE user_id = ?";
    if ($stmt = $conn->prepare($sql)) {
      $stmt->bind_param("i", $user_id);
      $stmt->execute();
      $stmt->bind_result($name, $profile_picture, $role);
      if ($stmt->fetch()) {
        $user_name = htmlspecialchars($name);
        $user_role = htmlspecialchars(ucfirst($role));
        $profile_pic = (empty($profile_picture) || $profile_picture === 'clearteenalogo.png') 
            ? $base . 'images/clearteenalogo.png' 
            : $base . 'images/profile_pics/' . htmlspecialchars($profile_picture);
      } else {
        $profile_pic = $base . "images/clearteenalogo.png";
      }
      $stmt->close();
    } else {
      $profile_pic = $base . "images/clearteenalogo.png";
    }
    
    // Get gamification data
    $gamification = new GamificationSystem($conn, $user_id);
    $totalPoints = $gamification->getTotalPoints();
    $streakInfo = $gamification->getStreakInfo();
  ?>
  <div class="profile-dropdown-topright position-fixed" style="top: 20px; right: 40px; z-index: 2000;">
    <div class="dropdown">
      <a class="btn btn-profile d-flex align-items-center p-0" href="#" id="profileDropdownTop" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="background: none; border: none;">
        <img src="<?php echo $profile_pic; ?>" alt="Profile" class="profile-pic-navbar" style="width: 44px; height: 44px; border-radius: 50%; object-fit: cover; border: 2px solid #4caf50; background: #fff;">
      </a>
      <ul class="dropdown-menu dropdown-menu-end custom-profile-dropdown" aria-labelledby="profileDropdownTop">
        <li class="dropdown-header text-center">
          <img src="<?php echo $profile_pic; ?>" alt="Profile" style="width: 56px; height: 56px; border-radius: 50%; object-fit: cover; border: 2px solid #4caf50; background: #fff; margin-bottom: 8px;">
          <div style="font-weight: 600;"><?php echo $user_name; ?></div>
          <div style="font-size: 0.95em; color: #388e3c; margin-top: 2px; font-weight: 500;">
            <?php echo $user_role; ?>
          </div>
          <div class="d-flex justify-content-center gap-2 mt-2">
            <span class="points-badge">💰 <?php echo $totalPoints; ?> pts</span>
            <span class="streak-badge">🔥 <?php echo $streakInfo['current_streak']; ?> days</span>
          </div>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="<?php echo $base; ?>php/dashboard.php">
          <i class="bi bi-speedometer2 me-2"></i>Dashboard
        </a></li>
        <li><a class="dropdown-item" href="<?php echo $base; ?>php/userpage.php">
          <i class="bi bi-person me-2"></i>Profile
        </a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="<?php echo $base; ?>php/logout.php">
          <i class="bi bi-box-arrow-right me-2"></i>Logout
        </a></li>
      </ul>
    </div>
  </div>
<?php endif; ?> 