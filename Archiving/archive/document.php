<?php
// Database connection settings
$servername = "localhost";
$username = "root"; // Default username for XAMPP
$password = ""; // Default password for XAMPP
$dbname = "student_management"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to get document names, descriptions, and ids
$sql = "SELECT document_name, description, id FROM document_requirements";
$result = $conn->query($sql);

// Check if the query returns any results
if ($result->num_rows > 0) {
    $documents = $result->fetch_all(MYSQLI_ASSOC); // Fetch all documents in an array
} else {
    $documents = []; // No documents found
}

// Edit functionality
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_id'])) {
    $id = $_POST['edit_id'];
    $document_name = $_POST['document_name'];
    $description = $_POST['description'];

    $update_sql = "UPDATE document_requirements SET document_name='$document_name', description='$description' WHERE id=$id";
    if ($conn->query($update_sql) === TRUE) {
        echo "<script>alert('Document updated successfully.');</script>";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "<script>alert('Error updating record: " . $conn->error . "');</script>";
    }
}

// Close the connection
$conn->close();
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
    <style>
        .table-container {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .table th, .table td {
            vertical-align: middle;
        }

        .table thead {
            background-color: #007bff;
            color: #fff;
        }

        .table tbody tr:hover {
            background-color: #f1f1f1;
        }

        .btn-custom {
            font-size: 16px;
        }

        .modal-body {
            padding: 30px;
        }

        .modal-header {
            background-color: #007bff;
            color: white;
        }

        .navbar-buttons .btn {
            margin-left: 10px;
        }

        .navbar-buttons {
            display: flex;
            align-items: center;
        }

        .logo-container img {
            max-width: 60px;
            margin-right: 15px;
        }

        .logo-container {
            display: flex;
            align-items: center;
        }

        .title {
            font-size: 24px;
            font-weight: bold;
        }
    </style>
</head>

<body class="bg-light">
    <header>
        <div class="logo-container">
            <img src="uploads/logo.png" alt="Logo" class="logo">
            <span class="title">Student Document Archives</span>
        </div>

        
        <div class="navbar-buttons">
        <button class="navbar-button" id="addDocumentButton" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#addDocumentModal">Add Document</button>
        <form action="logout.php" method="POST" style="display: inline;">
    <button type="submit" class="navbar-button" id="logoutButton">Logout</button>
</form>        </div>
    </header>
    <div class="d-flex justify-content-end" style="margin: 10px 10px;">
    <a href="studentList.php" class="btn btn-primary">
        <i class="fas fa-arrow-left"></i> Back to List
    </a>
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

    <section>
        <div class="container mt-4 table-container">
            <h3 class="text-center">Document List</h3>
            <table class="table table-hover table-bordered table-striped">
                <thead>
                    <tr>
                        <th scope="col" class=" text-center col-1">#</th>
                        <th scope="col" class=" text-center">Document Name</th>
                        <th scope="col" class=" text-center">Description</th>
                        <th scope="col" class=" text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Display each document in the table
                    if (count($documents) > 0) {
                        $counter = 1;
                        foreach ($documents as $document) {
                            echo "<tr>";
                            echo "<td class='text-center'>" . $counter . "</td>";
                            echo "<td class='col-3'>" . $document['document_name'] . "</td>";
                            echo "<td>" . $document['description'] . "</td>";
                            echo "<td class='col-1'>
                                    <button class='btn btn-warning btn-sm btn-custom' data-bs-toggle='modal' data-bs-target='#editModal' data-id='" . $document['id'] . "' data-name='" . $document['document_name'] . "' data-description='" . $document['description'] . "'>
                                        <i class='fas fa-edit'></i>
                                    </button>
                                    <a href='?delete_id=" . $document['id'] . "' class='btn btn-danger btn-sm btn-custom' onclick='return confirm(\"Are you sure you want to delete this document?\")'>
                                        <i class='fas fa-trash'></i>
                                    </a>
                                </td>";
                            echo "</tr>";
                            $counter++;
                        }
                    } else {
                        echo "<tr><td colspan='4' class='text-center'>No documents found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Modal for Editing Document -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" id="edit_id" name="edit_id">
                        <div class="mb-3">
                            <label for="document_name" class="form-label">Document Name</label>
                            <input type="text" class="form-control" id="document_name" name="document_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Populate the modal with the document data
        var editModal = document.getElementById('editModal');
        editModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget; // Button that triggered the modal
            var id = button.getAttribute('data-id');
            var name = button.getAttribute('data-name');
            var description = button.getAttribute('data-description');

            var modalId = editModal.querySelector('#edit_id');
            var modalName = editModal.querySelector('#document_name');
            var modalDescription = editModal.querySelector('#description');

            modalId.value = id;
            modalName.value = name;
            modalDescription.value = description;
        });
    </script>

</body>

</html>

<?php
// Delete functionality
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];

    // Reconnect to the database for deletion
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $delete_sql = "DELETE FROM document_requirements WHERE id = $id";
    if ($conn->query($delete_sql) === TRUE) {
        echo "<script>alert('Document deleted successfully.');</script>";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "<script>alert('Error deleting document: " . $conn->error . "');</script>";
    }

    $conn->close();
}
?>
