<?php
session_start();
require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$username = trim($_POST["username"] ?? "");
$password = $_POST["password"] ?? "";

if ($username === "" || $password === "") {
    $_SESSION["message"] = "Please enter both username and password.";
    $_SESSION["message_type"] = "warning";
    header("Location: index.php");
    exit;
}

$sql = "SELECT id, username, password FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    $_SESSION["message"] = "Something went wrong while processing your login.";
    $_SESSION["message_type"] = "error";
    header("Location: index.php");
    exit;
}


                  $stmt->bind_param("s", $username);
                       $stmt->execute();
                           $result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    
      if (hash("sha256", $password) === $user["password"]) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["username"] = $user["username"];
        $_SESSION["message"] = "Login successful! Welcome, " . $user["username"] . ".";
        $_SESSION["message_type"] = "success";
    } else {
        $_SESSION["message"] = "Login failed. Invalid username or password.";
        $_SESSION["message_type"] = "error";
    }
} else {
    $_SESSION["message"] = "Login failed. Invalid username or password.";
    $_SESSION["message_type"] = "error";
}

$stmt->close();
$conn->close();
header("Location: index.php");
exit;
?>
