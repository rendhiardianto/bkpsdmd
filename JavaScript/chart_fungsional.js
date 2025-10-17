// ======================== Jenis Kelamin =========================
const xArray1 = ["Laki-laki", "Perempuan"];
const yArray1 = [2000, 3000];
const barColors = ["#339cff", "#ff33bb"];

const layout1 = {
    title:"Pejabat Fungsional", height:350, width:400, 
    font: {size: 16, color: '#000'},
    showlegend:true,
    legend: {"orientation": "h", x:0.15, y:-0.1},
    margin: {l:50, r:50, b:100, t:100, pad:4},
};

const data1 = [{
  labels:xArray1, 
  values:yArray1, 
  hole:.4, 
  type:"pie", 
  marker: {
    colors: barColors
  },
  textinfo: "value+percent", 
  textposition: "outside",
  automargin: true, 
}];

Plotly.newPlot("myPlot1", data1, layout1);

// ======================== Top 10 Penjabat Jabatan Fungsional =========================
const xArray2 = [
    "GURU AHLI PERTAMA", "GURU AHLI MUDA", "GURU AHLI MADYA", 
    "BIDAN TERAMPIL", "PERAWAT PENYELIA", "ANALIS KEBIJAKAN AHLI MUDA"
];
const yArray2 = [1996, 654, 495, 152, 101, 86];

const total = [4561].reduce((a, b) => a + b, 0);

// bikin array text berisi "value (percent%)"
const textLabels = yArray2.map(v => `${v.toLocaleString("id-ID")} (${((v/total)*100).toFixed(1)}%)`);

const layout2 = {
    title:"Top 10 Jabatan Fungsional",
    font: {size: 15, color: '#000'},
    height: 350,
    width: 500,
    font: { size: 15, color: '#000' },
    margin: { t: 80, b: 150 }
};

const data2 = [{
  x: xArray2,
  y: yArray2,
  type: "bar",
  text: textLabels,
  textposition: "outside",
  orientation:"v",
  marker: { color: "#0077b6" }
}];

Plotly.newPlot("myPlot2", data2, layout2);

// ======================== End of File =========================
