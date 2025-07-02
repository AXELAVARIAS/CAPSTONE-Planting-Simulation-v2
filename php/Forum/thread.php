<?php
session_start();
include "../connection.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php"); 
    exit;
}

if (isset($_GET['id'])) {
    $question_id = intval($_GET['id']);

    $stmt = $conn->prepare("SELECT q.title, q.body, q.created_at, u.username FROM Questions q JOIN Users u ON q.user_id = u.user_id WHERE q.question_id = ?");
    $stmt->bind_param("i", $question_id);
    $stmt->execute();
    $stmt->bind_result($title, $body, $created_at, $username);
    $stmt->fetch();
    $stmt->close();

    $reply_stmt = $conn->prepare("SELECT r.reply_id, r.body, r.created_at, u.username FROM reply r JOIN Users u ON r.user_id = u.user_id WHERE r.question_id = ? ORDER BY r.created_at ASC");
    $reply_stmt->bind_param("i", $question_id);
    $reply_stmt->execute();
    $replies = $reply_stmt->get_result();
} else {
    echo "No discussion selected.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $reply_body = $_POST['reply'];
    $user_id = $_SESSION['user_id'];

    $insert_reply = $conn->prepare("INSERT INTO reply (question_id, user_id, body) VALUES (?, ?, ?)");
    $insert_reply->bind_param("iis", $question_id, $user_id, $reply_body);

    if ($insert_reply->execute()) {
        header("Location: thread.php?id=" . $question_id);
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
    $insert_reply->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discussion - <?= htmlspecialchars($title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../css/homepage.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body, html {
            height: 100%;
            display: flex;
            flex-direction: column;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
        }
        main {
            flex: 1;
        }
        .thread-card {
            border-radius: 1.2rem;
            box-shadow: 0 4px 24px rgba(76,175,80,0.10);
            margin-bottom: 2rem;
            overflow: hidden;
        }
        .thread-header {
            display: flex;
            align-items: center;
            gap: 1.2rem;
            background: #43a047;
            color: #fff;
            padding: 1.5rem 2rem 1rem 2rem;
        }
        .thread-avatar {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            color: #43a047;
            border: 3px solid #fff;
        }
        .thread-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.2rem;
        }
        .thread-meta {
            font-size: 1rem;
            color: #e0e0e0;
        }
        .thread-body {
            background: #fff;
            padding: 2rem;
            font-size: 1.15rem;
        }
        .reply-card {
            border-radius: 1rem;
            box-shadow: 0 2px 12px rgba(76,175,80,0.08);
            margin-bottom: 1.2rem;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            background: #f9fbe7;
            transition: box-shadow 0.2s, background 0.2s;
        }
        .reply-card:hover {
            background: #e8f5e9;
            box-shadow: 0 4px 24px rgba(76,175,80,0.12);
        }
        .reply-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #43a047;
            border: 2px solid #43a047;
            margin-top: 1rem;
        }
        .reply-content {
            flex: 1;
            padding: 1rem 0;
        }
        .reply-meta {
            font-size: 0.95rem;
            color: #888;
        }
        .reply-delete {
            margin-left: 1rem;
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
        .reply-delete:hover {
            background: #e53935;
            color: #fff;
        }
        .reply-count {
            font-size: 1.1rem;
            color: #388e3c;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .post-reply-card {
            border-radius: 1rem;
            box-shadow: 0 2px 12px rgba(76,175,80,0.08);
            margin-bottom: 2rem;
            background: #fff;
            overflow: hidden;
        }
        .post-reply-card .card-header {
            background: #43a047;
            color: #fff;
            border-radius: 1rem 1rem 0 0;
            padding: 1rem 1.5rem;
            font-size: 1.1rem;
            font-weight: 600;
        }
        .post-reply-card .btn-success {
            border-radius: 2rem;
            font-weight: 600;
            padding: 0.5rem 2rem;
            margin-top: 0.5rem;
            font-size: 1.1rem;
            width: 200px;
            max-width: 100%;
        }
        .post-reply-card .card-body {
            padding: 1.5rem;
        }
        @media (max-width: 700px) {
            .thread-header {
                flex-direction: column;
                align-items: flex-start;
                padding: 1rem 1rem 0.5rem 1rem;
            }
            .thread-title {
                font-size: 1.3rem;
            }
            .thread-avatar {
                width: 48px;
                height: 48px;
                font-size: 1.3rem;
            }
            .thread-body {
                padding: 1rem;
                font-size: 1rem;
            }
            .reply-card {
                flex-direction: column;
                gap: 0.5rem;
                padding: 0.5rem 0.5rem 0.5rem 0.5rem;
            }
            .reply-avatar {
                width: 36px;
                height: 36px;
                font-size: 1rem;
                margin-top: 0;
            }
            .reply-content {
                padding: 0.5rem 0;
            }
            .post-reply-card .card-body {
                padding: 1rem;
            }
            .post-reply-card .btn-success {
                width: 100%;
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
<?php include '../navbar.php'; ?>
    <main class="container my-4">
        <div class="thread-card mb-4">
            <div class="thread-header">
                <div class="thread-avatar"><i class="bi bi-person-circle"></i></div>
                <div>
                    <div class="thread-title"><?= htmlspecialchars($title) ?></div>
                    <div class="thread-meta">Posted by <?= htmlspecialchars($username) ?> on <?= $created_at ?></div>
                </div>
            </div>
            <div class="thread-body">
                <?= nl2br(htmlspecialchars($body)) ?>
            </div>
        </div>

        <div class="reply-count">
            <?php $reply_total = isset($replies) ? $replies->num_rows : 0; ?>
            <?= $reply_total ?> <?= $reply_total === 1 ? 'Reply' : 'Replies' ?>
        </div>

        <?php if ($reply_total > 0): ?>
            <?php while ($reply = $replies->fetch_assoc()): ?>
                <div class="reply-card-container">
                  <div class="reply-card">
                    <div class="reply-avatar"><i class="bi bi-person-circle"></i></div>
                    <div class="reply-content">
                      <div class="reply-meta">Replied by <?= htmlspecialchars($reply['username']) ?> on <?= date('M d, Y', strtotime($reply['created_at'])) ?></div>
                      <div class="reply-text">
                        <?= htmlspecialchars($reply['body']) ?>
                      </div>
                    </div>
                    <?php if ($_SESSION['role'] === 'agriculturist'): ?>
                        <a href="delete.php?type=reply&id=<?= $reply['reply_id'] ?>" 
                        class="reply-delete"
                        onclick="return confirm('Are you sure you want to delete this reply?');">
                            <i class="bi bi-x"></i>
                        </a>
                    <?php endif; ?>
                  </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="text-muted mb-4">No replies yet. Be the first to reply!</div>
        <?php endif; ?>

        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="post-reply-card">
                <div class="card-header">
                    <h6>Post a Reply</h6>
                </div>
                <div class="card-body">
                    <form method="POST" id="replyForm">
                        <div class="mb-3">
                            <textarea name="reply" class="form-control" rows="4" placeholder="Write your reply..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">Submit Reply</button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <p class="text-danger">You must <a href="../login.php">log in</a> to post a reply.</p>
        <?php endif; ?>
    </main>

    <footer class="bg-success text-center text-white py-3 mt-auto">
      <p class="mb-0">&copy; 2024 Farming Community. All rights reserved.</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Smooth scroll to reply form on submit
    document.getElementById('replyForm')?.addEventListener('submit', function(e) {
        setTimeout(function() {
            document.getElementById('replyForm').scrollIntoView({ behavior: 'smooth' });
        }, 100);
    });
    </script>
</body>
</html>
