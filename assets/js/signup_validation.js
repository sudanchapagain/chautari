function validateForm() {
  const name = document.getElementById("name").value.trim();
  const email = document.getElementById("email").value.trim();
  const password = document.getElementById("password").value.trim();
  const confirmPassword = document
    .getElementById("confirm_password")
    .value.trim();

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  const passwordRegex =
    /^(?=.*[!@#$%^&*(),.?":{}|<>])(?=.*\d)(?=.*[a-z])(?=.*[A-Z])/;

  let valid = true;

  clearErrors();

  if (name.length === 0) {
    showError("nameError", "Name is required.");
    valid = false;
  }

  if (name.length <= 2) {
    showError("nameError", "Name must be longer than 2 characters.");
    valid = false;
  }

  if (!emailRegex.test(email)) {
    showError("emailError", "Invalid email format.");
    valid = false;
  }

  if (password.length < 8) {
    showError("passwordError", "Password must be at least 8 characters long.");
    valid = false;
  } else if (!passwordRegex.test(password)) {
    showError(
      "passwordError",
      "Password must contain at least one special character, one digit, one uppercase letter, and one lowercase letter."
    );
    valid = false;
  }

  if (password !== confirmPassword) {
    showError("passNoMatch", "Passwords do not match.");
    valid = false;
  }

  return valid;
}

function clearErrors() {
  document.getElementById("nameError").textContent = "";
  document.getElementById("emailError").textContent = "";
  document.getElementById("passwordError").textContent = "";
  document.getElementById("confirmPasswordError").textContent = "";
  document.getElementById("passNoMatch").textContent = "";
}

function showError(elementId, message) {
  document.getElementById(elementId).textContent = message;
}
