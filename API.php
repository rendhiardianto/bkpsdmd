<?php
$token = "AmayGawagk435ccehWgu"; //isi dengan token anda
$target = "6289507283753"; //isi dengan target nomor tujuan

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
    'message' => 'Test Notifikasi PHP ke WhatsApp, dikirim dari Website resmi https://bkpsdmd.meranginkab.go.id',
),
  CURLOPT_HTTPHEADER => array(
    "Authorization: $token"
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
