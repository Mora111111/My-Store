/* -------------------HEADER_SCROLL-------------------- */
const header = document.getElementById("header");

window.addEventListener("scroll", function () {
  if (this.scrollY >= 20) {
    header.style.background = "#fff";
    header.style.boxShadow = "0 0 10px rgba(0, 0, 0, 0.2)";
  } else {
    header.style.background = "transparent";
    header.style.boxShadow = "0 0 0 #d0d0d0";
  }
});

/* -------------------MENU_MOBILE SCREEN-------------------- */
let navToggle = document.getElementById("nav-toggle");
let menuClose = document.getElementById("menu-close");
let navMenu = document.getElementById("nav-menu");

navToggle.addEventListener("click", () => {
  navMenu.classList.add("show_menu");
});

menuClose.addEventListener("click", () => {
  navMenu.classList.remove("show_menu");
});