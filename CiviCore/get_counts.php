<?php
// get_counts.php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/db.php'; // sesuaikan jika db.php di lokasi lain
// nama tabel: saya pakai jafung_submissions (ganti kalau beda)
$table = 'jafung_submissions';

// Ambil filter dari query string
$year  = isset($_GET['year']) && $_GET['year'] !== '' ? (int)$_GET['year'] : null;
$month = isset($_GET['month']) && $_GET['month'] !== '' ? (int)$_GET['month'] : null;

// Build where clause safely
$whereParts = [];
$params = [];
if ($year) {
    $whereParts[] = "YEAR(created_at) = ?";
    $params[] = $year;
}
if ($month) {
    $whereParts[] = "MONTH(created_at) = ?";
    $params[] = $month;
}
$whereSql = count($whereParts) ? 'WHERE ' . implode(' AND ', $whereParts) : '';

// Kita akan query grouped by status, dan juga total overall
$sql = "SELECT LOWER(status) AS status, COUNT(*) AS total
        FROM $table
        $whereSql
        GROUP BY LOWER(status)";

$stmt = $conn->prepare($sql);
if ($params) {
    // bind params dynamically (all ints)
    $types = str_repeat('i', count($params));
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$res = $stmt->get_result();

$counts = []; // will store status => count
$all = 0;
while ($row = $res->fetch_assoc()) {
    $s = $row['status'];
    $t = (int)$row['total'];
    $counts[$s] = $t;
    $all += $t;
}

// Normalize common status keys (adjust mapping if your DB uses Indonesian words)
$map = [
    // DB status (lowercase) => normalized key
    'new'       => 'new',
    'baru'      => 'new',
    'pending'   => 'new',

    'accepted'  => 'accepted',
    'diterima'  => 'accepted',

    'rejected'  => 'rejected',
    'ditolak'   => 'rejected',

    'revised'   => 'revised',
    'revisi'    => 'revised',

    'approved'  => 'approved',
    'selesai'   => 'completed',
    'completed' => 'completed',
    'done'      => 'completed'
];

// initialize expected keys
$norm = [
    'new' => 0,
    'accepted' => 0,
    'rejected' => 0,
    'revised' => 0,
    'approved' => 0,
    'completed' => 0,
];

// map DB counts to normalized keys
foreach ($counts as $status => $cnt) {
    if (isset($map[$status])) {
        $norm[$map[$status]] += $cnt;
    } else {
        // jika status tidak dikenali, ignore atau bisa dimasukkan ke 'others'
        // $norm['others'] = ($norm['others'] ?? 0) + $cnt;
    }
}

// groupCounts sesuai permintaan: new, process (accepted+rejected+revised+approved), done
$groupCounts = [
    'total'   => $all,
    'new'     => $norm['new'],
    'process' => $norm['accepted'] + $norm['rejected'] + $norm['revised'] + $norm['approved'],
    'done'    => $norm['completed']
];

echo json_encode($groupCounts);
exit;
