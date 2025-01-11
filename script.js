//Function to show or hide the navigation elements
document.addEventListener('DOMContentLoaded', () => {
    var menu_button = document.getElementById('menu_button');
    menu_button.addEventListener('click', function() {
        var header_nav = document.getElementById('header_nav');
        var header_buttons = document.getElementById('header_buttons');
        header_nav.classList.toggle('open');
        header_buttons.classList.toggle('open');
    });
});

// Function to validate the password field
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('signup-form');
    const button = form.querySelector('button[type="submit"]');
    button.addEventListener('click', (event) => validatePasswords(event));
});

function validatePasswords(event) {
    const password1 = document.getElementById('password').value;
    const password2 = document.getElementById('secondpassword').value;
    const message = document.getElementById('error-message');

    if (password1.length < 8 || password2.length < 8) {
        message.textContent = 'Passwords must be at least 8 characters long.';
        message.style.color = 'red';
        event.preventDefault();
    } else if (password1 !== password2) {
        message.textContent = 'Passwords do not match.';
        message.style.color = 'red';
        event.preventDefault();
    } else {
        message.textContent = '';
    }
}

// Function to validate the email field
document.addEventListener('DOMContentLoaded', () => {
    const emailInput = document.getElementById('email');

    emailInput.addEventListener('input', () => {
        const email = emailInput.value;
        validateEmail(email);
    });

    function validateEmail(email) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'validate_email.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.valid) {
                    emailInput.style.borderColor = 'green';
                } else {
                    emailInput.style.borderColor = 'red';
                }
            }
        };
        xhr.send('email=' + encodeURIComponent(email));
    }
});