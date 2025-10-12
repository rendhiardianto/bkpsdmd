// ======================== KATEGORI ASN =========================
const xArray0 = ["PNS", "PPPK", "PPPK PW"];
const yArray0 = [4661, 3105, 3510];
const barColors0 = ["#ffbb00ff", "#33adffff", "#ff33eeff"];

const layout0 = {
    title:"KATEGORI ASN", height:350, width:500, 
    font: {size: 14, color: '#000'},
    showlegend:true,
    legend: {"orientation": "h", x:0, y:-0.5},
    margin: {l:50, r:50, b:100, t:100, pad:4},
};

const data0 = [{
  labels:xArray0, 
  values:yArray0, 
  hole:.5, 
  type:"pie", 
  marker: {
    colors: barColors0
  },
  textinfo: "value+percent", 
  textposition: "outside",
  automargin: true, 
}];

Plotly.newPlot("myPlot0", data0, layout0);

// ======================== PNS - Jenis Kelamin =========================
fetch("/cms/rekap_asn_merangin/ajax_get_rekap_asn.php?kategori=PNS&sub_kategori=Jenis%20Kelamin&semester=2")
  .then(response => response.json())
  .then(data => {
    const xArray1 = data.map(row => row.label);
    const yArray1 = data.map(row => parseInt(row.jumlah));

    const barColors1 = ["#339cff", "#ff33bb"];
    const layout1 = {
        title:"PNS - Jenis Kelamin", height:350, width:350, 
        font: {size: 14, color: '#000'},
        showlegend:true,
        legend: {"orientation": "h", x:-0.1, y:-0.5},
        margin: {l:50, r:50, b:100, t:100, pad:4},
    };

    const data1 = [{
      labels:xArray1, 
      values:yArray1, 
      hole:.5, 
      type:"pie", 
      marker: { colors: barColors1 },
      textinfo: "value+percent", 
      textposition: "outside",
      automargin: true, 
    }];

    Plotly.newPlot("myPlot1", data1, layout1);
  })
  .catch(err => {
    console.error("Error fetching data:", err);
  });


// ======================== PPPK - Jenis Kelamin =========================
fetch("/cms/rekap_asn_merangin/ajax_get_rekap_asn.php?kategori=PPPK&sub_kategori=Jenis%20Kelamin&semester=2")
  .then(response => response.json())
  .then(data => {
    const xArray2 = data.map(row => row.label);
    const yArray2 = data.map(row => parseInt(row.jumlah));

const barColors2 = ["#339cff", "#ff33bb"];

const layout2 = {
    title:"PPPK - Jenis Kelamin", height:350, width:350, 
    font: {size: 14, color: '#000'},
    showlegend:true,
    legend: {"orientation": "h", x:-0.1, y:-0.5},
    margin: {l:50, r:50, b:100, t:100, pad:4},
};

const data2 = [{
  labels:xArray2, 
  values:yArray2, 
  hole:.5, 
  type:"pie", 
  marker: {
    colors: barColors2
  },
  textinfo: "value+percent", 
  textposition: "outside",
  automargin: true, 
}];

Plotly.newPlot("myPlot2", data2, layout2);
})
  .catch(err => {
    console.error("Error fetching data:", err);
  });
// ======================== PPPK Paruh Waktu - Jenis Kelamin =========================
const xArray3 = ["Laki-laki", "Perempuan"];
const yArray3 = [1510, 2000];
const barColors3 = ["#339cff", "#ff33bb"];

const layout3 = {
    title:"PPPK Paruh Waktu - Jenis Kelamin", height:350, width:350, 
    font: {size: 14, color: '#000'},
    showlegend:true,
    legend: {"orientation": "h", x:-0.1, y:-0.5},
    margin: {l:50, r:50, b:100, t:100, pad:4},
};

const data3 = [{
  labels:xArray3, 
  values:yArray3, 
  hole:.5, 
  type:"pie", 
  marker: {
    colors: barColors3
  },
  textinfo: "value+percent", 
  textposition: "outside",
  automargin: true, 
}];

Plotly.newPlot("myPlot3", data3, layout3);

// ======================== Top 10 Penjabat Jabatan Fungsional =========================
const xArray100 = [
    "GURU AHLI PERTAMA", "GURU AHLI MUDA", "GURU AHLI MADYA", 
    "BIDAN TERAMPIL", "PERAWAT PENYELIA", "ANALIS KEBIJAKAN AHLI MUDA"
];
const yArray100 = [1996, 654, 495, 152, 101, 86];

const total = [4561].reduce((a, b) => a + b, 0);

// bikin array text berisi "value (percent%)"
const textLabels = yArray100.map(v => `${v.toLocaleString("id-ID")} (${((v/total)*100).toFixed(1)}%)`);

const layout100 = {
    title:"Top 10 Jabatan Fungsional",
    height:350, 
    width:600, 
    font: {size: 15, color: '#000'},
};

const data100 = [{
  x: xArray100,
  y: yArray100,
  type: "bar",
  text: textLabels,
  textposition: "outside",
  orientation:"v",
  marker: {color:"rgba(0,0,255)"}
}];

Plotly.newPlot("myPlot100", data100, layout100);

// ======================== End of File =========================
