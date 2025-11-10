<?php
include "../db.php";

$q = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';

if ($q === '') {
    echo "<tr><td colspan='9' style='text-align:center;'>Masukkan kata kunci untuk mencari data</td></tr>";
    exit;
}

// Search by name, NIP, or jabatan
$sql = "
    SELECT id, nip, fullname, status_pegawai, jabatan, organisasi, organisasi_induk,  tempat_lahir, tanggal_lahir, phone
    FROM asn_merangin
    WHERE fullname LIKE '%$q%' OR nip LIKE '%$q%' OR jabatan LIKE '%$q%'
    ORDER BY fullname ASC
    LIMIT 200
";

$result = $conn->query($sql);

if ($result->num_rows === 0) {
    echo "<tr><td colspan='9' style='text-align:center;'>Tidak ditemukan data untuk '$q'</td></tr>";
    exit;
}

$no = 1;
while ($row = $result->fetch_assoc()) {
    echo "<tr data-id='{$row['id']}'>
        <td>{$no}</td>
        <td>{$row['nip']}</td>
        <td>{$row['fullname']}</td>
        <td style='text-align: center;'>{$row['status_pegawai']}</td>
        <td>{$row['jabatan']}</td>
        <td>{$row['organisasi']}</td>
        <td>{$row['organisasi_induk']}</td>
        <td>{$row['tempat_lahir']}, {$row['tanggal_lahir']}</td>
        <td style='text-align:center;'>
        <a href='detail_asn.php?id={$row['id']}' 
            style='background: #007bff; color: white; 
            padding: 10px 20px; margin: 5px;
            font-size: 17px;
            width: 100px; text-decoration:none;'>
            Detail
        </a>
        </td>
    </tr>";
    $no++;
}
?>


