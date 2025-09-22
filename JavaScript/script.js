// ---------- NAV ----------
function toggleNav() {
  document.getElementById("mynavBtn").classList.toggle("responsive");
}

// ---------- START LOGO ----------
function toggleStartMenu() {
  document.getElementById("myStart").classList.toggle("show");
}

// ---------- CLICK OUTSIDE ----------
window.addEventListener("click", e => {
  // Close start menu
  if (!e.target.closest(".startlogoDD")) {
    document.getElementById("myStart").classList.remove("show");
  }
  // Close dropdown-content
  if (!e.target.closest(".dropdown")) {
    document.querySelectorAll(".dropdown-content").forEach(menu => {
      menu.classList.remove("show");
    });
  }
});

// ---------- DROPDOWN BUTTONS ----------
document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".dropbtn").forEach(btn => {
    btn.addEventListener("click", e => {
      e.preventDefault();
      const dropdown = btn.nextElementSibling; // assumes dropdown-content is next
      // close others first
      document.querySelectorAll(".dropdown-content").forEach(menu => {
        if (menu !== dropdown) menu.classList.remove("show");
      });
      // toggle this one
      dropdown.classList.toggle("show");
    });
  });
});

// INFOGRAPHIS SLIDESHOW----------------------------------------------
var myIndex = 0;
carousel();
function carousel() {
    var i;
    var x = document.getElementsByClassName("mySlides");
    for (i = 0; i < x.length; i++) {
       x[i].style.display = "none";  
    }
    myIndex++;
    if (myIndex > x.length) {myIndex = 1}    
    x[myIndex-1].style.display = "block";  
    setTimeout(carousel, 3000);
}