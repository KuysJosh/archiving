<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "student_management");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $conn->query("DELETE FROM students WHERE student_id = '$delete_id'");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle edit request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_student'])) {
    $student_id = $_POST['student_id'];
    $diploma = $_POST['diploma'];
    $good_moral = $_POST['good_moral'];
    $psa = $_POST['psa'];
    $enrollment_form = $_POST['enrollment_form'];
    $form138 = $_POST['form138'];
    $form137 = $_POST['form137'];
    $tor = $_POST['tor'];
    $picture = $_POST['picture'];

    $conn->query("UPDATE students SET 
        diploma = '$diploma', 
        good_moral = '$good_moral', 
        psa = '$psa', 
        enrollment_form = '$enrollment_form', 
        form138 = '$form138', 
        form137 = '$form137', 
        tor = '$tor', 
        picture = '$picture' 
        WHERE student_id = '$student_id'");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch all students
$result = $conn->query("SELECT * FROM students");

// Close the database connection when done
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Documents</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h1 class="text-center mb-4">Student Document Management</h1>
        <div class="row">
            <?php while ($student = $result->fetch_assoc()): ?>
            <div class="col-md-6">
                <div class="card mb-4 shadow-sm border-primary">
                    <div class="card-body">
                        <h5 class="card-title">Student ID: <?php echo $student['student_id']; ?></h5>
                        <p><strong>Diploma:</strong> <?php echo $student['diploma']; ?></p>
                        <p><strong>Good Moral:</strong> <?php echo $student['good_moral']; ?></p>
                        <p><strong>PSA:</strong> <?php echo $student['psa']; ?></p>
                        <p><strong>Enrollment Form:</strong> <?php echo $student['enrollment_form']; ?></p>
                        <p><strong>Form 138:</strong> <?php echo $student['form138']; ?></p>
                        <p><strong>Form 137:</strong> <?php echo $student['form137']; ?></p>
                        <p><strong>TOR:</strong> <?php echo $student['tor']; ?></p>
                        <p><strong>Picture:</strong> <?php echo $student['picture']; ?></p>
                        <div class="d-flex justify-content-between">
                            <a href="?edit_id=<?php echo $student['student_id']; ?>" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $student['student_id']; ?>">Edit</a>
                            <a href="?delete_id=<?php echo $student['student_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');">Delete</a>
                            <a href="?view_id=<?php echo $student['student_id']; ?>" class="btn btn-primary btn-sm">View</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Modal -->
            <div class="modal fade" id="editModal<?php echo $student['student_id']; ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Student Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form method="post">
                                <input type="hidden" name="student_id" value="<?php echo $student['student_id']; ?>">
                                <div class="mb-3">
                                    <label for="diploma" class="form-label">Diploma</label>
                                    <input type="text" name="diploma" value="<?php echo $student['diploma']; ?>" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="good_moral" class="form-label">Good Moral</label>
                                    <input type="text" name="good_moral" value="<?php echo $student['good_moral']; ?>" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="psa" class="form-label">PSA</label>
                                    <input type="text" name="psa" value="<?php echo $student['psa']; ?>" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="enrollment_form" class="form-label">Enrollment Form</label>
                                    <input type="text" name="enrollment_form" value="<?php echo $student['enrollment_form']; ?>" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="form138" class="form-label">Form 138</label>
                                    <input type="text" name="form138" value="<?php echo $student['form138']; ?>" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="form137" class="form-label">Form 137</label>
                                    <input type="text" name="form137" value="<?php echo $student['form137']; ?>" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="tor" class="form-label">TOR</label>
                                    <input type="text" name="tor" value="<?php echo $student['tor']; ?>" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="picture" class="form-label">Picture</label>
                                    <input type="text" name="picture" value="<?php echo $student['picture']; ?>" class="form-control">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" name="edit_student" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
