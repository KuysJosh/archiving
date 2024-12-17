<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['document']) && isset($_POST['student_id']) && isset($_POST['document_name'])) {
    $file = $_FILES['document'];
    $studentId = $_POST['student_id'];
    $documentName = $_POST['document_name'];

    // Set file upload path
    $targetDir = "uploads/";
    $targetFile = $targetDir . basename($file["name"]);

    // Move uploaded file to the target directory
    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
        echo "Document uploaded successfully.";
    } else {
        echo "Error uploading document.";
    }
}
?>
