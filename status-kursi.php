<?php
$conn = new mysqli("localhost", "root", "", "studyhub");
$id = $_GET['id'];

$sql = "SELECT * FROM kursi WHERE ID_KURSI = $id";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

echo json_encode($row);
?>
