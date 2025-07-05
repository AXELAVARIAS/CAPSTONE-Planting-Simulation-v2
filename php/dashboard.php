<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'connection.php';
require_once 'gamification.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$gamification = new GamificationSystem($conn, $_SESSION['user_id']);
$totalPoints = $gamification->getTotalPoints();
$streakInfo = $gamification->getStreakInfo();
$userProgress = $gamification->getUserProgress();
$userAchievements = $gamification->getUserAchievements();

// Get user info
$sql = "SELECT name, username, profile_picture FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$userInfo = $result ? $result->fetch_assoc() : null;

// Provide default values if user info is not found
if (!$userInfo) {
    $userInfo = [
        'name' => 'User',
        'username' => 'user',
        'profile_picture' => null
    ];
}

$profilePic = (empty($userInfo['profile_picture']) || $userInfo['profile_picture'] === 'clearteenalogo.png') 
    ? '/CAPSTONE-Planting-Simulation/images/clearteenalogo.png' 
    : '/CAPSTONE-Planting-Simulation/images/profile_pics/' . $userInfo['profile_picture'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - Teen-Anim</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/homepage.css">
    <style>
        body {
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
            font-family: 'Poppins', sans-serif;
        }
        
        .dashboard-container {
            padding: 2rem 0;
        }
        
        .row.stats-row {
            display: flex;
            justify-content: center;
            align-items: stretch;
            gap: 2rem;
        }
        
        .stats-card {
            flex: 1 1 0;
            min-width: 220px;
            max-width: 260px;
            min-height: 140px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            box-sizing: border-box;
            background: linear-gradient(135deg, #43a047 0%, #66bb6a 100%);
            color: white;
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 24px rgba(76,175,80,0.15);
            transition: transform 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
        }
        
        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .stats-label {
            font-size: 1rem;
            opacity: 0.9;
        }
        
        .progress-card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 24px rgba(76,175,80,0.10);
        }
        
        .module-progress {
            margin-bottom: 1rem;
            padding: 1rem;
            border-radius: 0.5rem;
            background: #f8f9fa;
            border-left: 4px solid #43a047;
        }
        
        .module-progress.completed {
            background: #e8f5e9;
            border-left-color: #2e7d32;
        }
        
        .progress-bar {
            height: 0.5rem;
            border-radius: 0.25rem;
        }
        
        .achievement-card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 4px 24px rgba(76,175,80,0.10);
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .achievement-icon {
            font-size: 2rem;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #43a047 0%, #66bb6a 100%);
            color: white;
            border-radius: 50%;
        }
        
        .streak-fire {
            color: #ff6b35;
            font-size: 1.5rem;
            animation: flicker 1.5s infinite alternate;
        }
        
        @keyframes flicker {
            0% { opacity: 1; }
            50% { opacity: 0.8; }
            100% { opacity: 1; }
        }
        
        .points-display {
            background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
            color: #333;
            border-radius: 1rem;
            padding: 1rem;
            text-align: center;
            font-weight: 700;
            font-size: 1.2rem;
            box-shadow: 0 4px 24px rgba(255,215,0,0.3);
        }
        
        .welcome-section {
            background: linear-gradient(135deg, #43a047 0%, #66bb6a 100%);
            color: white;
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .profile-pic {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid white;
            margin-bottom: 1rem;
        }
        
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .action-btn {
            background: white;
            border: none;
            border-radius: 1rem;
            padding: 1.5rem;
            text-align: center;
            text-decoration: none;
            color: #333;
            transition: all 0.3s ease;
            box-shadow: 0 4px 24px rgba(76,175,80,0.10);
        }
        
        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 32px rgba(76,175,80,0.20);
            color: #43a047;
        }
        
        .action-btn i {
            font-size: 2rem;
            color: #43a047;
            margin-bottom: 0.5rem;
            display: block;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container dashboard-container">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <img src="<?php echo $profilePic; ?>" alt="Profile" class="profile-pic">
            <h2>Welcome back, <?php echo htmlspecialchars($userInfo['name']); ?>! 🌱</h2>
            <p class="mb-0">Ready to grow your farming knowledge today?</p>
        </div>
        
        <!-- Quick Actions -->
        <div class="quick-actions">
            <a href="modulepage.php" class="action-btn">
                <i class="bi bi-book"></i>
                <div>Continue Learning</div>
            </a>
            <a href="Forum/community.php" class="action-btn">
                <i class="bi bi-people"></i>
                <div>Join Community</div>
            </a>
            <a href="plantinder.php" class="action-btn">
                <i class="bi bi-flower1"></i>
                <div>Discover Plants</div>
            </a>
            <a href="simulator.php" class="action-btn">
                <i class="bi bi-controller"></i>
                <div>Farming Simulator</div>
            </a>
        </div>
        
        <!-- Stats Row -->
        <div class="row stats-row">
            <div class="stats-card text-center">
                <div class="stats-number"><?php echo $totalPoints; ?></div>
                <div class="stats-label">Total Points</div>
            </div>
            <div class="stats-card text-center">
                <div class="stats-number"><?php echo $streakInfo['current_streak']; ?></div>
                <div class="stats-label">Day Streak</div>
            </div>
            <div class="stats-card text-center">
                <div class="stats-number"><?php echo $userAchievements->num_rows; ?></div>
                <div class="stats-label">Achievements</div>
            </div>
            <div class="stats-card text-center">
                <?php 
                $completedModules = 0;
                $userProgress->data_seek(0);
                while ($progress = $userProgress->fetch_assoc()) {
                    if ($progress['completion_percentage'] == 100) {
                        $completedModules++;
                    }
                }
                ?>
                <div class="stats-number"><?php echo $completedModules; ?>/6</div>
                <div class="stats-label">Modules Completed</div>
            </div>
        </div>
        
        <!-- Progress Section -->
        <div class="row">
            <div class="col-lg-8">
                <div class="progress-card">
                    <h4 class="mb-3">📚 Your Learning Progress</h4>
                    <?php 
                    $userProgress->data_seek(0);
                    while ($progress = $userProgress->fetch_assoc()): 
                        $percentage = $progress['completion_percentage'] ?? 0;
                        $isCompleted = $percentage == 100;
                    ?>
                    <div class="module-progress <?php echo $isCompleted ? 'completed' : ''; ?>">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0"><?php echo htmlspecialchars($progress['title']); ?></h6>
                            <span class="badge <?php echo $isCompleted ? 'bg-success' : 'bg-secondary'; ?>">
                                <?php echo $percentage; ?>%
                            </span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-success" style="width: <?php echo $percentage; ?>%"></div>
                        </div>
                        <?php if ($isCompleted): ?>
                        <small class="text-success mt-1">
                            <i class="bi bi-check-circle"></i> Completed on <?php echo date('M j, Y', strtotime($progress['completed_at'])); ?>
                        </small>
                        <?php endif; ?>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="progress-card">
                    <h4 class="mb-3">🏆 Recent Achievements</h4>
                    <?php if ($userAchievements->num_rows > 0): ?>
                        <?php 
                        $count = 0;
                        while (($achievement = $userAchievements->fetch_assoc()) && $count < 3): 
                            $count++;
                        ?>
                        <div class="achievement-card">
                            <div class="achievement-icon">
                                <?php echo $achievement['icon']; ?>
                            </div>
                            <div>
                                <h6 class="mb-1"><?php echo htmlspecialchars($achievement['name']); ?></h6>
                                <small class="text-muted"><?php echo htmlspecialchars($achievement['description']); ?></small>
                                <div class="text-success mt-1">
                                    <i class="bi bi-star-fill"></i> +<?php echo $achievement['points_reward']; ?> points
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                        <?php if ($userAchievements->num_rows > 3): ?>
                        <div class="text-center mt-3">
                            <a href="achievements.php" class="btn btn-outline-success btn-sm">View All Achievements</a>
                        </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-trophy" style="font-size: 3rem; opacity: 0.3;"></i>
                            <p class="mt-2">No achievements yet. Start learning to earn your first badge!</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Points Display -->
                <div class="points-display">
                    <div>💰 <?php echo $totalPoints; ?> Points</div>
                    <small>Keep learning to earn more!</small>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add some interactive animations
        document.addEventListener('DOMContentLoaded', function() {
            // Animate stats cards on load
            const statsCards = document.querySelectorAll('.stats-card');
            statsCards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    card.style.transition = 'all 0.5s ease';
                    
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 100);
                }, index * 100);
            });
            
            // Add click effects to action buttons
            const actionBtns = document.querySelectorAll('.action-btn');
            actionBtns.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    // Create ripple effect
                    const ripple = document.createElement('span');
                    ripple.style.position = 'absolute';
                    ripple.style.borderRadius = '50%';
                    ripple.style.background = 'rgba(255,255,255,0.6)';
                    ripple.style.transform = 'scale(0)';
                    ripple.style.animation = 'ripple 0.6s linear';
                    ripple.style.left = e.clientX - this.offsetLeft + 'px';
                    ripple.style.top = e.clientY - this.offsetTop + 'px';
                    
                    this.style.position = 'relative';
                    this.style.overflow = 'hidden';
                    this.appendChild(ripple);
                    
                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            });
        });
        
        // Add CSS for ripple effect
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html> 