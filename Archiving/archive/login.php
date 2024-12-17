<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Archive Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
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
            background-color: rgba(185, 229, 232, 0.6); 
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); 
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
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
                    <div class="col-md-6 d-flex flex-column justify-content-center align-items-left custom-bg">
                        <h1 class="welcome" style="font-size: 3rem; font-weight: 800;">
                            Welcome to the <br>Student Archive
                        </h1>
                        <p class="welcome" style="font-size: 1.2rem; line-height: 1.6;">
                            Securely manage your academic documents with ease. <br>
                            Log in to access your student archive now.
                        </p>
                    </div>
                    <!-- Login Card -->
                    <div class="col-md-6">
                        <div class="card p-4">
                            <h4 class="card-title text-center mb-4">Login to Your Account</h4>
                            <form method="POST" action="loginBackend.php">
                                <!-- Username input -->
                                <div class="form-outline mb-4">
                                    <label class="form-label" for="loginUsername">Username</label>
                                    <input type="text" id="loginUsername" name="username" class="form-control" placeholder="Enter your username" required />
                                </div>
                                <!-- Password input -->
                                <div class="form-outline mb-4">
                                    <label class="form-label" for="loginPassword">Password</label>
                                    <input type="password" id="loginPassword" name="password" class="form-control" placeholder="Enter your password" required />
                                </div>
                                <!-- Show/Hide password checkbox -->
                                <div class="form-check mb-4">
                                    <input class="form-check-input" type="checkbox" id="showPassword" />
                                    <label class="form-check-label" for="showPassword">Show Password</label>
                                </div>
                                <!-- Submit button -->
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary w-100 py-2" style="max-width: 400px; margin: 0 auto; font-size: 1.2rem; border-radius: 5px;">
                                        Log In
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
    </div>
    <!-- Show/Hide Password Script -->
    <script>
        const showPasswordCheckbox = document.getElementById('showPassword');
        const passwordInput = document.getElementById('loginPassword');

        showPasswordCheckbox.addEventListener('change', function() {
            passwordInput.type = showPasswordCheckbox.checked ? 'text' : 'password';
        });
    </script>
</body>
</html>
