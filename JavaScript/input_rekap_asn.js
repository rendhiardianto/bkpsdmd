function updateSubOptions() {
  const kategori = document.getElementById("kategori").value;
  const subContainer = document.getElementById("subOptionContainer");
  const subSelect = document.getElementById("subKategori");
  const inputForm = document.getElementById("inputForm");

  subSelect.innerHTML = '<option value="">(Pilih Sub-Kategori)</option>';
  inputForm.style.display = "none";

  if (!kategori) {
    subContainer.style.display = "none";
    return;
  }

  subContainer.style.display = "block";

  const optionsPNS = [
    "Jenis Kelamin",
    "Usia",
    "Generasi",
    "Golongan",
    "Eselon",
    "Pendidikan",
    "Jabatan",
    "Kenaikan Pangkat",
    "Jabatan Fungsional",
    "Mutasi",
    "Pensiun"
  ];

  const optionsPPPK = [
    "Jenis Kelamin",
    "Usia",
    "Generasi",
    "Pendidikan",
    "Jabatan"
  ];

  const optionsPPPKParuhWaktu = [
    "Jenis Kelamin", 
    "Usia", 
    "Pendidikan"
  ];

  let options;
  if (kategori === "PNS") options = optionsPNS;
  else if (kategori === "PPPK") options = optionsPPPK;
  else if (kategori === "PPPK Paruh Waktu") options = optionsPPPKParuhWaktu;

  options.forEach(opt => {
    const option = document.createElement("option");
    option.value = opt;
    option.textContent = opt;
    subSelect.appendChild(option);
  });
}

function showInputForm() {
  const sub = document.getElementById("subKategori").value;
  const form = document.getElementById("inputForm");
  const title = document.getElementById("formTitle");

  form.innerHTML = ""; // clear previous
  form.style.display = "none";
  if (!sub) return;

  // Forms data with exact casing
  const forms = {
    "Jenis Kelamin": ["Laki-laki", "Perempuan"],
    "Usia": ["18-20 Tahun", "21-25 Tahun", "26-30 Tahun", "31-35 Tahun", "36-40 Tahun", "41-45 Tahun", "45-50 Tahun", "51-55 Tahun", "55-60 Tahun", "Diatas 60 Tahun"],
    "Generasi": ["Generasi Baby Boomer", "Generasi X", "Generasi Y (Millennial)", "Generasi Z"],
    "Golongan": ["Gol. I", "Gol. II", "Gol. III", "Gol. IV"],
    "Eselon": ["Eselon II", "Eselon III", "Eselon IV"],
    "Pendidikan": ["SD", "SLTP", "SLTA/Sederajat", "D1", "D2", "D3", "D4", "S1", "S2", "S3"],
    "Jabatan": ["JPT", "Administrator", "Pengawas", "Pelaksana", "Fungsional"],
    "Kenaikan Pangkat": ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"],
    "Jabatan Fungsional": ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"],
    "Mutasi": ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"],
    "Pensiun": ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"]
  };

  title.textContent = "Input Data: " + sub;
  form.appendChild(title);

  if (forms[sub]) {
    forms[sub].forEach(label => {
      const lbl = document.createElement("label");
      lbl.textContent = label;

      const input = document.createElement("input");
      input.type = "number";
      input.min = "0";
      input.name = label; // preserve exact spelling
      input.placeholder = "Masukkan jumlah " + label; // preserve exact spelling
      input.required = true;

      input.addEventListener("input", calculateTotal);

      form.appendChild(lbl);
      form.appendChild(input);
    });

    const totalLabel = document.createElement("label");
    totalLabel.textContent = "Total";
    const totalInput = document.createElement("input");
    totalInput.type = "number";
    totalInput.id = "totalField";
    totalInput.name = "Total";
    totalInput.readOnly = true;
    totalInput.placeholder = "Total otomatis";
    totalInput.style.backgroundColor = "#f0f0f0";
    totalInput.style.fontWeight = "bold";

    form.appendChild(totalLabel);
    form.appendChild(totalInput);

    const btn = document.createElement("button");
    btn.type = "submit";
    btn.textContent = "Tambahkan";
    form.appendChild(btn);

    form.style.display = "block";
  }
}

function calculateTotal() {
  const form = document.getElementById("inputForm");
  const inputs = form.querySelectorAll('input[type="number"]:not(#totalField)');
  let total = 0;

  inputs.forEach(inp => {
    const val = parseFloat(inp.value);
    if (!isNaN(val)) total += val;
  });

  const totalField = document.getElementById("totalField");
  if (totalField) totalField.value = total;
}

// Add main submit button outside dynamic fields
const inputForm = document.getElementById("inputForm");
const submitBtn = document.createElement("button");
submitBtn.type = "submit";
submitBtn.textContent = "Simpan Data";
inputForm.appendChild(submitBtn);
