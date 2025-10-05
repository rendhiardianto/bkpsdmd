function myFunction() {
  const input = document.getElementById("myInput").value.toUpperCase();
  const rows = document.querySelectorAll("#userTable tbody tr");

  rows.forEach(row => {
    // ambil semua kolom (td)
    const cols = row.getElementsByTagName("td");
    let found = false;

    // cek setiap kolom (id, jabatan, pembina)
    for (let i = 0; i < cols.length; i++) {
      if (cols[i].textContent.toUpperCase().includes(input)) {
        found = true;
        break;
      }
    }

    row.style.display = found ? "" : "none";
  });
}
