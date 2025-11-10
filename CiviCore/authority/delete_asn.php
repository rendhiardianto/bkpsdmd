<?php
include "../db.php";

if (!isset($_GET['id'])) {
    die("ID tidak ditemukan.");
}

$id = intval($_GET['id']);
$sql = "DELETE FROM asn_merangin WHERE id = $id";

if ($conn->query($sql)) {
    echo "<script>alert('Data berhasil dihapus!'); window.location='list_asn_merangin.php';</script>";
} else {
    echo "<script>alert('Gagal menghapus data.'); history.back();</script>";
}
