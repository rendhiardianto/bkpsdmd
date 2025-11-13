const modal = document.getElementById('imageModal');
const modalImg = document.getElementById('modalImage');
const downloadBtn = document.querySelector('.downloadBtn');

// --- Show modal when "Lihat" clicked ---
document.querySelectorAll('.detailBtn').forEach(btn => {
  btn.addEventListener('click', () => {
    const imgSrc = btn.getAttribute('data-img');
    if (imgSrc && imgSrc.trim() !== '') {
      modalImg.src = imgSrc;
      modalImg.removeAttribute('data-fallback');
      downloadBtn.style.display = 'inline-block'; // show by default
      modal.style.display = 'flex';
      setTimeout(() => modal.classList.add('show'), 10);
    } else {
      alert('Tidak ada gambar tersedia untuk item ini.');
    }
  });
});

// --- Close modal ---
function closeModal() {
  modal.classList.remove('show');
  setTimeout(() => modal.style.display = 'none', 200);
}

// --- Close when clicking outside the image ---
modal.addEventListener('click', (e) => {
  // Close only if user clicks directly on the dark overlay (not the content)
  if (e.target === modal) {
    closeModal();
  }
});

// --- ESC key to close ---
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') closeModal();
});

// --- Fallback image if original fails to load ---
modalImg.onerror = () => {
  modalImg.src = 'CiviCore/pojokjafung/uploads/detail_image/default.jpg';
  modalImg.setAttribute('data-fallback', 'true');
  downloadBtn.style.display = 'none'; // hide download button
};

// --- Download function ---
function downloadImage() {
  const imgSrc = modalImg.src;
  if (!imgSrc) {
    alert("Tidak ada gambar untuk diunduh.");
    return;
  }

  const link = document.createElement('a');
  link.href = imgSrc;
  link.download = imgSrc.split('/').pop();
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}
