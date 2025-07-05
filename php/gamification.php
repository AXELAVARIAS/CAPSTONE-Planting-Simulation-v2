<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'connection.php';

class GamificationSystem {
    private $conn;
    private $user_id;
    
    public function __construct($conn, $user_id) {
        $this->conn = $conn;
        $this->user_id = $user_id;
    }
    
    // Award points to user
    public function awardPoints($points, $source, $description = '') {
        $sql = "INSERT INTO user_points (user_id, points, source, description) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iiss", $this->user_id, $points, $source, $description);
        return $stmt->execute();
    }
    
    // Get user's total points
    public function getTotalPoints() {
        $sql = "SELECT COALESCE(SUM(points), 0) as total_points FROM user_points WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $this->user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result ? $result->fetch_assoc() : null;
        return $row ? $row['total_points'] : 0;
    }
    
    // Update module progress
    public function updateModuleProgress($module_id, $completion_percentage) {
        // Check if progress record exists
        $sql = "SELECT * FROM user_progress WHERE user_id = ? AND module_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $this->user_id, $module_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Update existing record
            $sql = "UPDATE user_progress SET completion_percentage = ?, completed_at = CASE WHEN ? = 100 THEN NOW() ELSE completed_at END WHERE user_id = ? AND module_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("iiii", $completion_percentage, $completion_percentage, $this->user_id, $module_id);
        } else {
            // Create new record
            $sql = "INSERT INTO user_progress (user_id, module_id, completion_percentage, completed_at) VALUES (?, ?, ?, CASE WHEN ? = 100 THEN NOW() ELSE NULL END)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("iiii", $this->user_id, $module_id, $completion_percentage, $completion_percentage);
        }
        
        $success = $stmt->execute();
        
        // Award points for completion
        if ($completion_percentage == 100) {
            $this->awardPoints(50, 'module_completion', 'Module completed!');
            $this->checkAchievements();
        }
        
        return $success;
    }
    
    // Check and award achievements
    public function checkAchievements() {
        $sql = "SELECT * FROM achievements WHERE achievement_id NOT IN (SELECT achievement_id FROM user_achievements WHERE user_id = ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $this->user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($achievement = $result->fetch_assoc()) {
            if ($this->hasEarnedAchievement($achievement)) {
                $this->awardAchievement($achievement['achievement_id']);
            }
        }
    }
    
    // Check if user has earned a specific achievement
    private function hasEarnedAchievement($achievement) {
        switch ($achievement['criteria_type']) {
            case 'module_completion':
                $sql = "SELECT COUNT(*) as count FROM user_progress WHERE user_id = ? AND completion_percentage = 100";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("i", $this->user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result ? $result->fetch_assoc() : null;
                return $row ? $row['count'] >= $achievement['criteria_value'] : false;
                
            case 'community_posts':
                $sql = "SELECT COUNT(*) as count FROM questions WHERE user_id = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("i", $this->user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result ? $result->fetch_assoc() : null;
                return $row ? $row['count'] >= $achievement['criteria_value'] : false;
                
            case 'plants_discovered':
                $sql = "SELECT COUNT(*) as count FROM favorites WHERE user_id = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("i", $this->user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result ? $result->fetch_assoc() : null;
                return $row ? $row['count'] >= $achievement['criteria_value'] : false;
                
            default:
                return false;
        }
    }
    
    // Award achievement to user
    private function awardAchievement($achievement_id) {
        // Get achievement details
        $sql = "SELECT * FROM achievements WHERE achievement_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $achievement_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $achievement = $result ? $result->fetch_assoc() : null;
        
        if (!$achievement) {
            return false;
        }
        
        // Award achievement
        $sql = "INSERT INTO user_achievements (user_id, achievement_id) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $this->user_id, $achievement_id);
        $stmt->execute();
        
        // Award points
        $this->awardPoints($achievement['points_reward'], 'achievement', 'Achievement: ' . $achievement['name']);
        
        return $achievement;
    }
    
    // Get user's achievements
    public function getUserAchievements() {
        $sql = "SELECT a.*, ua.earned_at FROM achievements a 
                JOIN user_achievements ua ON a.achievement_id = ua.achievement_id 
                WHERE ua.user_id = ? 
                ORDER BY ua.earned_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $this->user_id);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    // Get user's progress
    public function getUserProgress() {
        $sql = "SELECT m.title, m.module_id, up.completion_percentage, up.completed_at 
                FROM modules m 
                LEFT JOIN user_progress up ON m.module_id = up.module_id AND up.user_id = ? 
                ORDER BY m.module_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $this->user_id);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    // Update login streak
    public function updateLoginStreak() {
        $today = date('Y-m-d');
        
        // Check if streak record exists
        $sql = "SELECT * FROM user_streaks WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $this->user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $streak = $result->fetch_assoc();
            $last_login = $streak['last_login_date'];
            
            if ($last_login == $today) {
                return; // Already logged in today
            }
            
            $yesterday = date('Y-m-d', strtotime('-1 day'));
            if ($last_login == $yesterday) {
                // Consecutive day
                $new_streak = $streak['current_streak'] + 1;
                $longest_streak = max($streak['longest_streak'], $new_streak);
            } else {
                // Break in streak
                $new_streak = 1;
                $longest_streak = $streak['longest_streak'];
            }
            
            $sql = "UPDATE user_streaks SET current_streak = ?, longest_streak = ?, last_login_date = ? WHERE user_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("iisi", $new_streak, $longest_streak, $today, $this->user_id);
        } else {
            // First login
            $sql = "INSERT INTO user_streaks (user_id, current_streak, longest_streak, last_login_date) VALUES (?, 1, 1, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("is", $this->user_id, $today);
        }
        
        $stmt->execute();
        
        // Award daily login points
        $this->awardPoints(10, 'daily_login', 'Daily login bonus!');
    }
    
    // Get user's streak info
    public function getStreakInfo() {
        $sql = "SELECT current_streak, longest_streak FROM user_streaks WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $this->user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return ['current_streak' => 0, 'longest_streak' => 0];
        }
    }
}

// Initialize gamification system for logged-in user
if (isset($_SESSION['user_id'])) {
    $gamification = new GamificationSystem($conn, $_SESSION['user_id']);
    $gamification->updateLoginStreak();
}
?> 