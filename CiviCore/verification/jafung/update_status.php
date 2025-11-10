<?php
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../api_notification.php';

// Retrieve POST values
$id = $_POST['id'] ?? 0;
$action = strtolower(trim($_POST['action'] ?? ''));
$phone = trim($_POST['phone'] ?? '');
$note = trim($_POST['note'] ?? '');
$status = $action;

// ✅ Define allowed status values
$allowedStatuses = ['accepted', 'approved', 'rejected', 'completed', 'delete', 'revised'];

if ($action === 'delete') {
    // 1. Get document_paths JSON for this submission
    $getFiles = $conn->prepare("SELECT document_paths FROM jafung_submissions WHERE id = ?");
    $getFiles->bind_param("i", $id);
    $getFiles->execute();
    $res = $getFiles->get_result();
    $rowFile = $res->fetch_assoc();
    $getFiles->close();

    $uploadDir = realpath(__DIR__ . '/../../../layanan/jafung/uploads/documents/');
    $deleted = [];
    $notFound = [];
    $errors = [];

    if (!empty($rowFile['document_paths'])) {
        $filesArr = json_decode($rowFile['document_paths'], true);

        if (is_array($filesArr)) {
            foreach ($filesArr as $key => $filename) {
                if (empty($filename)) continue;

                $safeName = basename($filename);
                $filePath = $uploadDir . DIRECTORY_SEPARATOR . $safeName;

                if (file_exists($filePath)) {
                    if (@unlink($filePath)) {
                        $deleted[] = $filePath;
                    } else {
                        $errors[] = "Failed to unlink: $filePath";
                    }
                } else {
                    $notFound[] = $filePath;
                }
            }
        } else {
            $errors[] = "document_paths JSON invalid or empty.";
        }
    } else {
        $notFound[] = '(no document_paths stored)';
    }

    // 2. Delete the submission record
    $stmtDel = $conn->prepare("DELETE FROM jafung_submissions WHERE id = ?");
    $stmtDel->bind_param("i", $id);
    $okDel = $stmtDel->execute();
    $stmtDel->close();

    // 3. Debug / confirmation
    if ($okDel) {
        echo "✅ Submission deleted.<br>";
        if (!empty($deleted)) echo "Deleted files:<br><ul><li>" . implode("</li><li>", array_map('htmlspecialchars', $deleted)) . "</li></ul>";
        if (!empty($notFound)) echo "Not found:<br><ul><li>" . implode("</li><li>", array_map('htmlspecialchars', $notFound)) . "</li></ul>";
        if (!empty($errors)) echo "Errors:<br><ul><li>" . implode("</li><li>", array_map('htmlspecialchars', $errors)) . "</li></ul>";
    } else {
        echo "❌ Failed to delete submission from database.";
    }

    header("Location: verify_documents.php");
    exit;
}

// 🔍 Validate
if (!in_array($action, $allowedStatuses)) {
    echo "❌ Invalid status action.";
    exit;
}

// 🗃️ Update database
$stmt = $conn->prepare("UPDATE jafung_submissions SET status=?, admin_note=?, verified_at=NOW() WHERE id=?");
$stmt->bind_param("ssi", $status, $note, $id);
$stmt->execute();

// 🧩 Fetch user data (ticket_number, fullname, phone)
$getUser = $conn->prepare("SELECT ticket_number, fullname, phone FROM jafung_submissions WHERE id = ?");
$getUser->bind_param("i", $id);
$getUser->execute();
$resUser = $getUser->get_result();
$user = $resUser->fetch_assoc();
$getUser->close();

$fullname = $user['fullname'] ?? 'ASN';
$phone = $user['phone'] ?? $phone; // fallback to POST value if missing
$ticket_no = $user['ticket_number'];

/// 💬 WhatsApp message based on status
switch ($status) {
    case 'accepted':
        $message = "👋 Halo #KantiASN *{$fullname}*,\n\n".
                   "Berkas Pengajuan Jabatan Fungsional Anda sudah *diterima* dan sedang diperiksa oleh Tim JAFUNG. ".
                   "Berkas Anda masih mungkin *Disetujui* atau *Ditolak*. Terima kasih 🙏\n\n\n".
                   "_(Pesan ini dikirim otomatis oleh sistem, tidak perlu dibalas)_";
        break;

    case 'approved':
        $message = "👋 Halo #KantiASN *{$fullname}*,\n\n".
                   "✅ Selamat! Berkas Pengajuan Jabatan Fungsional Anda sudah *disetujui* dan saat ini telah diusulkan ke *BKN*. ".
                   "Mohon ditunggu proses selanjutnya.\n\n".
                   "🎫 *No. Tiket berkas Anda : {$ticket_no}*\n\n\n".
                   "_(Pesan ini dikirim otomatis oleh sistem, tidak perlu dibalas)_";
        break;

    case 'rejected':
        $message = "👋 Halo #KantiASN *{$fullname}*,\n\n".
                   "❌ Mohon maaf, Berkas Pengajuan Jabatan Fungsional Anda *ditolak*.\n\n".
                   "Alasan: {$note}\n\nSilakan perbaiki sesuai catatan di atas.\n\n".
                   "Kunjungi halaman Pengajuan Jafung untuk memperbaiki berkas Anda.\n".
                   "👉 https://bkpsdmd.meranginkab.go.id/layanan/jafung/\n\n\n". 
                   "_(Pesan ini dikirim otomatis oleh sistem, tidak perlu dibalas)_";
        break;

    case 'completed':
        $message = "👋 Halo #KantiASN *{$fullname}*,\n\n".
                   "🏁 SK Pengajuan Jabatan Fungsional Anda sudah *terbit*. 🎉\n\n".
                   "🎫 *No. Tiket berkas Anda : {$ticket_no}*\n\n".
                   "Silahkan download SK Anda di website resmi *BKPSDMD Merangin*:\n".
                   "👉 https://bkpsdmd.meranginkab.go.id/MyDocuments/\n\n". 
                   "Terima kasih atas kerja samanya 🙏\n\n\n". 
                   "_(Pesan ini dikirim otomatis oleh sistem, tidak perlu dibalas)_";
        break;

    default:
        $message = "ℹ️ Halo #KantiASN *{$fullname}*,\n\n".
                   "Status pengajuan Anda saat ini: *{$status}*.";
}


// 📱 Send WhatsApp notification
if (sendWhatsAppNotification($phone, $message)) {
    echo "✅ Status '$status' updated and notification sent successfully.";
} else {
    echo "⚠️ Status '$status' updated but failed to send WhatsApp notification.";
}

// 🚫 No redirect (if AJAX) or redirect if using normal form submit
header("Location: verify_documents.php");
exit;

?>
