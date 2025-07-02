<?php

  session_start();
  header("Cache-Control: no-cache, no-store, must-revalidate");
  header("Pragma: no-cache");
  header("Expires: 0");

  if (!isset($_SESSION['user_id'])) {
      header("Location: ../login.php");
      exit();
  }
  
  include "../connection.php"; 

  $sql = "SELECT q.question_id, q.title, q.body, q.created_at, u.username 
          FROM questions q
          JOIN users u ON q.user_id = u.user_id
          ORDER BY q.created_at DESC
          LIMIT 10";
  $result = $conn->query($sql);
  
  
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Farming Community</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../../css/homepage.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <style>
    html, body {
      height: 100%;
    }
    body {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
    }
    .main-content {
      flex: 1 0 auto;
    }
    footer {
      flex-shrink: 0;
    }
    .community-header {
      background: linear-gradient(90deg, #43a047 0%, #66bb6a 100%);
      color: #fff;
      border-radius: 1rem 1rem 0 0;
      padding: 1.5rem 2rem 1rem 2rem;
      margin-bottom: 2rem;
      box-shadow: 0 4px 24px rgba(76,175,80,0.08);
    }
    .discussion-form-card {
      border-radius: 1rem;
      box-shadow: 0 4px 24px rgba(76,175,80,0.10);
      margin-bottom: 2rem;
      overflow: hidden;
    }
    .discussion-form-card .card-header {
      background: #388e3c;
      color: #fff;
      font-weight: 600;
      font-size: 1.2rem;
      border-radius: 1rem 1rem 0 0;
    }
    .discussion-form-card .btn-success {
      border-radius: 2rem;
      font-weight: 600;
      padding: 0.5rem 2rem;
    }
    .discussion-list-card {
      border-radius: 1rem;
      box-shadow: 0 4px 24px rgba(76,175,80,0.10);
      overflow: hidden;
    }
    .discussion-list-card .card-header {
      background: #18804b;
      color: #fff;
      font-weight: 600;
      font-size: 1.1rem;
      border-radius: 1rem 1rem 0 0;
    }
    .discussion-item {
      transition: background 0.15s, box-shadow 0.15s;
      border-bottom: 1px solid #e0e0e0;
      padding: 1rem 1.2rem;
      display: flex;
      align-items: flex-start;
      gap: 1rem;
      background: #fff;
    }
    .discussion-item:last-child {
      border-bottom: none;
    }
    .discussion-item:hover {
      background: #f1f8e9;
      box-shadow: 0 2px 12px rgba(76,175,80,0.08);
    }
    .discussion-title {
      font-size: 1.1rem;
      font-weight: 600;
      color: #388e3c;
      margin-bottom: 0.2rem;
      text-decoration: none;
      transition: color 0.15s;
    }
    .discussion-title:hover {
      color: #256029;
      text-decoration: underline;
    }
    .discussion-meta {
      font-size: 0.95rem;
      color: #888;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    .discussion-delete {
      margin-left: auto;
      color: #e53935;
      background: #fff;
      border: none;
      border-radius: 50%;
      width: 32px;
      height: 32px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.2rem;
      transition: background 0.2s;
    }
    .discussion-delete:hover {
      background: #e53935;
      color: #fff;
    }
    @media (max-width: 767.98px) {
      .community-header {
        padding: 1rem 0.5rem 0.7rem 0.5rem;
      }
      .discussion-item {
        flex-direction: column;
        gap: 0.3rem;
        padding: 0.8rem 0.5rem;
      }
    }
  </style>
</head>
<body>
<?php include '../navbar.php'; ?>
<div class="main-content">
  <div class="container py-4">
    <div class="community-header mb-4" data-aos="fade-down">
      <h2 class="mb-1"><i class="bi bi-people-fill me-2"></i>Farming Community</h2>
      <p class="mb-0">Connect, share, and grow with fellow young farmers. Start a new discussion or join the conversation!</p>
    </div>
    <div class="row justify-content-center g-4">
      <div class="col-12 col-lg-5">
        <div class="card discussion-form-card mb-4 mb-lg-0" data-aos="fade-up">
          <div class="card-header">
            <i class="bi bi-chat-dots me-2"></i>Start a New Discussion
          </div>
          <div class="card-body">
            <form method="POST" action="addquestion.php">
              <div class="mb-3">
                  <label for="threadTitle" class="form-label">Thread Title</label>
                  <input type="text" id="threadTitle" name="title" class="form-control" placeholder="Enter title" required>
              </div>
              <div class="mb-3">
                  <label for="threadMessage" class="form-label">Message</label>
                  <textarea id="threadMessage" name="body" class="form-control" rows="5" placeholder="Write your message here..." required></textarea>
              </div>
              <button type="submit" class="btn btn-success">Create Thread</button>
            </form>
          </div>
        </div>
      </div>
      <div class="col-12 col-lg-7">
        <div class="card discussion-list-card" data-aos="fade-up" data-aos-delay="100">
          <div class="card-header">
            <i class="bi bi-clock-history me-2"></i>Recent Discussions
          </div>
          <div class="list-group list-group-flush">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="discussion-item list-group-item">
                        <div>
                            <a href="thread.php?id=<?= $row['question_id'] ?>" class="discussion-title">
                                <?= htmlspecialchars($row['title']) ?>
                            </a>
                            <div class="discussion-meta">
                              <i class="bi bi-person-circle"></i> <?= htmlspecialchars($row['username']) ?>
                              <i class="bi bi-calendar-event ms-2"></i> <?= date('M d, Y', strtotime($row['created_at'])) ?>
                            </div>
                        </div>
                        <?php if ($_SESSION['role'] === 'agriculturist'): ?>
                            <form method="GET" action="delete.php" onsubmit="return confirm('Are you sure you want to delete this question?');">
                              <input type="hidden" name="type" value="question">
                              <input type="hidden" name="id" value="<?= $row['question_id'] ?>">
                              <button type="submit" class="discussion-delete" title="Delete"><i class="bi bi-x"></i></button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="discussion-item list-group-item">
                    <p class="mb-0 text-muted">No questions yet. Start the first discussion!</p>
                </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<footer class="bg-success text-center text-white py-3 mt-auto">
  <p class="mb-0">&copy; 2024 Farming Community. All rights reserved.</p>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
AOS.init();
</script>
</body>
</html>
