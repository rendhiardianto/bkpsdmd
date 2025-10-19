<?php

require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../auth.php';

include_once __DIR__ . '/../../datetime_helper.php';


requireRole('super_admin');
$role = 'super_admin';

// Read role (GET first, session fallback)
$role = $_GET['role'] ?? $_SESSION['role'];

// Read “from” page if provided
$fromPage = $_GET['from'] ?? null;

// Define back links for each role
$backLinks = [
    'super_admin'  => '../../dashboard_super_admin.php#tab3',
];
$backUrl = $backLinks[$role];

// Handle filter
$filter = $_GET['filter'] ?? 'all';
$where = '';

if ($filter !== 'all') {
  $where = "WHERE s.status = '" . $conn->real_escape_string($filter) . "'";
}

$query = "
  SELECT 
    s.id AS submission_id,
    u.fullname,
    u.phone,
    s.document_path,
    s.status,
    s.admin_note,
    s.submitted_at
  FROM service_submissions s
  LEFT JOIN asn_merangin u ON u.id = s.user_id
  $where
  ORDER BY s.submitted_at DESC
";

$result = $conn->query($query);

// Get counts for each status
$countQuery = "
  SELECT 
    status,
    COUNT(*) AS total
  FROM service_submissions
  GROUP BY status
";
$countResult = $conn->query($countQuery);

$counts = [
  'pending' => 0,
  'approved' => 0,
  'rejected' => 0,
  'all' => 0
];

if ($countResult && $countResult->num_rows > 0) {
  while ($row = $countResult->fetch_assoc()) {
    $counts[strtolower($row['status'])] = (int)$row['total'];
    $counts['all'] += (int)$row['total'];
  }
}
?>

<?php
$userId = $_SESSION['user_id'];
$result = $conn->query("SELECT nip, fullname, jabatan, organisasi, profile_pic FROM users WHERE id=$userId");
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - Verifikasi Jafung</title>
  <link href="style.css" rel="stylesheet" type="text/css">
</head>
<body>

<div class="header">
    <div class="navbar">
      <a href="<?php echo htmlspecialchars($backUrl); ?>" class="btn btn-secondary" 
      style="text-decoration: none; color:white;">&#10094; Kembali</a>
    </div>
    
    <div class="roleHeader">
      Dashboard Verifikasi Jafung 
    </div>
</div>

<div class="content">
  
  <div class="liveClock">
    <?php echo renderLiveClock(); ?>
  </div>

  <div class="filter-bar">
    <?php
      $filters = [
        'all' => 'All',
        'pending' => 'Pending',
        'approved' => 'Approved',
        'rejected' => 'Rejected'
      ];
      foreach ($filters as $key => $label) {
        $active = ($filter === $key) ? 'active' : '';
        $count = $counts[$key] ?? 0;
        echo "
          <a href='#' data-filter='$key' class='$active filter-$key'>
            $label
            <span class='badge badge-$key'>$count</span>
          </a>
        ";
      }
    ?>
  </div>
  <div class="search-bar">
    <input type="text" id="liveSearch" placeholder="🔍 Search by name, phone, or note...">
  </div>

  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Tanggal Pengajuan</th>
        <th>NIP</th>
        <th>Nama</th>
        <th>Phone</th>
        <th>Jenis Usulan</th>
        <th>Status</th>
        <th>Admin Note</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $no = 1;
      if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
          $statusClass = "status-" . strtolower($row['status']);
      ?>
        <tr>
          <td><?= $no++; ?></td>
          <td><?= htmlspecialchars($row['fullname']); ?></td>
          <td><?= htmlspecialchars($row['fullname']); ?></td>
          <td><?= htmlspecialchars($row['fullname']); ?></td>
          <td><?= htmlspecialchars($row['phone']); ?></td>
          <td>
            <?php if (!empty($row['document_path'])): ?>
              <a href="/layanan/jafung/uploads/documents/<?= urlencode($row['document_path']); ?>" target="_blank">📄 View</a>
            <?php else: ?>
              <span style="color:gray;">No File</span>
            <?php endif; ?>
          </td>
          <td><span class="status-badge <?= $statusClass; ?>"><?= ucfirst($row['status']); ?></span></td>

          <td><?= htmlspecialchars($row['admin_note']); ?></td>
          <td>
            <form method="POST" action="update_status.php" style="display:inline;">
              <input type="hidden" name="id" value="<?= $row['submission_id']; ?>">
              <input type="hidden" name="phone" value="<?= $row['phone']; ?>">
              <input type="text" name="note" class="note-input" placeholder="Reason (if reject)">
              <button type="submit" name="action" value="approve" class="approve-btn">✅</button>
              <button type="submit" name="action" value="reject" class="reject-btn">❌</button>
            </form>
          </td>
        </tr>
      <?php
        }
      } else {
        echo '<tr><td colspan="7" style="text-align:center;">No submissions found for this filter.</td></tr>';
      }
      ?>
    </tbody>
  </table>
