const showIcons = document.querySelectorAll(".showPss");

showIcons.forEach((icon) => {
  icon.addEventListener("click", (e) => {
    const targetIcon = e.target;
    const passwordInput = targetIcon.previousElementSibling;

    if (passwordInput.type === "password") {
      passwordInput.type = "text";
      targetIcon.classList.replace("fa-eye-slash", "fa-eye");
    } else {
      passwordInput.type = "password";
      targetIcon.classList.replace("fa-eye", "fa-eye-slash");
    }
  });
});