<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$conn = new mysqli("localhost", "root", "", "student_management");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle file upload and save document
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file'], $_POST['student_id'], $_POST['document_type'], $_POST['note'])) {
        $student_id = intval($_POST['student_id']);
        $document_type = intval($_POST['document_type']);
        $note = $conn->real_escape_string($_POST['note']);
        $file = $_FILES['file'];

        // Upload directory
        $target_dir = "uploads/documents/";

        // Create the uploads directory if it doesn't exist
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // File information
        $file_name = basename($file['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $file_path = $target_dir . uniqid("doc_", true) . "." . $file_ext;

        // Validate file type and size
        if ($file_ext !== "pdf") {
            die("Invalid file type. Only PDF files are allowed.");
        }

        if ($file["size"] > 5 * 1024 * 1024) { // 5MB limit
            die("File is too large. Max file size is 5MB.");
        }

        // Save file to the server
        if (move_uploaded_file($file["tmp_name"], $file_path)) {
            // Check if the document already exists for the student and document type
            $check_query = "SELECT * FROM student_documents WHERE student_id = ? AND document_id = ?";
            $stmt_check = $conn->prepare($check_query);
            $stmt_check->bind_param("ii", $student_id, $document_type);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();

            if ($result_check->num_rows > 0) {
                // Document exists, update the record
                $update_query = "UPDATE student_documents
                                 SET file_path = ?, status = 1, notes = ?, updated_at = NOW()
                                 WHERE student_id = ? AND document_id = ?";
                $stmt_update = $conn->prepare($update_query);
                $stmt_update->bind_param("ssii", $file_path, $note, $student_id, $document_type);

                if ($stmt_update->execute()) {
                    // Redirect back to the previous page
                    header("Location: " . $_SERVER['HTTP_REFERER']);
                    exit();
                } else {
                    die("Error updating document: " . $stmt_update->error);
                }
            } else {
                // Document does not exist, insert a new record
                $insert_query = "INSERT INTO student_documents (student_id, document_id, file_path, status, notes, updated_at)
                                 VALUES (?, ?, ?, 1, ?, NOW())";
                $stmt_insert = $conn->prepare($insert_query);
                $stmt_insert->bind_param("iiss", $student_id, $document_type, $file_path, $note);

                if ($stmt_insert->execute()) {
                    // Redirect back to the previous page
                    header("Location: " . $_SERVER['HTTP_REFERER']);
                    exit();
                } else {
                    die("Error saving document: " . $stmt_insert->error);
                }
            }
        } else {
            die("Failed to upload the file.");
        }
    } else {
        die("Invalid form data.");
    }
}

// Close the database connection
$conn->close();
?>
