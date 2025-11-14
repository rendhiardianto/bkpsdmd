// When the user scrolls down 80px from the top of the document, resize the navbar's padding and the logo's font size
window.addEventListener("scroll", function() {
  scrollFunction();
  scrollTopFunction();
});

function scrollFunction() {
  const navbar = document.getElementById("navbar");
  const logo = document.getElementById("logo");
  const icon = document.getElementById("icon");

  // Skip shrink behavior if on mobile
  if (window.innerWidth <= 768) return;

  if (document.body.scrollTop > 80 || document.documentElement.scrollTop > 80) {
    navbar.style.padding = "10px 0px";
    logo.style.fontSize = "17px";
    icon.style.width = "80px";
  } else {
    navbar.style.padding = "50px 0px";
    logo.style.fontSize = "25px";
    icon.style.width = "150px";
  }
}

// ✅ Toggle menu visibility on mobile
function toggleMenu() {
    const menu = document.getElementById("navbar-right");
    const toggle = document.getElementById("menu-toggle");

    toggle.classList.toggle("active");

    if (menu.classList.contains("show")) {
        menu.classList.remove("show");
        menu.style.maxHeight = "0px";
    } else {
        menu.classList.add("show");
        menu.style.maxHeight = menu.scrollHeight + "px"; 
    }
}


// ✅ Close menu automatically when clicking a link (on mobile)
document.addEventListener("DOMContentLoaded", () => {
  const menu = document.getElementById("navbar-right");
  const toggle = document.getElementById("menu-toggle");
  const links = menu.querySelectorAll("a");

  links.forEach(link => {
    link.addEventListener("click", () => {
      if (window.innerWidth <= 768 && menu.classList.contains("show")) {
        menu.classList.remove("show");
        toggle.classList.remove("active");
      }
    });
  });
});

document.addEventListener("click", (event) => {
  const menu = document.getElementById("navbar-right");
  const toggle = document.getElementById("menu-toggle");

  if (window.innerWidth <= 768) {
    const isClickInside = menu.contains(event.target) || toggle.contains(event.target);
    if (!isClickInside && menu.classList.contains("show")) {
      menu.classList.remove("show");
      toggle.classList.remove("active");
    }
  }
});

// When the user scrolls down 100px from the top, show the button
window.onscroll = function() { scrollTopFunction(); };

function scrollTopFunction() {
  const mybutton = document.getElementById("myBtn");
  if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
    mybutton.style.display = "block";
  } else {
    mybutton.style.display = "none";
  }
}

function topFunction() {
  window.scrollTo({ top: 0, behavior: "smooth" });
}