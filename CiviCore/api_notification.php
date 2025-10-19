<?php
include "db.php";

function sendWhatsAppNotification($phone, $message) {
  global $conn; // use your global DB connection

  $token = "HBB89yoJxZs2DtcH5AR2"; // 🔒 your Fonnte API token
  
  // Clean + normalize phone number
  $target = preg_replace('/[^0-9]/', '', $phone);
  if (strpos($target, '62') !== 0) {
    $target = '62' . ltrim($target, '0');
  }

  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://api.fonnte.com/send',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => array(
      'target' => $target,
      'message' => $message,
    ),
    CURLOPT_HTTPHEADER => array(
      "Authorization: $token"
    ),
  ));

  $response = curl_exec($curl);
  $error = curl_error($curl);
  curl_close($curl);

  // Determine status
  $status = ($error) ? 'failed' : 'success';
  $responseText = $error ? $error : $response;

  // Log to DB
  $stmt = $conn->prepare("INSERT INTO wa_notifications (phone, message, response, status) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("ssss", $target, $message, $responseText, $status);
  $stmt->execute();

  // Return boolean success/fail
  return $status === 'success';
}
?>
