<?php
// Enable error reporting for debugging purposes
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$conn = new mysqli("localhost", "root", "", "student_management");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get student ID from the form
    $student_id = $_POST['student_id'];

    // Check if the student ID exists in the enrolled_students table
    $check_query = "SELECT firstname, middlename, lastname FROM enrolled_students WHERE student_id = '$student_id'";
    $result = $conn->query($check_query);

    if ($result->num_rows > 0) {
        // Fetch student details
        $student = $result->fetch_assoc();
        $firstname = $student['firstname'];
        $middlename = $student['middlename'];
        $lastname = $student['lastname'];

        // Default values for the file fields
        $diploma = "No Record";
        $good_moral = "No Record";
        $psa = "No Record";
        $enrollment_form = "No Record";
        $form138 = "No Record";
        $form137 = "No Record";
        $tor = "No Record";

        // Handle file uploads
        $uploads_dir = 'uploads/';
        if (isset($_FILES['diploma']) && $_FILES['diploma']['error'] == 0) {
            $diploma = $_FILES['diploma']['name'];
            move_uploaded_file($_FILES['diploma']['tmp_name'], $uploads_dir . $diploma);
        }
        if (isset($_FILES['good_moral']) && $_FILES['good_moral']['error'] == 0) {
            $good_moral = $_FILES['good_moral']['name'];
            move_uploaded_file($_FILES['good_moral']['tmp_name'], $uploads_dir . $good_moral);
        }
        // Repeat for other file fields...

        // Insert student data into the students table
        $insert_query = "INSERT INTO students (student_id, firstname, middlename, lastname, diploma, good_moral, psa, enrollment_form, form138, form137, tor) 
                         VALUES ('$student_id', '$firstname', '$middlename', '$lastname', '$diploma', '$good_moral', '$psa', '$enrollment_form', '$form138', '$form137', '$tor')";

        if ($conn->query($insert_query) === TRUE) {
            echo "<script>alert('Student added successfully');</script>";
        } else {
            echo "<script>alert('Failed to add student: " . $conn->error . "');</script>";
        }
    } else {
        // Student ID not found
        echo "<script>alert('No Student ID found in enrolled_students');</script>";
    }
}

// Handle search functionality
$search_query = "";
if (isset($_GET['search'])) {
    $search_term = $_GET['search'];
    $search_query = " WHERE student_id LIKE '%$search_term%' OR firstname LIKE '%$search_term%' OR lastname LIKE '%$search_term%'";
}

// Fetch all students in ascending order by student_id or based on search
$result = $conn->query("SELECT * FROM students" . $search_query . " ORDER BY student_id ASC");




// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="nav.css">
</head>
<body class="bg-light">
<header>
    <div class="logo-container">
        <img src="uploads/logo.png" alt="Logo" class="logo">
        <span class="title">Student Document Archives</span>
    </div>
    <div class="navbar-buttons">
        <button class="navbar-button" id="addStudentButton" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">Add Student</button>
        <button class="navbar-button" id="logoutButton">Logout</button>
    </div>
</header>

<div class="container mt-5">
    <h1 class="text-center mb-4">Student's Documents Archiving System</h1>

    <!-- Search Bar -->
    <form method="GET" class="mb-4">
        <input type="text" name="search" class="form-control" placeholder="Search by ID, First Name, or Last Name" value="<?php echo isset($search_term) ? $search_term : ''; ?>">
    </form>

    <!-- Add Student Modal -->
    <div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addStudentModalLabel">Add Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="student_id" class="form-label">Student ID:</label>
                            <input type="text" id="student_id" name="student_id" class="form-control" required>
                        </div>
                        <!-- Repeat for other file fields like PSA, Enrollment Form, etc. -->
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Save</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Student Table -->
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Diploma</th>
                    <th>Good Moral</th>
                    <th>PSA</th>
                    <th>Enrollment Form</th>
                    <th>Form138</th>
                    <th>Form137</th>
                    <th>TOR</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['student_id']; ?></td>
                            <td><?php echo $row['firstname'] . ' ' . $row['middlename'] . ' ' . $row['lastname']; ?></td>
                            <td style="color: <?php echo $row['diploma'] != 'No Record' ? 'green' : 'red'; ?>">
                                <?php echo $row['diploma'] != 'No Record' ? 'Passed' : 'No Record'; ?>
                            </td>
                            <td style="color: <?php echo $row['good_moral'] != 'No Record' ? 'green' : 'red'; ?>">
                                <?php echo $row['good_moral'] != 'No Record' ? 'Passed' : 'No Record'; ?>
                            </td>
                            <td style="color: <?php echo $row['psa'] != 'No Record' ? 'green' : 'red'; ?>">
                                <?php echo $row['psa'] != 'No Record' ? 'Passed' : 'No Record'; ?>
                            </td>
                            <td style="color: <?php echo $row['enrollment_form'] != 'No Record' ? 'green' : 'red'; ?>">
                                <?php echo $row['enrollment_form'] != 'No Record' ? 'Passed' : 'No Record'; ?>
                            </td>
                            <td style="color: <?php echo $row['form138'] != 'No Record' ? 'green' : 'red'; ?>">
                                <?php echo $row['form138'] != 'No Record' ? 'Passed' : 'No Record'; ?>
                            </td>
                            <td style="color: <?php echo $row['form137'] != 'No Record' ? 'green' : 'red'; ?>">
                                <?php echo $row['form137'] != 'No Record' ? 'Passed' : 'No Record'; ?>
                            </td>
                            <td style="color: <?php echo $row['tor'] != 'No Record' ? 'green' : 'red'; ?>">
                                <?php echo $row['tor'] != 'No Record' ? 'Passed' : 'No Record'; ?>
                            </td>
                            <td>
                                <a href="html2.php?student_id=<?php echo $row['student_id']; ?>" class="btn btn-info">View</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" class="text-center">No records found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>



<!-- Bootstrap JS (Modal functionality) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js"></script>
<script>   document.getElementById('logoutButton').addEventListener('click', function() {
        // Redirect to logout.php to handle session destruction
        window.location.href = 'logout.php';
    });</script>
</body>
</html>
