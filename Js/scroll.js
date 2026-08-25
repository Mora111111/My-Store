const header = document.getElementById("header");
window.addEventListener("scroll", function () {
  if (!header) return;
  if (this.scrollY >= 20) {
    header.style.background = "#fff";
    header.style.boxShadow = "0 4px 15px rgba(0,0,0,0.05)";
  } else {
    header.style.background = "transparent";
    header.style.boxShadow = "none";
  }
});