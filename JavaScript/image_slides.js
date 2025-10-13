  let slideIndex = 1;
  let slideTimer;

  function showSlides(n) {
    let slides = document.getElementsByClassName("slides");
    let dots = document.getElementsByClassName("dot");
    let thumbs = document.querySelectorAll(".thumbnail-row img");

    if (n > slides.length) { slideIndex = 1 }
    if (n < 1) { slideIndex = slides.length }

    for (let i = 0; i < slides.length; i++) {
      slides[i].style.display = "none";
    }

    for (let i = 0; i < dots.length; i++) {
      dots[i].className = dots[i].className.replace(" active", "");
    }

    thumbs.forEach(img => img.classList.remove("active-thumb"));

    slides[slideIndex-1].style.display = "block";
    dots[slideIndex-1].className += " active";
    thumbs[slideIndex-1].classList.add("active-thumb");
  }

  function plusSlides(n) {
    clearInterval(slideTimer);
    slideIndex += n;
    showSlides(slideIndex);
    autoSlide();
  }

  function currentSlide(n) {
    clearInterval(slideTimer);
    slideIndex = n;
    showSlides(slideIndex);
    autoSlide();
  }

  function autoSlide() {
    slideTimer = setInterval(() => {
      slideIndex++;
      if (slideIndex > document.getElementsByClassName("slides").length) { slideIndex = 1 }
      showSlides(slideIndex);
    }, 4000); // 4 seconds
  }

  // Initialize
  showSlides(slideIndex);
  autoSlide();