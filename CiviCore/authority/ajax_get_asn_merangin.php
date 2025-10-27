<?php
include "../db.php";

header("Content-Type: application/json");

$kategori = $_GET['kategori'] ?? '';
$sub_kategori = $_GET['sub_kategori'] ?? '';
$semester = $_GET['semester'] ?? '';

if (empty($kategori) || empty($sub_kategori) || empty($semester)) {
    echo json_encode(["success" => false, "message" => "Missing parameters"]);
    exit;
}

$sql = "SELECT label, jumlah 
        FROM asn_merangin 
        WHERE kategori = ? AND sub_kategori = ? AND semester = ?
        ORDER BY id ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $kategori, $sub_kategori, $semester);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
