let index = 0;
const slidesContainer = document.querySelector('.slides');
const images = document.querySelectorAll('.slides img');
let total = images.length;

function showSlide() {
  const width = document.querySelector('.slider').clientWidth;
  slidesContainer.style.transform = `translateX(-${index * width}px)`;
}

// Next
function nextSlide() {
  index = (index + 1) % total;
  showSlide();
}

// Prev
function prevSlide() {
  index = (index - 1 + total) % total;
  showSlide();
}

// Resize responsiveness
window.addEventListener('resize', showSlide);

// Auto play
setInterval(nextSlide, 3000);

