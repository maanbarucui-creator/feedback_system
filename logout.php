<?php
session_start();
session_unset();
session_destroy();
session_start();
$_SESSION["message"] = "You have been successfully logged out.";
$_SESSION["message_type"] = "success";
header("Location: index.php");
exit;
?>
