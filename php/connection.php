<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "capstone";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if($conn->connect_error){
        die("Connection Failed". $conn->connect_error);
    }

    // Create gamification tables if they don't exist
    $tables = [
        "CREATE TABLE IF NOT EXISTS user_progress (
            progress_id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            module_id INT NOT NULL,
            completion_percentage INT DEFAULT 0,
            completed_at TIMESTAMP NULL,
            points_earned INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(user_id),
            FOREIGN KEY (module_id) REFERENCES modules(module_id)
        )",
        
        "CREATE TABLE IF NOT EXISTS achievements (
            achievement_id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description TEXT NOT NULL,
            icon VARCHAR(255) NOT NULL,
            points_reward INT DEFAULT 0,
            criteria_type ENUM('module_completion', 'community_posts', 'plants_discovered', 'simulation_time', 'streak_days') NOT NULL,
            criteria_value INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        "CREATE TABLE IF NOT EXISTS user_achievements (
            user_achievement_id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            achievement_id INT NOT NULL,
            earned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(user_id),
            FOREIGN KEY (achievement_id) REFERENCES achievements(achievement_id)
        )",
        
        "CREATE TABLE IF NOT EXISTS user_points (
            point_id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            points INT NOT NULL,
            source ENUM('module_completion', 'achievement', 'community_post', 'daily_login', 'plant_discovery') NOT NULL,
            description VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(user_id)
        )",
        
        "CREATE TABLE IF NOT EXISTS user_streaks (
            streak_id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            current_streak INT DEFAULT 0,
            longest_streak INT DEFAULT 0,
            last_login_date DATE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(user_id)
        )"
    ];

    foreach ($tables as $sql) {
        if (!$conn->query($sql)) {
            echo "Error creating table: " . $conn->error;
        }
    }

    // Insert default achievements if they don't exist
    $achievements = [
        "INSERT IGNORE INTO achievements (name, description, icon, points_reward, criteria_type, criteria_value) VALUES
        ('First Steps', 'Complete your first module', '🌱', 50, 'module_completion', 1),
        ('Knowledge Seeker', 'Complete 5 modules', '📚', 100, 'module_completion', 5),
        ('Urban Farmer', 'Complete 10 modules', '🏙️', 200, 'module_completion', 10),
        ('Community Builder', 'Make 5 forum posts', '👥', 75, 'community_posts', 5),
        ('Plant Explorer', 'Discover 10 different plants', '🌿', 150, 'plants_discovered', 10),
        ('Simulation Master', 'Spend 2 hours in farming simulation', '🎮', 100, 'simulation_time', 120),
        ('Dedicated Learner', 'Maintain a 7-day login streak', '🔥', 200, 'streak_days', 7),
        ('Expert Gardener', 'Complete all modules', '👨‍🌾', 500, 'module_completion', 6)"
    ];

    foreach ($achievements as $sql) {
        if (!$conn->query($sql)) {
            echo "Error inserting achievements: " . $conn->error;
        }
    }
?>