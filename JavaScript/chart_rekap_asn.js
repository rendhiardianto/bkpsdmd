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
fetch("/CiviCore/rekap_asn_merangin/ajax_get_rekap_asn.php?kategori=PNS&sub_kategori=Jenis%20Kelamin&semester=2")
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
fetch("/CiviCore/rekap_asn_merangin/ajax_get_rekap_asn.php?kategori=PPPK&sub_kategori=Jenis%20Kelamin&semester=2")
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

// ======================== PNS - Pendidikan =========================
fetch("/CiviCore/rekap_asn_merangin/ajax_get_rekap_asn.php?kategori=PNS&sub_kategori=Pendidikan&semester=2")
  .then(response => response.json())
  .then(data => {
    // xArray: nama jabatan
    const xArray = data.map(row => row.label);

    // yArray: jumlah
    const yArray = data.map(row => parseInt(row.jumlah));

    // total jumlah untuk persentase
    const total = yArray.reduce((a, b) => a + b, 0);

    // bikin array text berisi "value (percent%)"
    const textLabels = yArray.map(v => `${v.toLocaleString("id-ID")} (${((v / total) * 100).toFixed(1)}%)`);

    const layout = {
      title: "PNS - Pendidikan",
      height: 500,
      width: 700,
      font: { size: 15, color: '#000' },
      margin: { t: 80, b: 150 }
    };

    const chartData = [{
      x: xArray,
      y: yArray,
      type: "bar",
      text: textLabels,
      textposition: "outside",
      orientation: "v",
      marker: { color: "#0077b6" }
    }];

    Plotly.newPlot("myPlot100", chartData, layout);
  })
  .catch(err => {
    console.error("Error fetching Pendidkan PNS:", err);
  });

// ======================== PNS - Usia =========================
fetch("/CiviCore/rekap_asn_merangin/ajax_get_rekap_asn.php?kategori=PNS&sub_kategori=Usia&semester=2")
  .then(response => response.json())
  .then(data => {
    // xArray: nama jabatan
    const xArray = data.map(row => row.label);

    // yArray: jumlah
    const yArray = data.map(row => parseInt(row.jumlah));

    // total jumlah untuk persentase
    const total = yArray.reduce((a, b) => a + b, 0);

    // bikin array text berisi "value (percent%)"
    const textLabels = yArray.map(v => `${v.toLocaleString("id-ID")} (${((v / total) * 100).toFixed(1)}%)`);

    const layout = {
      title: "PNS - Usia",
      height: 500,
      width: 700,
      font: { size: 15, color: '#000' },
      margin: { t: 80, b: 150 }
    };

    const chartData = [{
      x: xArray,
      y: yArray,
      type: "bar",
      text: textLabels,
      textposition: "outside",
      orientation: "v",
      marker: { color: "#0077b6" }
    }];

    Plotly.newPlot("myPlot99", chartData, layout);
  })
  .catch(err => {
    console.error("Error fetching Usia PNS:", err);
  });

  // ======================== PNS - Golongan =========================
fetch("/CiviCore/rekap_asn_merangin/ajax_get_rekap_asn.php?kategori=PNS&sub_kategori=Golongan&semester=2")
  .then(response => response.json())
  .then(data => {
    // xArray: nama jabatan
    const xArray = data.map(row => row.label);

    // yArray: jumlah
    const yArray = data.map(row => parseInt(row.jumlah));

    // total jumlah untuk persentase
    const total = yArray.reduce((a, b) => a + b, 0);

    // bikin array text berisi "value (percent%)"
    const textLabels = yArray.map(v => `${v.toLocaleString("id-ID")} (${((v / total) * 100).toFixed(1)}%)`);

    const layout = {
      title: "PNS - Golongan",
      height: 400,
      width: 450,
      font: { size: 15, color: '#000' },
      margin: { t: 80, b: 150 }
    };

    const chartData = [{
      x: xArray,
      y: yArray,
      type: "bar",
      text: textLabels,
      textposition: "outside",
      orientation: "v",
      marker: { color: "#0077b6" }
    }];

    Plotly.newPlot("myPlot98", chartData, layout);
  })
  .catch(err => {
    console.error("Error fetching Golongan PNS:", err);
  });

  // ======================== PNS - Eselon =========================
