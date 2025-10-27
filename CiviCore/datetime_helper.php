<?php
// CiviCore/includes/datetime_helper.php

// Set timezone
date_default_timezone_set('Asia/Jakarta');

/**
 * Get current date (YYYY-MM-DD)
 */
function getCurrentDate() {
    return date('Y-m-d');
}

/**
 * Get current time (HH:MM:SS)
 */
function getCurrentTime() {
    return date('H:i:s');
}

/**
 * Get full datetime (YYYY-MM-DD HH:MM:SS)
 */
function getCurrentDateTime() {
    return date('Y-m-d H:i:s');
}

/**
 * Format date into Indonesian format
 * Example: formatIndoDate('2025-10-17') => "17 Oktober 2025"
 */
function formatIndoDate($date) {
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $parts = explode('-', $date);
    return $parts[2] . ' ' . $bulan[(int)$parts[1]] . ' ' . $parts[0];
}

/**
 * Show time ago (e.g. "5 menit yang lalu")
 */
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;

    if ($diff < 60) return "Baru saja";
    elseif ($diff < 3600) return floor($diff / 60) . " menit yang lalu";
    elseif ($diff < 86400) return floor($diff / 3600) . " jam yang lalu";
    elseif ($diff < 604800) return floor($diff / 86400) . " hari yang lalu";
    else return formatIndoDate(date('Y-m-d', $timestamp));
}

/**
 * Render live clock synced with server time (Indonesian format)
 */
function renderLiveClock() {
    $serverTime = time() * 1000; // in milliseconds
    ob_start();
?>
    <div id="live-clock" style="
        font-family: 'Segoe UI', sans-serif;
        font-size: 15px;
        font-weight: 600;
        color: #111827;
        background: #f3f4f6;
        display: inline-block;
        padding: 6px 12px;
        border-radius: 0px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.35);
    ">
        Memuat waktu...
    </div>

    <script>
      const serverTime = <?= $serverTime; ?>;
      let currentTime = new Date(serverTime);

      const hariIndo = ["Minggu","Senin","Selasa","Rabu","Kamis","Jumat","Sabtu"];
      const bulanIndo = [
        "Januari","Februari","Maret","April","Mei","Juni",
        "Juli","Agustus","September","Oktober","November","Desember"
      ];

      function padZero(num) {
        return num < 10 ? "0" + num : num;
      }

      function updateClock() {
        currentTime.setSeconds(currentTime.getSeconds() + 1);

        const hari = hariIndo[currentTime.getDay()];
        const tanggal = currentTime.getDate();
        const bulan = bulanIndo[currentTime.getMonth()];
        const tahun = currentTime.getFullYear();

        const jam = padZero(currentTime.getHours());
        const menit = padZero(currentTime.getMinutes());
        const detik = padZero(currentTime.getSeconds());

        const waktuLengkap = `${hari}, ${tanggal} ${bulan} ${tahun} | ${jam}:${menit}:${detik}`;
        document.getElementById("live-clock").textContent = waktuLengkap;
      }

      updateClock();
      setInterval(updateClock, 1000);
    </script>
<?php
    return ob_get_clean();
}
?>

<?php
function formatTanggalIndonesia($datetime) {
    if (empty($datetime)) return '';

    $hari = [
        'Sunday' => 'Minggu',
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu'
    ];

    $bulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
        4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September',
        10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];

    date_default_timezone_set('Asia/Jakarta');
    $timestamp = strtotime($datetime);

    $namaHari = $hari[date('l', $timestamp)];
    $tgl = date('j', $timestamp);
    $bln = $bulan[(int)date('n', $timestamp)];
    $thn = date('Y', $timestamp);
    $jam = date('H:i', $timestamp);

    return "$namaHari, $tgl $bln $thn $jam";
}
?>

