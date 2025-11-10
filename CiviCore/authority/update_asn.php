<?php
include "../db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $nip = $conn->real_escape_string($_POST['nip']);
    $fullname = $conn->real_escape_string($_POST['fullname']);
    $jabatan = $conn->real_escape_string($_POST['jabatan']);
    $organisasi = $conn->real_escape_string($_POST['organisasi']);

    $sql = "UPDATE asn_merangin 
            SET nip='$nip', fullname='$fullname', jabatan='$jabatan', organisasi='$organisasi'
            WHERE id=$id";

    if ($conn->query($sql)) {
        echo "<script>alert('Data berhasil diperbarui!'); window.location='list_asn_merangin.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data.'); history.back();</script>";
    }
}
