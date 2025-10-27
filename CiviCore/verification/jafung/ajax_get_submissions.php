<?php
require_once __DIR__ . '/../../db.php';

// Get the filter and search text
$filter = $_GET['filter'] ?? 'all';
$search = trim($_GET['search'] ?? '');

$whereClauses = [];

// Apply filter if not "all"
if ($filter !== 'all') {
  $whereClauses[] = "s.status = '" . $conn->real_escape_string($filter) . "'";
}

// Apply search
if ($search !== '') {
  $search = $conn->real_escape_string($search);
  $whereClauses[] = "(u.fullname LIKE '%$search%' OR u.phone LIKE '%$search%' OR s.admin_note LIKE '%$search%')";
}

// Combine WHERE clauses
$whereSQL = '';
if (!empty($whereClauses)) {
  $whereSQL = "WHERE " . implode(' AND ', $whereClauses);
}

// Main query
$query = "
  SELECT 
    s.id AS submission_id,
    u.fullname,
    u.phone,
    s.document_path,
    s.status,
    s.admin_note,
    s.submitted_at
  FROM jafung_submissions s
  LEFT JOIN asn_merangin u ON u.id = s.user_id
  $whereSQL
  ORDER BY s.submitted_at DESC
";

$result = $conn->query($query);
$data = [];

if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $data[] = $row;
  }
}

header('Content-Type: application/json');
echo json_encode($data);
