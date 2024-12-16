<?php
// Start the session
session_start();

// Destroy the session to log the user out
session_destroy();

// Redirect to the login page (or any page you'd like after logging out)
header("Location: login.php");
exit();
?>
