function validateForm() {
  const password = document.getElementById("password").value;
  const confirmPassword = document.getElementById("confirm_password").value;
  if (password !== confirmPassword) {
    document.getElementById("passNoMatch").innerHTML =
      "<br/>Passwords do not match.<br/>";
    return false;
  }
  return true;
}
