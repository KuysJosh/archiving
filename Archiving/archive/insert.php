<?php
$plainPassword = 'adfcarchiving_12345';
$hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

// Insert into the database
include '../db.php';  // Include your DB connection

$sql = "INSERT INTO users (username, password) VALUES ('Adfc_Archiving1984', ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $hashedPassword);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "User inserted successfully!";
} else {
    echo "Error inserting user: " . $stmt->error;
}
?>
