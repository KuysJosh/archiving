<?php
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Send a JSON response to indicate that the logout was successful
header("Location: login.php");
exit();
