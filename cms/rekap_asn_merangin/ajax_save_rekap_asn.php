<?php
include "../db.php";

ob_clean();
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: application/json");

// Read form data safely
$tahun        = trim($_POST['tahun'] ?? '');
$semester     = trim($_POST['semester'] ?? '');
$kategori     = trim($_POST['kategori'] ?? '');
$sub_kategori = trim($_POST['sub_kategori'] ?? '');
$total        = floatval($_POST['total'] ?? 0);

if (empty($tahun) || empty($semester) || empty($kategori) || empty($sub_kategori)) {
    echo json_encode(["success" => false, "message" => "Tahun, Kategori, Sub-Kategori wajib diisi."]);
    exit;
}

// Remove helper fields
unset($_POST['tahun'], $_POST['semester'], $_POST['kategori'], $_POST['sub_kategori'], $_POST['total'], $_POST['label']);

// 1️⃣ Collect all submitted labels
$labels = [];
$insert_data = []; // store label + jumlah for insert
foreach ($_POST as $field_name => $jumlah) {
    if ($jumlah === "" || !is_numeric($jumlah)) continue;

    $label_clean = ucwords(str_replace("_", " ", $field_name));
    $labels[] = $label_clean;
    $insert_data[] = [
        'label' => $label_clean,
        'jumlah' => intval($jumlah)
    ];
}

// 2️⃣ Check if any of these labels already exist for this combination
if (!empty($labels)) {
    // Create placeholders for prepared statement
    $placeholders = implode(',', array_fill(0, count($labels), '?'));
    
    $types = str_repeat('s', 5) . str_repeat('s', count($labels)); // 5 strings for tahun, semester, kategori, sub_kategori, plus labels
    $check_sql = "SELECT label FROM rekap_asn_merangin 
                  WHERE tahun = ? AND semester = ? AND kategori = ? AND sub_kategori = ? 
                  AND label IN ($placeholders)";
    
    $stmt = $conn->prepare($check_sql);
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "SQL Error: " . $conn->error]);
        exit;
    }

    // Bind params dynamically
    $bind_params = array_merge([$tahun, $semester, $kategori, $sub_kategori], $labels);
    $types = str_repeat('s', count($bind_params));

    $tmp = [];
    foreach ($bind_params as $key => $value) {
        $tmp[$key] = &$bind_params[$key];
    }
    array_unshift($tmp, $types);
    call_user_func_array([$stmt, 'bind_param'], $tmp);

    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $existing_labels = [];
        while ($row = $result->fetch_assoc()) {
            $existing_labels[] = $row['label'];
        }
        echo "Data sudah ada untuk label: " . implode(', ', $existing_labels) . ". Silahkan edit saja!";
        exit;
    }
    $stmt->close();
}

// 3️⃣ Insert all rows (none existed before)
$insert_sql = "INSERT INTO rekap_asn_merangin 
               (tahun, semester, kategori, sub_kategori, label, jumlah, created_at)
               VALUES (?, ?, ?, ?, ?, ?, NOW())";
$insert_stmt = $conn->prepare($insert_sql);

foreach ($insert_data as $data) {
    $insert_stmt->bind_param("sssssi", $tahun, $semester, $kategori, $sub_kategori, $data['label'], $data['jumlah']);
    $insert_stmt->execute();
}

$insert_stmt->close();
$conn->close();

echo "Data berhasil disimpan.";
?>
