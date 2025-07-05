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

// Get leaderboard data
$sql = "SELECT 
            u.user_id,
            u.name,
            u.username,
            u.profile_picture,
            COALESCE(SUM(up.points), 0) as total_points,
            COUNT(DISTINCT ua.achievement_id) as achievements_count,
            COALESCE(us.current_streak, 0) as current_streak,
            COALESCE(us.longest_streak, 0) as longest_streak,
            COUNT(DISTINCT up2.module_id) as completed_modules
        FROM users u
        LEFT JOIN user_points up ON u.user_id = up.user_id
        LEFT JOIN user_achievements ua ON u.user_id = ua.user_id
        LEFT JOIN user_streaks us ON u.user_id = us.user_id
        LEFT JOIN user_progress up2 ON u.user_id = up2.user_id AND up2.completion_percentage = 100
        WHERE u.status = 'active'
        GROUP BY u.user_id, u.name, u.username, u.profile_picture, us.current_streak, us.longest_streak
        ORDER BY total_points DESC, achievements_count DESC, current_streak DESC
        LIMIT 20";

$result = $conn->query($sql);

// Get current user's rank
$currentUserRank = 0;
$currentUserData = null;
$rank = 1;
while ($row = $result->fetch_assoc()) {
    if ($row['user_id'] == $_SESSION['user_id']) {
        $currentUserRank = $rank;
        $currentUserData = $row;
        break;
    }
    $rank++;
}

