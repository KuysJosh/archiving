<?php
session_start();
include 'db.php'; // Include the database connection file

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['loginUsername']; // User username
    $password = $_POST['loginPassword']; // User password
    
    // SQL query to fetch user based on the provided username
    $sql = "SELECT * FROM users WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password); // Bind parameters
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a row is returned
    if ($result->num_rows > 0) {
        // User is valid, start a session
        $_SESSION['user'] = $username;
        header("Location: html1.php"); // Redirect to the dashboard (html2.php)
        exit();
    } else {
        // Invalid credentials
        $error_message = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <title>Student Archive Login</title>
    <style>
        /* Main Theme Colors */
        body {
            font-family: Arial, sans-serif;
            color: #4A628A;
            background-image: url(uploads/adfc_background.png);
            background-position: center;
            background-size: cover;
        }

        .card {
            background-color: #B9E5E8;
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .card-title {
            font-weight: bold;
            color: #4A628A;
        }

        .form-label {
            color: #4A628A;
        }

        .btn-primary {
            background-color: #7AB2D3;
            border: none;
            border-radius: 20px;
            padding: 10px 20px;
            font-weight: bold;
            color: #fff;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #4A628A;
        }

        .social-buttons button {
            color: #4A628A;
            border: 1px solid #4A628A;
        }

        .social-buttons button:hover {
            background-color: #7AB2D3;
            color: #fff;
        }

        .custom-bg h1 {
            font-weight: 800;
            color: #4A628A;
            font-size: 2.5rem;
        }

        .custom-bg p {
            font-size: 1rem;
            color: #4A628A;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center align-items-center vh-100">
            <div class="col-lg-10 col-md-12">
                <div class="row">
                    <!-- Welcome Section -->
                    <div class="col-md-6 d-flex flex-column justify-content-center align-items-center custom-bg">
                        <h1>Welcome to the Student Archive</h1>
                        <p>
                            Securely manage your academic documents with ease. 
                            Log in to access your student archive now.
                        </p>
                    </div>
                    <!-- Login Card -->
                    <div class="col-md-6">
                        <div class="card p-4">
                            <h4 class="card-title text-center mb-4">Login to Your Account</h4>
                            <?php if (isset($error_message)) { ?>
                                <div class="alert alert-danger text-center"><?= $error_message ?></div>
                            <?php } ?>
                            <form method="POST" action="">
                                <!-- Username input -->
                                <div class="form-outline mb-4">
                                    <label class="form-label" for="loginUsername">Username</label>
                                    <input type="text" id="loginUsername" name="loginUsername" class="form-control" placeholder="Enter your username" required />
                                </div>
                            
                                <!-- Password input -->
                                <div class="form-outline mb-4">
                                    <label class="form-label" for="loginPassword">Password</label>
                                    <input type="password" id="loginPassword" name="loginPassword" class="form-control" placeholder="Enter your password" required />
                                </div>
                            

                                <!-- Show/Hide password checkbox -->
                                <div class="form-check mb-4">
                                    <input class="form-check-input" type="checkbox" id="showPassword" />
                                    <label class="form-check-label" for="showPassword">Show Password</label>
                                </div>
                                

                                <!-- Submit button -->
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary btn-block">Log In</button>
                                </div>
                            

                                <!-- Social login buttons -->
                                <div class="text-center mt-4">
                                    <p>Login as admin!</p>
                                    <div class="social-buttons">
                                        <button type="button" class="btn btn-outline-secondary btn-floating mx-1">
                                            <i class="fab fa-facebook-f"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-floating mx-1">
                                            <i class="fab fa-google"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-floating mx-1">
                                            <i class="fab fa-twitter"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-floating mx-1">
                                            <i class="fab fa-github"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Show/Hide password functionality
        const showPasswordCheckbox = document.getElementById('showPassword');
        const passwordInput = document.getElementById('loginPassword');

        showPasswordCheckbox.addEventListener('change', function() {
            // Toggle password visibility
            if (showPasswordCheckbox.checked) {
                passwordInput.type = 'text'; // Show password
            } else {
                passwordInput.type = 'password'; // Hide password
            }
        });
    </script>
</body>
</html>
