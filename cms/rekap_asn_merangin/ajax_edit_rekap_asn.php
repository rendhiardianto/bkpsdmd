<?php
include "../db.php";
include "../auth.php";

requireRole(['super_admin', 'admin']);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = intval($_POST['id']);
    $tahun = $conn->real_escape_string($_POST['tahun']);
    $semester = $conn->real_escape_string($_POST['semester']);
    $kategori = $conn->real_escape_string($_POST['kategori']);
    $sub_kategori = $conn->real_escape_string($_POST['sub_kategori']);
    $label = $conn->real_escape_string($_POST['label']);
    $jumlah = $conn->real_escape_string($_POST['jumlah']);

    $updateFields = "tahun='$tahun', semester='$semester', kategori='$kategori', sub_kategori='$sub_kategori', 
    label='$label', jumlah='$jumlah'";

    // === Update database ===
    $sql = "UPDATE rekap_asn_merangin SET $updateFields WHERE id=$id";

    if ($conn->query($sql)) {
        echo "Data berhasil diperbarui!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
