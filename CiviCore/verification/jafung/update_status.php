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

// 💬 WhatsApp message based on status
switch ($status) {
    case 'accepted':
        $message = "📥 Berkas Anda sudah *diterima* untuk direview oleh admin.";
        break;
    case 'approved':
        $message = "✅ Selamat! berkas Anda *sudah disetujui*, sekarang berkas Anda sudah diusulkan ke BKN.";
        break;
    case 'rejected':
        $message = "❌ Mohon maaf, berkas Anda *ditolak*. Alasan: $note";
        break;
    case 'completed':
        $message = "🏁 Berkas Pengajuan Jabatan Fungsional Anda sudah *terbit*. Terima kasih.";
        break;
    default:
        $message = "ℹ️ Status update: $status";
}

// 📱 Send WhatsApp notification
if (sendWhatsAppNotification($phone, $message)) {
    echo "✅ Status '$status' updated and notification sent successfully.";
} else {
    echo "⚠️ Status '$status' updated but failed to send WhatsApp notification.";
}

// 🚫 No redirect (if you want AJAX), or keep redirect if using normal form submit
header("Location: verify_documents.php");
exit;
?>
