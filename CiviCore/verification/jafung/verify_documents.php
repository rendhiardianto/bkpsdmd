<?php

require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../auth.php';
include_once __DIR__ . '/../../datetime_helper.php';

requireRole('super_admin');

// --- Determine role and back link ---
$role = $_GET['role'] ?? $_SESSION['role'] ?? 'super_admin';
$backLinks = [
    'super_admin' => '../../dashboard_super_admin.php',
];
$backUrl = $backLinks[$role] ?? '../../dashboard_super_admin.php';

// --- Filter setup ---
$filter = $_GET['filter'] ?? 'all';
$where = '';
if ($filter !== 'all') {
    $where = "WHERE s.status = '" . $conn->real_escape_string($filter) . "'";
}

// --- Main query ---
$query = "
    SELECT 
        s.id AS submission_id,
        s.nip,
        s.fullname,
        s.jenis_usulan,
        s.document_paths,
        s.status,
        s.final_doc,
        s.admin_note,
        s.user_note,
        s.created_at,
        s.phone
    FROM jafung_submissions s
    LEFT JOIN asn_merangin a ON a.nip = s.nip
    $where
    ORDER BY s.created_at DESC
";
$result = $conn->query($query);

// --- Count query ---
$countQuery = "
    SELECT status, COUNT(*) AS total
    FROM jafung_submissions
    GROUP BY status
";
$countResult = $conn->query($countQuery);

// --- Initialize counters for all new statuses ---
$counts = [
    'new' => 0,
    'accepted' => 0,
    'rejected' => 0,
    'revised' => 0,
    'approved' => 0,
    'completed' => 0,
    'all' => 0
];

if ($countResult && $countResult->num_rows > 0) {
    while ($row = $countResult->fetch_assoc()) {
        $key = strtolower($row['status']);
        if (isset($counts[$key])) {
            $counts[$key] = (int)$row['total'];
        }
        $counts['all'] += (int)$row['total'];
    }
}

// --- Current user info ---
$userId = $_SESSION['user_id'];
$resultUser = $conn->query("SELECT nip, fullname, jabatan, organisasi, profile_pic FROM users WHERE id=$userId");
$user = $resultUser->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - Verifikasi Jafung</title>
  <link href="style.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>

<div class="header">
  <div class="navbar">
    <a href="<?php echo htmlspecialchars($backUrl); ?>" class="btn btn-secondary" style="text-decoration: none; color:white;">&#10094; Kembali</a>
  </div>
  <div class="roleHeader">
    <h1>Daftar Permohonan Pengajuan Jafung</h1>
  </div>

</div>

<!-- 🔹 Popup HTML (added once globally) -->
<div id="approvePopup" class="popup-confirm">
  <div class="popup-content">
    <h3>Konfirmasi Persetujuan</h3>
    <p>Apakah berkas ini <b>sudah diusulkan</b> ke BKN?</p>
    <p>Jika sudah, ketik kode berikut untuk konfirmasi:</p>
    <b id="confirmCode"></b><br>
    <input type="text" id="codeInput" placeholder="Masukkan kode di atas">
    <div class="popup-buttons">
      <button class="confirm-btn" id="confirmApproveBtn">Konfirmasi</button>
      <button class="cancel-btn" id="cancelApproveBtn">Batal</button>
    </div>
  </div>
</div>

