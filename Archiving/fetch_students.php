<?php
include('db_connection.php');

$sql = "SELECT * FROM students";
$result = $conn->query($sql);

$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

echo json_encode($students);
?>
