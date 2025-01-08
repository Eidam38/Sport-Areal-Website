// The waitForPageLoad function ensures that the callback is executed only after the page has fully loaded.
function waitForPageLoad(callback) {
    if (document.readyState === 'complete') {
        callback();
    } else {
        window.addEventListener('load', callback);
    }
}

waitForPageLoad(function() {
    var menu_button = document.getElementById('menu_button');
    menu_button.addEventListener('click', function() {
        var header_nav = document.getElementById('header_nav');
        var header_buttons = document.getElementById('header_buttons');
        // Toggle the 'open' class to show or hide navigation elements
        header_nav.classList.toggle('open');
        header_buttons.classList.toggle('open');
    });
});

// Function to validate the email field
function checkEmail(value) {
    var xhr = new XMLHttpRequest();
    // Initialize a POST request to the login.php script
    xhr.open("POST", "login.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var emailField = document.getElementById("email");
            // Check the response to determine if the email is invalid
            if (xhr.responseText === "invalid") {
                emailField.style.borderColor = "red";
            } else {
                emailField.style.borderColor = "";
            }
        }
    };
    xhr.send("email=" + encodeURIComponent(value));
}

// Function to validate the password field
function validatePasswords() {
    var passwordField = document.getElementById("password");
    var secondPasswordField = document.getElementById("secondpassword");
    var password = passwordField.value;
    var secondpassword = secondPasswordField.value;
    var errorMessage = document.getElementById("error-message");
    if (password !== secondpassword) {
        passwordField.style.borderColor = "red";
        secondPasswordField.style.borderColor = "red";
        errorMessage.textContent = "Hesla se neshodují!";
        errorMessage.style.display = "block";
        return false;
    } else if (password.length < 8) {
        passwordField.style.borderColor = "red";
        secondPasswordField.style.borderColor = "red";
        errorMessage.textContent = "Hesla musí být minimálně 8 znaků!";
        errorMessage.style.display = "block";
        return false;
    }
    errorMessage.style.display = "none";
    return true;
}