<div class="content">

  <div class="liveClock">
    <?php echo renderLiveClock(); ?>
  </div>

  <!-- 🔹 Filter Buttons -->
  <div class="filter-bar">
    <?php
      $filters = [
        'all' => 'Semua',
        'new' => 'Baru',
        'accepted' => 'Diterima',
        'rejected' => 'Ditolak',
        'revised' => 'Direvisi',
        'approved' => 'Disetujui',
        'completed' => 'Selesai'
      ];
      foreach ($filters as $key => $label) {
        $active = ($filter === $key) ? 'active' : '';
        $count = $counts[$key] ?? 0;
        echo "<a href='?filter=$key' class='$active'>$label <span class='badge'>$count</span></a>";
      }
    ?>
  </div>

  <div class="search-bar">
    <input type="text" id="liveSearch" placeholder="🔍 Cari data yang diperlukan">
  </div>

  <table>
    <thead>
      <tr>
        <th>No</th>
        <th style="width: 150px;">Tanggal Pengajuan</th>
        <th>Status</th>
        <th>NIP</th>
        <th>Nama Lengkap</th>
        <th>No HP</th>
        <th style="width: 150px;">Jenis Pengajuan</th>
        <th style="width: 200px;">Dokumen</th>
        <th>Instruksi</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
    <?php
    $no = 1;

    // --- Mapping jenis_usulan ---
    $jenisMap = [
      'JF4' => '(JF4) Kenaikan Jenjang JF',
      'JF3' => '(JF3) Pengangkatan Kembali ke dalam JF',
      'JF2' => '(JF2) Perpindahan dari Jabatan Lain ke dalam JF',
      'JF1' => '(JF1) Pengangkatan Pertama dalam JF'
    ];

    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        $statusClass = "status-" . strtolower($row['status']);
        $userNote = $row['user_note'] ??'';
        $docs = json_decode($row['document_paths'], true);
        $jenisCode = $row['jenis_usulan'] ?? '';
        $displayJenis = $jenisMap[$jenisCode] ?? htmlspecialchars($jenisCode);
    ?>
      <tr>
        <td><?= $no++; ?></td>
        <td><b><?= formatTanggalIndonesia($row['created_at']); ?></b></td>

        <td style="text-align:center">
          <span class="status-badge <?= $statusClass; ?>">
            <?= ucfirst($row['status']); ?>
          </span>
          <?php if (strtolower($row['status']) === 'revised' && !empty($row['user_note'])): ?>
            <br><b>Catatan Revisi:</b> 
            <br><div style="color: green;">
              <?= htmlspecialchars($row['user_note']); ?>
            </div>
          <?php endif; ?>
        </td>


        <td><?= htmlspecialchars($row['nip']); ?></td>
        <td><?= htmlspecialchars($row['fullname']); ?></td>
        <td><?= htmlspecialchars($row['phone']); ?></td>
        <td style="text-align: center;"><?= $displayJenis; ?></td>
        <td style="text-align: center;">
          <?php 
            if (!empty($docs)) {
              $status = strtolower($row['status']);

              // show detail button only for accepted & revised
              if (in_array($status, ['accepted', 'revised'])) {
                echo "<a href='detail_document.php?id={$row['submission_id']}' class='verify-btn' target='_blank'>Verifikasi Berkas</a><br>";
              }
              if ($status === 'approved') {
                echo "<a href='detail_document.php?id={$row['submission_id']}' class='detail-btn' target='_blank'>Lihat Berkas</a><br>";
              }
              // show upload final doc only when status = complete
              if ($status === 'completed') {
                echo "<a href='upload_final_document.php?id={$row['submission_id']}' class='upload-final-btn'>Upload SK</a><br>";
                echo "<a href='detail_document.php?id={$row['submission_id']}' class='detail-btn'>Lihat Berkas</a><br>";
              }
              // 🧩 show rejection note if rejected
              if ($status === 'rejected' && !empty($row['admin_note'])) {
                echo "<div style='margin-top:8px; color:#b30000; font-size:13px; background:#ffeaea; border:1px solid #f5c2c2; padding:6px; border-radius:8px; text-align:left;'>
                        <strong>Alasan Penolakan:</strong><br>" . nl2br(htmlspecialchars($row['admin_note'])) . "
                      </div>";
              }
            } else {
              echo "<span style='color:gray;'>No File</span>";
            }
          ?>
        </td>
        <td style="text-align: center; color:red;">
          <?php 
            if (!empty($docs)) {
              if (strtolower($row['status']) === 'new') {
                echo "Terima berkas!";
              }
              if (strtolower($row['status']) === 'accepted') {
                echo "Verifikasi berkas terlebih dahulu dan langsung usulkan melalui I-MUT BKN. 
                <br> Lalu Setujui atau Tolak!";
              }
              if (strtolower($row['status']) === 'rejected') {
                echo "Tunggu, berkas sedang diperbaiki oleh Pegawai bersangkutan.";
              }
              if (strtolower($row['status']) === 'approved') {
                echo "Berkas sudah diusulkan melalui I-MUT BKN, jika SK sudah terbit, 
                klik Selesai!";
              }
              if (strtolower($row['status']) === 'revised') {
                echo "Berkas sudah direvisi, silahkan periksa kembali! <br> Lanjut usulkan melalui I-MUT BKN.
                <br> Lalu Setujui atau Tolak!";
              }
              if (strtolower($row['status']) === 'completed') {
                if (!empty($row['final_doc'])) {
                    // ✅ SK uploaded → show link to view it
                    $finalDocPath = htmlspecialchars($row['final_doc']);
                    echo "SK sudah diupload.<br>";
                    echo "<a href='uploads/final_docs/{$finalDocPath}' target='_blank' class='lihat-sk-btn'>
                            Lihat SK
                          </a>";
                } else {
                    // ⚠️ No SK yet → prompt admin to upload
                    echo "Silahkan Upload SK Jafung";
                }
            }
            } else {
              echo "<span style='color:gray;'>No File</span>";
            }
          ?>
        </td>
        <td>
          <form method="POST" action="update_status.php" class="action-form">
            <input type="hidden" name="id" value="<?= $row['submission_id']; ?>">
            <input type="hidden" name="phone" value="<?= htmlspecialchars($row['phone']); ?>">
            
            <?php 
              $status = strtolower($row['status']);
              switch ($status) {
                case 'new':
                  echo '<button type="submit" name="action" value="accepted" class="accept-btn">✔️ Terima</button>';
                  break;
                case 'accepted':
                  echo '<button type="submit" name="action" value="approved" class="approve-btn">✔️ Setujui</button>';
                  echo '<button type="submit" name="action" value="rejected" class="reject-btn">❌ Tolak</button>';
                  echo '<input type="text" name="note" class="note-input" placeholder="Alasan penolakan">';
                  break;
                case 'revised':
                  echo '<button type="submit" name="action" value="approved" class="approve-btn">✔️ Setujui</button>';
                  echo '<button type="submit" name="action" value="rejected" class="reject-btn">❌ Tolak</button>';
                  echo '<input type="text" name="note" class="note-input" placeholder="Alasan penolakan">';
                  break;
                case 'approved':
                  echo '<button type="submit" name="action" value="completed" class="complete-btn">🏁 Selesai</button>';
                  break;
                case 'rejected':
                  // Show delete button for rejected cases
                  echo '<button type="submit" name="action" value="delete" class="delete-btn">🗑️ Hapus</button>';
                  break;
                default:
                  echo '<span style="color:gray;">Selesai</span>';
              }
            ?>
          </form>
        </td>
        
      </tr>
    <?php
      }
    } else {
      echo '<tr><td colspan="10" style="text-align:center;">Tidak ada berkas yang ditemukan.</td></tr>';
    }
    ?>
    </tbody>
  </table>
