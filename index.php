<?php
session_start();

$host = 'localhost';
$dbname = 'feedback_system'; 
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database Connection Error: " . $e->getMessage());
}

$message = "";
$message_type = "";
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $message_type = $_SESSION['message_type'];
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}

try {
    $stmt = $pdo->query("SELECT * FROM feedback ORDER BY created_at DESC");
    $feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $feedbacks = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini Feedback System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        
        <div class="header">
            <a href="../index.php" class="back-portfolio">&larr; Back to Portfolio</a>
            <h1>Mini Feedback System</h1>
           
        </div>

        <?php if ($message != ""): ?>
            <div class="message <?= htmlspecialchars($message_type) ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <h2>🔒 Optional Login</h2>
            <?php if (isset($_SESSION['user_id'])): ?>
                <p>Welcome, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></p>
                <a href="logout.php" class="logout">Logout</a>
            <?php else: ?>
                <p class="optional">
                    Login is optional. You can still submit feedback without logging in.
                </p>
                <form action="login.php" method="POST">
                    <label>Username</label>
                    <input type="text" name="username" placeholder="Enter username">
                    
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Enter password">
                    
                    <button type="submit">Login</button>
                </form>
            <?php endif; ?>
        </div>

        <div class="card">
            <h2>📝 Send Your Feedback</h2>
            <form action="submit_feedback.php" method="POST">
                <label>Name</label>
                <!-- Fallback to current username if logged in, otherwise let them type or leave anonymous -->
                <input type="text" name="name" placeholder="Enter your name" value="<?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : '' ?>" required>
                
                <label>Feedback</label>
                <!-- Name attribute matches form requirements -->
                <textarea name="feedback" rows="5" placeholder="Write your feedback..." required></textarea>
                
                <button type="submit">Submit Feedback</button>
            </form>
        </div>

        <div class="card">
            <h2>📊 Past Submissions</h2>
            <?php if (empty($feedbacks)): ?>
                <p class="optional">No feedback submitted yet. Be the first!</p>
            <?php else: ?>
                <div class="feedback-list">
                    <?php foreach ($feedbacks as $item): ?>
                        <div class="feedback-item" style="border-bottom: 1px solid #eee; padding: 10px 0;">
                            <!-- Handles anonymous users cleanly if name/user_id is missing -->
                            <strong><?= !empty($item['name']) ? htmlspecialchars($item['name']) : 'Anonymous Visitor' ?></strong> 
                            
                            <span style="font-size: 0.8em; color: #888; float: right;">
                                <?= htmlspecialchars($item['created_at']) ?>
                            </span>
                            
                            <!-- FIXED: Changed column key from 'feedback' to 'message' to match your database structure -->
                            <p style="margin: 5px 0 0 0; color: #444;"><?= nl2br(htmlspecialchars($item['message'])) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="footer">
            Mini Web Application Activity<br>
            Error Handling • Feedback Messages • Optional Login
        </div>
        
    </div>
</body>
</html>
