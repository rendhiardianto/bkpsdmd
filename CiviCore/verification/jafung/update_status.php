<?php
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../api_notification.php';

$id = $_POST['id'];
$action = $_POST['action'];
$phone = $_POST['phone'];
$note = trim($_POST['note'] ?? '');

$status = ($action === 'approve') ? 'approved' : 'rejected';

$stmt = $conn->prepare("UPDATE service_submissions SET status=?, admin_note=?, verified_at=NOW() WHERE id=?");
$stmt->bind_param("ssi", $status, $note, $id);
$stmt->execute();

// send WhatsApp notification
$message = ($status === 'approved')
  ? "✅ Your document has been approved by admin."
  : "❌ Your document was rejected. Reason: $note";

if (sendWhatsAppNotification($phone, $message)) {
  echo "Notification sent successfully.";
} else {
  echo "Failed to send WhatsApp notification.";
}
header("Location: verify_documents.php");
exit;