</div>

<!------------------- FOOTER ----------------------------------->	
<div class="gotoTop" onclick="topFunction()" id="myBtn" title="Go to top"> <img src="../../../icon/go_to_top.png"></div>
<script src="../../../JavaScript/back_to_top.js"></script>

<div id="footer"></div>
<script>
fetch("../../footer.php")
  .then(response => response.text())
  .then(data => {
    document.getElementById("footer").innerHTML = data;
  });
</script>
<!------------------- BATAS AKHIR CONTENT ---------------------------------->

<script>
document.getElementById("liveSearch").addEventListener("keyup", function() {
  const input = this.value.toLowerCase();
  document.querySelectorAll("table tbody tr").forEach(row => {
    row.style.display = row.innerText.toLowerCase().includes(input) ? "" : "none";
  });
});

// 🔹 APPROVE BUTTON POPUP CONFIRMATION
let currentForm = null;
let generatedCode = "";

document.querySelectorAll(".approve-btn").forEach(btn => {
  btn.addEventListener("click", function(e) {
    e.preventDefault(); // Stop normal submit
    currentForm = this.closest("form");
    generatedCode = Math.random().toString(36).substring(2, 8).toUpperCase();
    document.getElementById("confirmCode").textContent = generatedCode;
    document.getElementById("codeInput").value = "";
    document.getElementById("approvePopup").style.display = "block";
  });
});

document.getElementById("confirmApproveBtn").addEventListener("click", function() {
  const inputCode = document.getElementById("codeInput").value.trim().toUpperCase();
  if (inputCode === generatedCode) {
    document.getElementById("approvePopup").style.display = "none";
    if (currentForm) {
    // ensure "action" value is sent properly
    const hidden = document.createElement("input");
    hidden.type = "hidden";
    hidden.name = "action";
    hidden.value = "approved";
    currentForm.appendChild(hidden);
    currentForm.submit();
  }

  } else {
    alert("Kode salah! Silakan coba lagi.");
  }
});

document.getElementById("cancelApproveBtn").addEventListener("click", function() {
  document.getElementById("approvePopup").style.display = "none";
});
</script>

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

</body>
</html>
