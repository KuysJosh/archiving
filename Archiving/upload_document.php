<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "student_management");

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed.']));
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input
    $student_id = isset($_POST['student_id']) ? $conn->real_escape_string($_POST['student_id']) : null;
    $documentType = isset($_POST['documentType']) ? $conn->real_escape_string($_POST['documentType']) : null;
    $file = $_FILES['file'];

    if ($student_id && $documentType && $file) {
        // Define the upload directory
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true); // Create the directory if it doesn't exist
        }

        // Generate a unique filename to prevent overwriting
        $fileName = time() . "_" . basename($file['name']);
        $filePath = $uploadDir . $fileName;

        // Move the uploaded file to the upload directory
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            // Update the database with the file path
            $sql = "UPDATE students SET $documentType = '$filePath' WHERE student_id = '$student_id'";
            if ($conn->query($sql)) {
                echo json_encode(['success' => true, 'message' => 'Document uploaded and database updated successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update the database.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

// Close the database connection
$conn->close();
?>
