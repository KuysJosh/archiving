<?php
session_start();
include '../db.php'; // Include database connection

// Check if the database connection is working
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize user input
    $username = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['username']));
    $password = $_POST['password'];

    // SQL query to fetch user based on username
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Error preparing the statement: " . $conn->error);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Debugging: Output user data
        // Uncomment the line below to inspect the user data
        // var_dump($user);

        // Verify the hashed password
        if (password_verify($password, $user['password'])) { 
            session_regenerate_id(true); // Prevent session fixation
            $_SESSION['user'] = $username;

            // Redirect to the student list page
            header("Location: studentList.php");
            exit();
        } else {
            // Invalid password
            error_log("Failed login attempt for username: $username", 0); // Log failed login
            echo "<script>alert('Invalid username or password.'); window.location.href='login.html';</script>";
            exit();
        }
    } else {
        // User not found
        error_log("Failed login attempt for unknown username: $username", 0);
        echo "<script>alert('User not found.'); window.location.href='login.html';</script>";
        exit();
    }
} else {
    // Redirect back to login if accessed directly
    header("Location: login.php");
    exit();
}
?>