</div>
<script>
document.getElementById("liveSearch").addEventListener("keyup", function() {
  const input = this.value.toLowerCase();
  const rows = document.querySelectorAll("table tbody tr");

  rows.forEach(row => {
    const text = row.innerText.toLowerCase();
    row.style.display = text.includes(input) ? "" : "none";
  });
});
</script>
<script>
const tableBody = document.querySelector("table tbody");
const searchInput = document.getElementById("liveSearch");
const filterLinks = document.querySelectorAll(".filter-bar a");

let currentFilter = "all";
let typingTimer;

function fetchSubmissions() {
  const search = searchInput.value.trim();
  fetch(`ajax_get_submissions.php?filter=${currentFilter}&search=${encodeURIComponent(search)}`)
    .then(res => res.json())
    .then(data => {
      tableBody.innerHTML = "";

      if (data.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="7" style="text-align:center;">No submissions found.</td></tr>`;
        return;
      }

      data.forEach((row, index) => {
        const statusClass = "status-" + row.status.toLowerCase();
        const note = row.admin_note ? row.admin_note : "";
        const document = row.document_path 
          ? `<a href="/layanan/jafung/uploads/documents/${encodeURIComponent(row.document_path)}" target="_blank">📄 View</a>`
          : `<span style="color:gray;">No File</span>`;

        tableBody.innerHTML += `
          <tr>
            <td>${index + 1}</td>
            <td>${row.fullname}</td>
            <td>${row.phone}</td>
            <td>${document}</td>
            <td><span class="status-badge ${statusClass}">${row.status.charAt(0).toUpperCase() + row.status.slice(1)}</span></td>
            <td>${note}</td>
            <td>
              <form method="POST" action="update_status.php" style="display:inline;">
                <input type="hidden" name="id" value="${row.submission_id}">
                <input type="hidden" name="phone" value="${row.phone}">
                <input type="text" name="note" class="note-input" placeholder="Reason (if reject)">
                <button type="submit" name="action" value="approve" class="approve-btn">✅</button>
                <button type="submit" name="action" value="reject" class="reject-btn">❌</button>
              </form>
            </td>
          </tr>
        `;
      });
    });
}

// 🧠 Live search (with 300ms delay)
searchInput.addEventListener("keyup", () => {
  clearTimeout(typingTimer);
  typingTimer = setTimeout(fetchSubmissions, 300);
});

// 🧭 Filter buttons
filterLinks.forEach(link => {
  link.addEventListener("click", (e) => {
    e.preventDefault();
    filterLinks.forEach(l => l.classList.remove("active"));
    link.classList.add("active");
    currentFilter = link.dataset.filter;
    fetchSubmissions();
  });
});

// 🔄 Load initial data
fetchSubmissions();
</script>

</body>
</html>
