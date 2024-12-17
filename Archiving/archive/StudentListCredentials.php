<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "student_management");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure student_id is passed in the URL
if (isset($_GET['student_id']) && !empty($_GET['student_id'])) {
    $student_id = intval($_GET['student_id']);
} else {
    die("Student ID is required.");
}

// Handle delete request using prepared statements
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM student_documents WHERE document_id = ? AND student_id = ?");
    $stmt->bind_param("ii", $delete_id, $student_id);
    $stmt->execute();
    $stmt->close();
    header("Location: studentListCredentials.php?student_id=$student_id");
    exit();
}

// Handle view request
if (isset($_GET['view_id'])) {
    $view_id = intval($_GET['view_id']);
    $stmt = $conn->prepare("SELECT * FROM student_documents WHERE document_id = ? AND student_id = ?");
    $stmt->bind_param("ii", $view_id, $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $document = $result->fetch_assoc();
    $stmt->close();
    // Logic for viewing document
}

// Handle print request
if (isset($_GET['print_id'])) {
    $print_id = intval($_GET['print_id']);
    $stmt = $conn->prepare("SELECT * FROM student_documents WHERE document_id = ? AND student_id = ?");
    $stmt->bind_param("ii", $print_id, $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $document = $result->fetch_assoc();
    $stmt->close();
    // Logic for printing document
}

// Handle download request
if (isset($_GET['download_id'])) {
    $download_id = intval($_GET['download_id']);
    $stmt = $conn->prepare("SELECT * FROM student_documents WHERE document_id = ? AND student_id = ?");
    $stmt->bind_param("ii", $download_id, $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $document = $result->fetch_assoc();
    $stmt->close();

    if ($document) {
        $file_path = $document['file_path']; 
        if (file_exists($file_path)) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
            readfile($file_path);
            exit();
        } else {
            die("File not found.");
        }
    }
}

// Handle edit request (update status and notes)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_document'])) {
    $document_id = intval($_POST['document_id']);
    $status = intval($_POST['status']);
    $notes = $_POST['notes'];

    $stmt = $conn->prepare("UPDATE student_documents SET status = ?, notes = ? WHERE student_id = ? AND document_id = ?");
    $stmt->bind_param("isii", $status, $notes, $student_id, $document_id);
    $stmt->execute();
    $stmt->close();
    header("Location: studentListCredentials.php?student_id=$student_id");
    exit();
}

// Fetch all documents
$documents_result = $conn->query("SELECT * FROM document_requirements");

// Fetch student details
$stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student_result = $stmt->get_result();
$student = $student_result->fetch_assoc();
$stmt->close();

// Fetch the documents for the specific student
$stmt = $conn->prepare("SELECT * FROM student_documents WHERE student_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student_documents_result = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Document Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="nav.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-light">
<header>
    <div class="logo-container">
        <img src="uploads/logo.png" alt="Logo" class="logo">
        <span class="title">Student Document Archives</span>
    </div>
    <div class="navbar-buttons">
        <button class="navbar-button btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">Add Document</button>
        <form action="logout.php" method="POST" style="display: inline;">
    <button type="submit" class="navbar-button" id="logoutButton">Logout</button>
</form>    </div>
</header>

<div class="d-flex justify-content-end" style="margin: 10px 10px;">
    <a href="studentList.php" class="btn btn-primary">
        <i class="fas fa-arrow-left"></i> Back to List
    </a>
</div>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-center w-100">Student Credentials</h1>
    </div>

    <!-- Student Info Section -->
    <div class="mb-4">
        <div class="p-3 mb-3 border rounded bg-white">
            <h5 class="mb-1 fs-6">Full Name:</h5>
            <p style="margin-left:30px;" class="mb-0 text-primary fw-bold fs-3">
                <?= htmlspecialchars($student['firstname'] . ' ' . $student['middlename'] . ' ' . $student['lastname']); ?>
            </p>
        </div>
        <div class="p-3 border rounded bg-white">
            <h5 class="mb-1 fs-6">Student ID:</h5>
            <p style="margin-left:30px;" class="mb-0 text-primary fw-bold fs-3">
                <?= htmlspecialchars($student['student_id']); ?>
            </p>
        </div>
    </div>

    <!-- Documents Table -->
    <div class="row">
        <table class="table table-bordered table-striped table-hover table-light">
            <thead class="thead-dark">
                <tr>
                    <th class="col-2">Document</th>
                    <th class="col-2">Status</th>
                    <th class="col-6">Notes</th>
                    <th class="col-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($document = $documents_result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($document['document_name']); ?></td>

                    <?php
                    $status = "Not Uploaded";
                    $notes = "No notes provided";
                    $color = "red"; // Default color for "Not Uploaded" or "No record"

                    $student_documents_result->data_seek(0); // Reset the result pointer
                    while ($student_document = $student_documents_result->fetch_assoc()) {
                        if ($student_document['document_id'] == $document['id']) {
                            $status = $student_document['status'] == 1 ? "Submitted" : "No record";
                            $color = $student_document['status'] == 1 ? "green" : "red";
                            $notes = htmlspecialchars($student_document['notes']);
                            break;
                        }
                    }
                    ?>

                    <td style="color: <?php echo $color; ?>;"><?php echo $status; ?></td>
                    <td><?= $notes; ?></td>

                    <td>
                    <button 
    class="btn btn-primary btn-sm" 
    data-bs-toggle="modal" 
    data-bs-target="#viewModal"
    data-file="<?= htmlspecialchars($student_document['file_path'] ?? '#'); ?>"
    title="View">
    <i class="fas fa-eye"></i>
</button>



<!-- Print button -->
<a href="#" class="btn btn-success btn-sm" 
   title="Print" 
   onclick="printDocument('http://localhost/archiving/archive/<?= htmlspecialchars($student_document['file_path'] ?? '#'); ?>')">
    <i class="fas fa-print"></i>
</a>





                        <!-- Delete button -->
                        <a href="?delete_id=<?= htmlspecialchars($document['id']) ?>&student_id=<?= htmlspecialchars($student['student_id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');" title="Delete">
                            <i class="fas fa-trash"></i>
                        </a>

                        <!-- Download button -->
                        <a href="?download_id=<?= htmlspecialchars($document['id']) ?>&student_id=<?= htmlspecialchars($student['student_id']) ?>" class="btn btn-info btn-sm" title="Download">
                            <i class="fas fa-download"></i>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">Document Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- PDF Viewer -->
                <iframe id="pdfPreview" src="" width="100%" height="500px" hidden></iframe>
                <!-- Fallback Message -->
                <div id="noPdfMessage" class="text-center" hidden>
                    <p>No PDF file available to display.</p>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Upload/Scan Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">Upload or Scan Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="upload.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="student_id" value="<?= $student_id; ?>">

                    <!-- File Upload Section -->
                    <div class="mb-3">
                        <label for="fileInput" class="form-label">Upload File (PDF only)</label>
                        <input type="file" class="form-control" id="fileInput" name="file" accept="application/pdf" required>
                    </div>

                    <!-- Document Type Dropdown -->
                    <div class="mb-3">
                        <label for="documentType" class="form-label">Select Document Type</label>
                        <select class="form-select" id="documentType" name="document_type" required>
                            <?php
                            // Reset the pointer and fetch options again
                            $documents_result->data_seek(0);
                            while ($document = $documents_result->fetch_assoc()): ?>
                                <option value="<?= $document['id']; ?>">
                                    <?= htmlspecialchars($document['document_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- Notes Section -->
                    <div class="mb-3">
                        <label for="note" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="note" name="note" rows="3"></textarea>
                    </div>

                    <!-- PDF Preview -->
                    <div class="mb-3">
                        <iframe id="preview" class="w-100" style="height: 400px; border: 1px solid #ccc;" src="" hidden></iframe>
                    </div>

                    <!-- Submit Button -->
                    <div class="text-center">
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    // File input change listener for PDF preview
    document.getElementById('fileInput').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const previewFrame = document.getElementById('preview');

        // Validate that the file is a PDF
        if (file && file.type === "application/pdf") {
            const fileURL = URL.createObjectURL(file);
            previewFrame.src = fileURL;
            previewFrame.hidden = false;
        } else {
            previewFrame.src = "";
            previewFrame.hidden = true;
            alert('Please upload a valid PDF file.');
        }
    });

    const viewModal = document.getElementById('viewModal');
viewModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget; 
    const filePath = button.getAttribute('data-file'); 

    console.log("File Path: ", filePath); // Debugging output

    const iframe = document.getElementById('pdfPreview');
    const noPdfMessage = document.getElementById('noPdfMessage');

    if (filePath && filePath !== '#') {
        iframe.src = filePath; // Assign path to iframe
        iframe.hidden = false; 
        noPdfMessage.hidden = true;
    } else {
        iframe.src = ""; 
        iframe.hidden = true; 
        noPdfMessage.hidden = false;
    }
});

    // Clear iframe source when modal is closed to avoid unnecessary loading
    viewModal.addEventListener('hidden.bs.modal', function () {
        document.getElementById('pdfPreview').src = "";
    });

    function printDocument(filePath) {
    // Check if filePath is valid
    if (!filePath || filePath === '#') {
        alert('Invalid file path');
        return;
    }

    // Check if the file path is correct and accessible
    const baseURL = window.location.origin; // Get the base URL of the current site
    const fullPath = filePath.startsWith('uploads/') ? baseURL + '/' + filePath : filePath;

    // Open the document's file in a new window
    const printWindow = window.open(fullPath, '_blank');
    
    // When the new window content is fully loaded, trigger the print dialog
    printWindow.onload = function () {
        printWindow.print(); // Automatically trigger print dialog
        printWindow.onafterprint = function () {
            printWindow.close(); // Close the print window after printing
        };
    };
}




</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
