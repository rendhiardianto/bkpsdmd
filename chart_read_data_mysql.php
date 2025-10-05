Alurnya seperti ini:

1. Ambil data dari MySQL pakai PHP

2. Convert ke JSON supaya bisa dipakai JavaScript

3. Masukkan hasil JSON ke dalam xArray dan yArray

Contoh sederhana (misalnya kamu punya tabel "pegawai" dengan kolom "jenis_kelamin"):

<?php
// koneksi database
$conn = new mysqli("localhost", "root", "", "namadb");

// hitung jumlah per gender
$result = $conn->query("
    SELECT jenis_kelamin, COUNT(*) as total 
    FROM pegawai 
    GROUP BY jenis_kelamin
");

$labels = [];
$values = [];

while ($row = $result->fetch_assoc()) {
    $labels[] = $row['jenis_kelamin'];
    $values[] = (int)$row['total'];
}
?>

<!DOCTYPE html>
<html>
<script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
<body>
<div id="myPlot" style="width:100%;max-width:700px"></div>

<script>
// Ambil data dari PHP (JSON encode)
const xArray = <?php echo json_encode($labels); ?>;
const yArray = <?php echo json_encode($values); ?>;

const layout = {
  title:"Penjabat Jabatan Fungsional", 
  height:400, 
  width:600, 
  font: {size: 16, color: '#000'},
  showlegend:true,
  legend: {"orientation": "h", x:0.15, y:-0.1},
  margin: {l:50, r:50, b:100, t:100, pad:4},
};

const data = [{
  labels:xArray, 
  values:yArray, 
  hole:.4, 
  type:"pie", 
  marker: { colors: ["#339cff", "#ff33bb"] },
  textinfo: "value+percent", 
  textposition: "outside",
  automargin: true, 
}];

Plotly.newPlot("myPlot", data, layout);
</script>
</body>
</html>
