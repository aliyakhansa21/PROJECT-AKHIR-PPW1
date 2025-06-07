<?php
$conn = new mysqli("localhost", "root", "", "studyhub");
$keyword = $_GET['keyword'];

$sql = "SELECT * FROM kursi WHERE NOMOR_KURSI LIKE '%$keyword%'";
$result = $conn->query($sql);

$data = [];
while($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