fetch("/CiviCore/rekap_asn_merangin/ajax_get_rekap_asn.php?kategori=PNS&sub_kategori=Eselon&semester=2")
  .then(response => response.json())
  .then(data => {
    // xArray: nama jabatan
    const xArray = data.map(row => row.label);

    // yArray: jumlah
    const yArray = data.map(row => parseInt(row.jumlah));

    // total jumlah untuk persentase
    const total = yArray.reduce((a, b) => a + b, 0);

    // bikin array text berisi "value (percent%)"
    const textLabels = yArray.map(v => `${v.toLocaleString("id-ID")} (${((v / total) * 100).toFixed(1)}%)`);

    const layout = {
      title: "PNS - Eselon",
      height: 400,
      width: 450,
      font: { size: 15, color: '#000' },
      margin: { t: 80, b: 150 }
    };

    const chartData = [{
      x: xArray,
      y: yArray,
      type: "bar",
      text: textLabels,
      textposition: "outside",
      orientation: "v",
      marker: { color: "#0077b6" }
    }];

    Plotly.newPlot("myPlot97", chartData, layout);
  })
  .catch(err => {
    console.error("Error fetching Eselon PNS:", err);
  });

 // ======================== PNS - Jabatan =========================
fetch("/CiviCore/rekap_asn_merangin/ajax_get_rekap_asn.php?kategori=PNS&sub_kategori=Jabatan&semester=2")
  .then(response => response.json())
  .then(data => {
    // xArray: nama jabatan
    const xArray = data.map(row => row.label);

    // yArray: jumlah
    const yArray = data.map(row => parseInt(row.jumlah));

    // total jumlah untuk persentase
    const total = yArray.reduce((a, b) => a + b, 0);

    // bikin array text berisi "value (percent%)"
    const textLabels = yArray.map(v => `${v.toLocaleString("id-ID")} (${((v / total) * 100).toFixed(1)}%)`);

    const layout = {
      title: "PNS - Jabatan",
      height: 400,
      width: 450,
      font: { size: 15, color: '#000' },
      margin: { t: 80, b: 150 }
    };

    const chartData = [{
      x: xArray,
      y: yArray,
      type: "bar",
      text: textLabels,
      textposition: "outside",
      orientation: "v",
      marker: { color: "#0077b6" }
    }];

    Plotly.newPlot("myPlot96", chartData, layout);
  })
  .catch(err => {
    console.error("Error fetching Jabatan PNS:", err);
  });


  // ======================== PNS - Kenaikan Pangkat =========================
fetch("/CiviCore/rekap_asn_merangin/ajax_get_rekap_asn.php?kategori=PNS&sub_kategori=Kenaikan Pangkat&semester=2")
  .then(response => response.json())
  .then(data => {
    // xArray: nama jabatan
    const xArray = data.map(row => row.label);

    // yArray: jumlah
    const yArray = data.map(row => parseInt(row.jumlah));

    // total jumlah untuk persentase
    const total = yArray.reduce((a, b) => a + b, 0);

    // bikin array text berisi "value (percent%)"
    const textLabels = yArray.map(v => `${v.toLocaleString("id-ID")} (${((v / total) * 100).toFixed(1)}%)`);

    const layout = {
      title: "PNS - Kenaikan Pangkat 2024",
      height: 400,
      width: 700,
      font: { size: 15, color: '#000' },
      margin: { t: 80, b: 150 }
    };

    const chartData = [{
      x: xArray,
      y: yArray,
      type: "bar",
      text: textLabels,
      textposition: "outside",
      orientation: "v",
      marker: { color: "#0077b6" }
    }];

    Plotly.newPlot("myPlot95", chartData, layout);
  })
  .catch(err => {
    console.error("Error fetching Kenaikan Pangkat PNS:", err);
  });

// ======================== End of File =========================