// Reset result pointer
$result->data_seek(0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard - Teen-Anim</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/homepage.css">
    <style>
        body {
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
            font-family: 'Poppins', sans-serif;
        }
        
        .leaderboard-container {
            padding: 2rem 0;
        }
        
        .leaderboard-header {
            background: linear-gradient(135deg, #43a047 0%, #66bb6a 100%);
            color: white;
            border-radius: 1rem;
            padding: 2rem;
            text-align: center;
            margin-bottom: 2rem;
            box-shadow: 0 4px 24px rgba(76,175,80,0.15);
        }
        
        .leaderboard-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .leaderboard-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 24px rgba(76,175,80,0.10);
            overflow: hidden;
        }
        
        .leaderboard-item {
            display: flex;
            align-items: center;
            padding: 1.5rem;
            border-bottom: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }
        
        .leaderboard-item:hover {
            background: #f8f9fa;
            transform: translateX(5px);
        }
        
        .leaderboard-item:last-child {
            border-bottom: none;
        }
        
        .rank-badge {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.2rem;
            margin-right: 1rem;
        }
        
        .rank-1 { background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%); color: #333; }
        .rank-2 { background: linear-gradient(135deg, #c0c0c0 0%, #e0e0e0 100%); color: #333; }
        .rank-3 { background: linear-gradient(135deg, #cd7f32 0%, #daa520 100%); color: white; }
        .rank-other { background: #e9ecef; color: #6c757d; }
        
        .user-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #43a047;
            margin-right: 1rem;
        }
        
        .user-info {
            flex: 1;
        }
        
        .user-name {
            font-weight: 600;
            font-size: 1.1rem;
            color: #333;
            margin-bottom: 0.25rem;
        }
        
        .user-stats {
            display: flex;
            gap: 1rem;
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .stat-item {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        
        .points-display {
            background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
            color: #333;
            border-radius: 2rem;
            padding: 0.5rem 1rem;
            font-weight: 700;
            font-size: 1.1rem;
            box-shadow: 0 2px 8px rgba(255,215,0,0.3);
        }
        
        .current-user-highlight {
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
            border-left: 4px solid #43a047;
        }
        
        .achievement-badges {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }
        
        .achievement-badge {
            background: #43a047;
            color: white;
            border-radius: 1rem;
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .streak-fire {
            color: #ff6b35;
            animation: flicker 1.5s infinite alternate;
        }
        
        @keyframes flicker {
            0% { opacity: 1; }
            50% { opacity: 0.8; }
            100% { opacity: 1; }
        }
        
        .filter-tabs {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .filter-tab {
            background: white;
            border: 2px solid #43a047;
            color: #43a047;
            border-radius: 2rem;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .filter-tab.active,
        .filter-tab:hover {
            background: #43a047;
            color: white;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container leaderboard-container">
        <!-- Header -->
        <div class="leaderboard-header">
            <h1>🏆 Leaderboard</h1>
            <p class="mb-0">Compete with fellow farmers and climb the ranks!</p>
        </div>
        
        <!-- Filter Tabs -->
        <div class="filter-tabs">
            <a href="#" class="filter-tab active" data-filter="points">Points</a>
            <a href="#" class="filter-tab" data-filter="achievements">Achievements</a>
            <a href="#" class="filter-tab" data-filter="streaks">Streaks</a>
            <a href="#" class="filter-tab" data-filter="modules">Modules</a>
        </div>
        
        <!-- Current User Status -->
        <?php if ($currentUserData): ?>
        <div class="alert alert-success mb-4" style="border-radius: 1rem; border: none;">
            <div class="d-flex align-items-center">
                <img src="<?php
                    if (empty($currentUserData['profile_picture']) || $currentUserData['profile_picture'] === 'clearteenalogo.png') {
                        echo '/CAPSTONE-Planting-Simulation/images/clearteenalogo.png';
                    } else {
                        echo '/CAPSTONE-Planting-Simulation/images/profile_pics/' . $currentUserData['profile_picture'];
                    }
                ?>" 
                     alt="Your Profile" style="width: 40px; height: 40px; border-radius: 50%; margin-right: 1rem;">
                <div>
                    <strong>Your Rank: #<?php echo $currentUserRank; ?></strong>
                    <div class="text-muted">
                        <?php echo $currentUserData['total_points']; ?> points • 
                        <?php echo $currentUserData['achievements_count']; ?> achievements • 
                        <span class="streak-fire">🔥 <?php echo $currentUserData['current_streak']; ?> day streak</span>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Leaderboard -->
        <div class="leaderboard-card">
            <?php 
            $rank = 1;
            while ($row = $result->fetch_assoc()): 
                $isCurrentUser = $row['user_id'] == $_SESSION['user_id'];
                $rankClass = $rank <= 3 ? "rank-$rank" : "rank-other";
            ?>
            <div class="leaderboard-item <?php echo $isCurrentUser ? 'current-user-highlight' : ''; ?>">
                <div class="rank-badge <?php echo $rankClass; ?>">
                    <?php echo $rank; ?>
                </div>
                
                <img src="<?php
                    if (empty($row['profile_picture']) || $row['profile_picture'] === 'clearteenalogo.png') {
                        echo '/CAPSTONE-Planting-Simulation/images/clearteenalogo.png';
                    } else {
                        echo '/CAPSTONE-Planting-Simulation/images/profile_pics/' . $row['profile_picture'];
                    }
                ?>" 
                     alt="Profile" class="user-avatar">
                
                <div class="user-info">
                    <div class="user-name">
                        <?php echo htmlspecialchars($row['name']); ?>
                        <?php if ($isCurrentUser): ?>
                        <span class="badge bg-success ms-2">You</span>
                        <?php endif; ?>
                    </div>
                    <div class="user-stats">
                        <div class="stat-item">
                            <i class="bi bi-star-fill text-warning"></i>
                            <span><?php echo $row['achievements_count']; ?> achievements</span>
                        </div>
                        <div class="stat-item">
                            <i class="bi bi-book-fill text-primary"></i>
                            <span><?php echo $row['completed_modules']; ?> modules</span>
                        </div>
                        <div class="stat-item">
                            <span class="streak-fire">🔥</span>
                            <span><?php echo $row['current_streak']; ?> day streak</span>
                        </div>
                    </div>
                    
                    <!-- Achievement badges for top 3 -->
                    <?php if ($rank <= 3 && $row['achievements_count'] > 0): ?>
                    <div class="achievement-badges">
                        <span class="achievement-badge">🏆 Top Performer</span>
                        <?php if ($row['achievements_count'] >= 5): ?>
                        <span class="achievement-badge">⭐ Achievement Hunter</span>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="points-display">
                    <?php echo number_format($row['total_points']); ?> pts
                </div>
            </div>
            <?php 
            $rank++;
            endwhile; 
            ?>
        </div>
        
        <!-- Motivation Section -->
        <div class="text-center mt-4">
            <h4>💪 Keep Learning, Keep Growing!</h4>
            <p class="text-muted">Complete modules, earn achievements, and maintain your streak to climb the leaderboard!</p>
            <a href="dashboard.php" class="btn btn-success btn-lg">
                <i class="bi bi-speedometer2"></i> Go to Dashboard
            </a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Filter tabs functionality
        document.querySelectorAll('.filter-tab').forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remove active class from all tabs
                document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
                
                // Add active class to clicked tab
                this.classList.add('active');
                
                // Here you would implement the actual filtering logic
                // For now, we'll just show a message
                const filter = this.dataset.filter;
                console.log('Filtering by:', filter);
            });
        });
        
        // Add some animations
        document.addEventListener('DOMContentLoaded', function() {
            const leaderboardItems = document.querySelectorAll('.leaderboard-item');
            leaderboardItems.forEach((item, index) => {
                setTimeout(() => {
                    item.style.opacity = '0';
                    item.style.transform = 'translateX(-20px)';
                    item.style.transition = 'all 0.5s ease';
                    
                    setTimeout(() => {
                        item.style.opacity = '1';
                        item.style.transform = 'translateX(0)';
                    }, 100);
                }, index * 100);
            });
        });
    </script>
</body>
</html> 