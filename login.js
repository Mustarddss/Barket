document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");
    const emailInput = document.querySelector("input[type='text']");
    const passwordInput = document.querySelector("input[type='password']");

    // Create error elements
    const emailError = document.createElement("div");
    emailError.className = "error-message";

    const passwordError = document.createElement("div");
    passwordError.className = "error-message";

    form.addEventListener("submit", function (e) {
        e.preventDefault();

        let valid = true;
        emailError.textContent = "";
        passwordError.textContent = "";
        emailInput.classList.remove("error");
        passwordInput.classList.remove("error");

        const emailValue = emailInput.value.trim();
        const passwordValue = passwordInput.value;

        const emailPattern = /^[a-zA-Z0-9._%+-]+@students\.nu-dasma\.edu\.ph$/;

        if (!emailPattern.test(emailValue)) {
            emailError.textContent = "Use your School Email.";
            if (!emailInput.nextElementSibling || !emailInput.nextElementSibling.classList.contains("error-message")) {
                emailInput.parentElement.appendChild(emailError);
            }
            emailInput.classList.add("error");
            valid = false;
        }

        if (passwordValue.length < 6) {
            passwordError.textContent = "Password must be at least 6 characters.";
            if (!passwordInput.nextElementSibling || !passwordInput.nextElementSibling.classList.contains("error-message")) {
                passwordInput.parentElement.appendChild(passwordError);
            }
            passwordInput.classList.add("error");
            valid = false;
        }

        if (valid) {
            window.location.href = "landingpage.html"; // Replace with your actual landing page
        }
    });
});
