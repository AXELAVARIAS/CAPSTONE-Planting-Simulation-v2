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

$module_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($module_id <= 0) {
    header("Location: modulepage.php");
    exit();
}

// Get module details
$sql = "SELECT * FROM modules WHERE module_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $module_id);
$stmt->execute();
$module = $stmt->get_result()->fetch_assoc();

if (!$module) {
    header("Location: modulepage.php");
    exit();
}

$gamification = new GamificationSystem($conn, $_SESSION['user_id']);

// Handle progress updates via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $response = ['success' => false, 'message' => ''];
    
    if ($_POST['action'] === 'update_progress') {
        $progress = (int)$_POST['progress'];
        if ($progress >= 0 && $progress <= 100) {
            $success = $gamification->updateModuleProgress($module_id, $progress);
            if ($success) {
                $response['success'] = true;
                $response['message'] = 'Progress updated successfully!';
                if ($progress == 100) {
                    $response['message'] = '🎉 Module completed! You earned 50 points!';
                }
            } else {
                $response['message'] = 'Failed to update progress.';
            }
        } else {
            $response['message'] = 'Invalid progress value.';
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Get current progress
$progressResult = $gamification->getUserProgress();
$currentProgress = 0;
while ($progress = $progressResult->fetch_assoc()) {
    if ($progress['module_id'] == $module_id) {
        $currentProgress = $progress['completion_percentage'] ?? 0;
        break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($module['title']); ?> - Teen-Anim</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/homepage.css">
    <style>
        body {
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
            font-family: 'Poppins', sans-serif;
        }
        
        .module-container {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 24px rgba(76,175,80,0.15);
            margin: 2rem 0;
            overflow: hidden;
        }
        
        .module-header {
            background: linear-gradient(135deg, #43a047 0%, #66bb6a 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .module-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .module-meta {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-bottom: 1rem;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.1rem;
        }
        
        .progress-section {
            background: #f8f9fa;
            padding: 1.5rem;
            border-bottom: 1px solid #e9ecef;
        }
        
        .progress-bar {
            height: 1rem;
            border-radius: 0.5rem;
            background: #e9ecef;
            overflow: hidden;
            margin-bottom: 1rem;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #43a047 0%, #66bb6a 100%);
            transition: width 0.5s ease;
            border-radius: 0.5rem;
        }
        
        .progress-text {
            display: flex;
            justify-content: between;
            align-items: center;
            font-weight: 600;
        }
        
        .module-content {
            padding: 2rem;
            min-height: 500px;
        }
        
        .content-section {
            margin-bottom: 2rem;
            padding: 1.5rem;
            border-radius: 0.5rem;
            background: #f8f9fa;
            border-left: 4px solid #43a047;
        }
        
        .content-section h3 {
            color: #43a047;
            margin-bottom: 1rem;
        }
        
        .interactive-element {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 0.5rem;
            padding: 1rem;
            margin: 1rem 0;
            transition: all 0.3s ease;
        }
        
        .interactive-element:hover {
            border-color: #43a047;
            box-shadow: 0 2px 8px rgba(76,175,80,0.1);
        }
        
        .completion-btn {
            background: linear-gradient(135deg, #43a047 0%, #66bb6a 100%);
            color: white;
            border: none;
            border-radius: 2rem;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .completion-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(76,175,80,0.3);
            color: white;
        }
        
        .completion-btn:disabled {
            background: #6c757d;
            transform: none;
            box-shadow: none;
        }
        
        .achievement-popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
            z-index: 1000;
            text-align: center;
            display: none;
        }
        
        .achievement-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
            display: none;
        }
        
        .navigation-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
            padding: 1rem 0;
            border-top: 1px solid #e9ecef;
        }
        
        .nav-btn {
            background: #43a047;
            color: white;
            border: none;
            border-radius: 2rem;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .nav-btn:hover {
            background: #388e3c;
            color: white;
            transform: translateY(-2px);
        }
        
        .nav-btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container">
        <div class="module-container">
            <!-- Module Header -->
            <div class="module-header">
                <h1><?php echo htmlspecialchars($module['title']); ?></h1>
                <div class="module-meta">
                    <div class="meta-item">
                        <i class="bi bi-tag"></i>
                        <span><?php echo htmlspecialchars($module['category']); ?></span>
                    </div>
                    <div class="meta-item">
                        <i class="bi bi-book"></i>
                        <span><?php echo htmlspecialchars($module['type']); ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Progress Section -->
            <div class="progress-section">
                <div class="progress-text">
                    <span>Your Progress</span>
                    <span id="progressPercentage"><?php echo $currentProgress; ?>%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" id="progressFill" style="width: <?php echo $currentProgress; ?>%"></div>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <button class="completion-btn" id="markComplete" <?php echo $currentProgress == 100 ? 'disabled' : ''; ?>>
                        <?php echo $currentProgress == 100 ? '✅ Completed' : 'Mark as Complete'; ?>
                    </button>
                    <small class="text-muted">Complete this module to earn 50 points!</small>
                </div>
            </div>
            
            <!-- Module Content -->
            <div class="module-content">
                <div class="content-section">
                    <h3><i class="bi bi-info-circle"></i> Description</h3>
                    <p><?php echo htmlspecialchars($module['description']); ?></p>
                </div>
                
                <div class="content-section">
                    <h3><i class="bi bi-book-open"></i> Learning Content</h3>
                    <div class="interactive-element">
                        <h5>📖 Module Content</h5>
                        <p>Click below to view the full module content:</p>
                        <a href="<?php echo htmlspecialchars($module['content']); ?>" target="_blank" class="btn btn-outline-success">
                            <i class="bi bi-file-earmark-text"></i> Open Module
                        </a>
                    </div>
                </div>
                
                <div class="content-section">
                    <h3><i class="bi bi-lightbulb"></i> Key Takeaways</h3>
                    <div class="interactive-element">
                        <ul>
                            <li>Understand the fundamentals of <?php echo strtolower($module['category']); ?></li>
                            <li>Learn practical techniques and strategies</li>
                            <li>Apply knowledge to real-world scenarios</li>
                            <li>Connect with the farming community</li>
                        </ul>
                    </div>
                </div>
                
                <div class="content-section">
                    <h3><i class="bi bi-question-circle"></i> Self-Assessment</h3>
                    <div class="interactive-element">
                        <p>Test your understanding of this module:</p>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="understanding1">
                            <label class="form-check-label" for="understanding1">
                                I understand the main concepts covered in this module
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="understanding2">
                            <label class="form-check-label" for="understanding2">
                                I can apply these techniques in practice
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="understanding3">
                            <label class="form-check-label" for="understanding3">
                                I'm ready to move to the next module
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Navigation -->
            <div class="navigation-buttons">
                <a href="modulepage.php" class="nav-btn">
                    <i class="bi bi-arrow-left"></i> Back to Modules
                </a>
                <a href="dashboard.php" class="nav-btn">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </div>
        </div>
    </div>
    
    <!-- Achievement Popup -->
    <div class="overlay" id="overlay"></div>
    <div class="achievement-popup" id="achievementPopup">
        <div class="achievement-icon">🎉</div>
        <h3>Module Completed!</h3>
        <p>Congratulations! You've successfully completed this module and earned 50 points!</p>
        <button class="completion-btn" onclick="closeAchievementPopup()">Continue</button>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentProgress = <?php echo $currentProgress; ?>;
        
        // Update progress
        function updateProgress(progress) {
            fetch('module_viewer.php?id=<?php echo $module_id; ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=update_progress&progress=${progress}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentProgress = progress;
                    document.getElementById('progressPercentage').textContent = progress + '%';
                    document.getElementById('progressFill').style.width = progress + '%';
                    
                    if (progress == 100) {
                        document.getElementById('markComplete').textContent = '✅ Completed';
                        document.getElementById('markComplete').disabled = true;
                        showAchievementPopup();
                    }
                    
                    // Show success message
                    showNotification(data.message, 'success');
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while updating progress.', 'error');
            });
        }
        
        // Mark as complete
        document.getElementById('markComplete').addEventListener('click', function() {
            updateProgress(100);
        });
        
        // Self-assessment checkboxes
        const checkboxes = document.querySelectorAll('.form-check-input');
        checkboxes.forEach((checkbox, index) => {
            checkbox.addEventListener('change', function() {
                const checkedCount = document.querySelectorAll('.form-check-input:checked').length;
                const progress = Math.round((checkedCount / checkboxes.length) * 100);
                updateProgress(progress);
            });
        });
        
        // Show achievement popup
        function showAchievementPopup() {
            document.getElementById('overlay').style.display = 'block';
            document.getElementById('achievementPopup').style.display = 'block';
        }
        
        // Close achievement popup
        function closeAchievementPopup() {
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('achievementPopup').style.display = 'none';
        }
        
        // Show notification
        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `alert alert-${type === 'success' ? 'success' : 'danger'} position-fixed`;
            notification.style.cssText = 'top: 100px; right: 20px; z-index: 1000; min-width: 300px;';
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
        
        // Add some interactive animations
        document.addEventListener('DOMContentLoaded', function() {
            const interactiveElements = document.querySelectorAll('.interactive-element');
            interactiveElements.forEach((element, index) => {
                setTimeout(() => {
                    element.style.opacity = '0';
                    element.style.transform = 'translateY(20px)';
                    element.style.transition = 'all 0.5s ease';
                    
                    setTimeout(() => {
                        element.style.opacity = '1';
                        element.style.transform = 'translateY(0)';
                    }, 100);
                }, index * 100);
            });
        });
    </script>
</body>
</html> 