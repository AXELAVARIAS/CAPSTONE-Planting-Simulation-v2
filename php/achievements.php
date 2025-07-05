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

// Get all achievements
$sql = "SELECT * FROM achievements ORDER BY points_reward ASC";
$allAchievements = $conn->query($sql);

// Get user's earned achievements
$userAchievements = $gamification->getUserAchievements();
$earnedAchievementIds = [];
while ($achievement = $userAchievements->fetch_assoc()) {
    $earnedAchievementIds[] = $achievement['achievement_id'];
}

// Get user stats for progress calculation
$userProgress = $gamification->getUserProgress();
$completedModules = 0;
$userProgress->data_seek(0);
while ($progress = $userProgress->fetch_assoc()) {
    if ($progress['completion_percentage'] == 100) {
        $completedModules++;
    }
}

// Get other stats
$sql = "SELECT COUNT(*) as post_count FROM questions WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$postCount = $stmt->get_result()->fetch_assoc()['post_count'];

$sql = "SELECT COUNT(*) as plant_count FROM favorites WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$plantCount = $stmt->get_result()->fetch_assoc()['plant_count'];

$streakInfo = $gamification->getStreakInfo();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Achievements - Teen-Anim</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/homepage.css">
    <style>
        body {
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
            font-family: 'Poppins', sans-serif;
        }
        
        .achievements-container {
            padding: 2rem 0;
        }
        
        .achievements-header {
            background: linear-gradient(135deg, #43a047 0%, #66bb6a 100%);
            color: white;
            border-radius: 1rem;
            padding: 2rem;
            text-align: center;
            margin-bottom: 2rem;
            box-shadow: 0 4px 24px rgba(76,175,80,0.15);
        }
        
        .achievements-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .stats-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 4px 24px rgba(76,175,80,0.10);
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #43a047;
            margin-bottom: 0.5rem;
        }
        
        .achievement-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        
        .achievement-card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 4px 24px rgba(76,175,80,0.10);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .achievement-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 32px rgba(76,175,80,0.20);
        }
        
        .achievement-card.earned {
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
            border: 2px solid #43a047;
        }
        
        .achievement-card.earned::before {
            content: '✅';
            position: absolute;
            top: 1rem;
            right: 1rem;
            font-size: 1.5rem;
        }
        
        .achievement-icon {
            font-size: 3rem;
            text-align: center;
            margin-bottom: 1rem;
        }
        
        .achievement-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }
        
        .achievement-description {
            color: #666;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        
        .achievement-reward {
            background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
            color: #333;
            border-radius: 1rem;
            padding: 0.5rem 1rem;
            font-weight: 700;
            display: inline-block;
            margin-bottom: 1rem;
        }
        
        .progress-section {
            margin-top: 1rem;
        }
        
        .progress-label {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 0.5rem;
        }
        
        .progress-bar {
            height: 0.5rem;
            background: #e9ecef;
            border-radius: 0.25rem;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #43a047 0%, #66bb6a 100%);
            transition: width 0.5s ease;
            border-radius: 0.25rem;
        }
        
        .progress-text {
            font-size: 0.8rem;
            color: #666;
            margin-top: 0.25rem;
        }
        
        .locked-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #999;
        }
        
        .category-filter {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        
        .filter-btn {
            background: white;
            border: 2px solid #43a047;
            color: #43a047;
            border-radius: 2rem;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .filter-btn.active,
        .filter-btn:hover {
            background: #43a047;
            color: white;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container achievements-container">
        <!-- Header -->
        <div class="achievements-header">
            <h1>🏆 Achievements</h1>
            <p class="mb-0">Unlock badges by completing challenges and growing your farming knowledge!</p>
        </div>
        
        <!-- Stats Summary -->
        <div class="stats-summary">
            <div class="stat-card">
                <div class="stat-number"><?php echo count($earnedAchievementIds); ?></div>
                <div>Earned</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $allAchievements->num_rows; ?></div>
                <div>Total Available</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $completedModules; ?></div>
                <div>Modules Completed</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $streakInfo['current_streak']; ?></div>
                <div>Day Streak</div>
            </div>
        </div>
        
        <!-- Category Filter -->
        <div class="category-filter">
            <button class="filter-btn active" data-filter="all">All</button>
            <button class="filter-btn" data-filter="module_completion">Learning</button>
            <button class="filter-btn" data-filter="community_posts">Community</button>
            <button class="filter-btn" data-filter="plants_discovered">Discovery</button>
            <button class="filter-btn" data-filter="streak_days">Streaks</button>
        </div>
        
        <!-- Achievements Grid -->
        <div class="achievement-grid">
            <?php 
            $allAchievements->data_seek(0);
            while ($achievement = $allAchievements->fetch_assoc()): 
                $isEarned = in_array($achievement['achievement_id'], $earnedAchievementIds);
                $progress = 0;
                $current = 0;
                $target = $achievement['criteria_value'];
                
                // Calculate progress based on achievement type
                switch ($achievement['criteria_type']) {
                    case 'module_completion':
                        $current = $completedModules;
                        $progress = min(100, ($current / $target) * 100);
                        break;
                    case 'community_posts':
                        $current = $postCount;
                        $progress = min(100, ($current / $target) * 100);
                        break;
                    case 'plants_discovered':
                        $current = $plantCount;
                        $progress = min(100, ($current / $target) * 100);
                        break;
                    case 'streak_days':
                        $current = $streakInfo['current_streak'];
                        $progress = min(100, ($current / $target) * 100);
                        break;
                }
            ?>
            <div class="achievement-card <?php echo $isEarned ? 'earned' : ''; ?>" data-category="<?php echo $achievement['criteria_type']; ?>">
                <div class="achievement-icon"><?php echo $achievement['icon']; ?></div>
                <div class="achievement-title"><?php echo htmlspecialchars($achievement['name']); ?></div>
                <div class="achievement-description"><?php echo htmlspecialchars($achievement['description']); ?></div>
                <div class="achievement-reward">+<?php echo $achievement['points_reward']; ?> points</div>
                
                <?php if (!$isEarned): ?>
                <div class="progress-section">
                    <div class="progress-label">Progress: <?php echo $current; ?>/<?php echo $target; ?></div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo $progress; ?>%"></div>
                    </div>
                    <div class="progress-text"><?php echo round($progress); ?>% complete</div>
                </div>
                <?php endif; ?>
                
                <?php if (!$isEarned): ?>
                <div class="locked-overlay">
                    🔒
                </div>
                <?php endif; ?>
            </div>
            <?php endwhile; ?>
        </div>
        
        <!-- Motivation Section -->
        <div class="text-center mt-5">
            <h4>🎯 Keep Going!</h4>
            <p class="text-muted">Complete more modules, engage with the community, and maintain your streak to unlock more achievements!</p>
            <a href="dashboard.php" class="btn btn-success btn-lg me-3">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="leaderboard.php" class="btn btn-outline-success btn-lg">
                <i class="bi bi-trophy"></i> Leaderboard
            </a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Filter functionality
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const filter = this.dataset.filter;
                
                // Update active button
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                // Filter achievements
                document.querySelectorAll('.achievement-card').forEach(card => {
                    if (filter === 'all' || card.dataset.category === filter) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
        
        // Add animations
        document.addEventListener('DOMContentLoaded', function() {
            const achievementCards = document.querySelectorAll('.achievement-card');
            achievementCards.forEach((card, index) => {
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
        });
    </script>
</body>
</html> 