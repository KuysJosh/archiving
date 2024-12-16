<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "student_management");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch document names from the document_requirements table
$document_query = "SELECT * FROM document_requirements";
$document_result = $conn->query($document_query);

// Handle search functionality
$search_query = "";
if (isset($_GET['search'])) {
    $search_term = $_GET['search'];
    $search_query = " WHERE student_id LIKE '%$search_term%' OR firstname LIKE '%$search_term%' OR lastname LIKE '%$search_term%'";
}

// Fetch all students in ascending order by student_id or based on search
$student_query = "SELECT * FROM students" . $search_query . " ORDER BY student_id ASC";
$student_result = $conn->query($student_query);

// Handle Add Student Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['student_id'])) {
    $student_id = $_POST['student_id'];

    // Check if student_id exists in enrolled_students table
    $check_query = "SELECT * FROM enrolled_students WHERE student_id = '$student_id'";
    $check_result = $conn->query($check_query);

    if ($check_result->num_rows > 0) {
        // Fetch the data from enrolled_students table
        $student_data = $check_result->fetch_assoc();

        // Extract relevant fields from the enrolled_students table
        $firstname = $student_data['firstname'];
        $lastname = $student_data['lastname'];
        $course = $student_data['course'];

        // Check if student_id already exists in the students table
        $duplicate_check = "SELECT * FROM students WHERE student_id = '$student_id'";
        $duplicate_result = $conn->query($duplicate_check);

        if ($duplicate_result->num_rows > 0) {
            // Student ID already exists in students table
            $error_message = "Student ID $student_id is already enrolled.";
        } else {
            // Insert student data into the students table
            $insert_query = "INSERT INTO students (student_id, firstname, lastname, course)
                             VALUES ('$student_id', '$firstname', '$lastname', '$course')";
            if ($conn->query($insert_query) === TRUE) {
                // Redirect to refresh the page and see the new student
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } else {
                $error_message = "Error: " . $conn->error;
            }
        }
    } else {
        $error_message = "Student ID $student_id is not found in the enrolled_students table.";
    }
}

// Handle Add Document Requirement Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['document_name'])) {
    $document_name = $_POST['document_name'];

    // Insert document requirement into the document_requirements table
    $insert_document_query = "INSERT INTO document_requirements (document_name) VALUES ('$document_name')";
    if ($conn->query($insert_document_query) === TRUE) {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $error_message = "Error: " . $conn->error;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
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
        <button class="navbar-button" id="addDocumentButton" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#addDocumentModal">Add Document</button>
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
                <form action="" method="POST">
                    <div class="modal-body">
                        <!-- Display error message if student ID already exists -->
                        <?php if (isset($error_message)): ?>
                            <div class="alert alert-danger"><?php echo $error_message; ?></div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="student_id" class="form-label">Student ID:</label>
                            <input type="text" id="student_id" name="student_id" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Save</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Document Requirement Modal -->
    <div class="modal fade" id="addDocumentModal" tabindex="-1" aria-labelledby="addDocumentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDocumentModalLabel">Add Document Requirement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="POST">
                    <div class="modal-body">
                        <!-- Display error message if any -->
                        <?php if (isset($error_message)): ?>
                            <div class="alert alert-danger"><?php echo $error_message; ?></div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="document_name" class="form-label">Document Name:</label>
                            <input type="text" id="document_name" name="document_name" class="form-control" required>
                        </div>
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

                    <!-- Dynamically display document requirements from the database -->
                    <?php if ($document_result->num_rows > 0): ?>
                        <?php while($document = $document_result->fetch_assoc()): ?>
                            <th><?php echo $document['document_name']; ?></th>
                        <?php endwhile; ?>
                    <?php endif; ?>

                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($student_result->num_rows > 0): ?>
                    <?php while($row = $student_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['student_id']; ?></td>
                            <td><?php echo $row['firstname'] . " " . $row['lastname']; ?></td>

                            <!-- Loop through document_requirements and display the document status for each student -->
                            <?php
// Fetch the document status for the current student
$student_documents_query = "SELECT * FROM student_documents WHERE student_id = " . $row['student_id'];
$student_documents_result = $conn->query($student_documents_query);
$student_documents = [];
while ($doc = $student_documents_result->fetch_assoc()) {
    $student_documents[$doc['document_id']] = $doc['status'];
}

// Reset the pointer to the start of the document result
$document_result->data_seek(0);

while ($document = $document_result->fetch_assoc()) {
    // Display the document status for each document
    $doc_status = isset($student_documents[$document['id']]) ? $student_documents[$document['id']] : 0;

    // Set the color based on the document status
    if ($doc_status == 0) {
        // No record (status 0)
        $color = 'red';
        $status_text = 'No record';
    } elseif ($doc_status == 1) {
        // Submitted (status 1)
        $color = 'green';
        $status_text = 'Submitted';
    } else {
        // For any other status value
        $color = 'gray';
        $status_text = 'Unknown';
    }

    // Display the status with the color
    echo "<td style='color: $color;'>$status_text</td>";
}
?>


<td>
    <!-- Ensure that student_id is correctly passed -->
    <a href="studentListCredentials.php?student_id=<?php echo urlencode($row['student_id']); ?>" class="btn btn-info">View</a>
</td>

                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" class="text-center" style="color: red;">No records found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<!-- Bootstrap JS (Modal functionality) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js"></script>
</body>
</html>
