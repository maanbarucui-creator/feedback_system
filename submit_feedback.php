<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$name = trim($_POST['name'] ?? '');
$feedback = trim($_POST['feedback'] ?? '');

$user_id = $_SESSION['user_id'] ?? null;

if ($name === '') {
    $_SESSION['message'] = 'Please enter your name.';
    $_SESSION['message_type'] = 'warning';
    header('Location: index.php');
    exit;
}

if ($feedback === '') {
    $_SESSION['message'] = 'Please enter your feedback.';
    $_SESSION['message_type'] = 'warning';
    header('Location: index.php');
    exit;
}

if (strlen($feedback) < 10) {
    $_SESSION['message'] = 'Feedback must contain at least 10 characters.';
    $_SESSION['message_type'] = 'warning';
    header('Location: index.php');
    exit;
}

$sql = "INSERT INTO feedback (user_id, name, message) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    $_SESSION['message'] = 'Unable to process your feedback. Please try again.';
    $_SESSION['message_type'] = 'error';
    header('Location: index.php');
    exit;
}

$stmt->bind_param('iss', $user_id, $name, $feedback);

if ($stmt->execute()) {
    $_SESSION['message'] = "Thank you, {$name}! Your feedback has been submitted successfully.";
    $_SESSION['message_type'] = 'success';
} else {
    $_SESSION['message'] = 'Something went wrong while saving your feedback.';
    $_SESSION['message_type'] = 'error';
}

$stmt->close();
$conn->close();
header('Location: index.php');
exit;
?>
