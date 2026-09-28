const loginForm = document.getElementById("login-form");

loginForm.addEventListener("submit", async function(event) {

    event.preventDefault();

    const email = document.querySelector('input[name="email"]').value;
    const password = document.querySelector('input[name="password"]').value;

    const response = await fetch("login.php", {
        method: "POST",
        body: new URLSearchParams({
            email: email,
            password: password
        })
    });

    const result = await response.text();

    if (result === "Incorrect password.") {

        alert("Incorrect password.");

    } else if (result === "User not found.") {

        alert("User not found.");

    } else if (result === "Login successful.") {

        window.location.href = "index.php";

    }

});