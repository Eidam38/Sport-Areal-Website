<?php

/**
 * This script handles the user signup process for the Sport Areal website.
 */

/**
 * Checks if a user with the given username already exists.
 * 
 * @param array $users The array of users.
 * @param string $username The username to check.
 * @return bool True if the user exists, false otherwise.
 */
function userExists($users, $username) {
    foreach ($users as $line) {
        list($existingEmail) = explode('|', $line);
        if ($existingEmail === $username) {
            return true;
        }
    }
    return false;
}

/**
 * Adds a new user to the users.txt file.
 * 
 * @param string $file The path to the users file.
 * @param string $username The username of the user.
 * @param string $password The password of the user.
 * @param string $role The role of the user.
 * @return void
 */
function addUser($file, $username, $password, $role = 'user') {
    $userLine = $username . '|' . $password . '|' . $role . PHP_EOL;
    file_put_contents($file, $userLine, FILE_APPEND);
}

/**
 * Handles the signup request from a POST form submission.
 * 
 * @param string $username The username (email) of the user.
 * @param string $password The password of the user.
 * @return void
 */
function handleSignupRequest() {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $file = 'Data/users.txt';
        $users = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES) : [];

        if (userExists($users, $username)) {
            header("Location: signup.php?status=exists");
            exit;
        } else {
            addUser($file, $username, $password);
            session_start();
            $_SESSION['username'] = $username;
            $_SESSION['role'] = 'user';
            header("Location: signup.php?status=success");
            exit;
        }
    } else {
        if (isset($_GET['status']) && $_GET['status'] === 'exists') {
            echo <<<HTML
            <html>
            <head><title>Registrace neuspěšná</title></head>
            <body class="signup">
                <div id='popup'>
                    <h3>Email již existuje</h3>
                    <a href="signup.php"><button>Zkusit znovu</button></a>
                </div>
            </body>
            </html>
HTML;
        } elseif (isset($_GET['status']) && $_GET['status'] === 'success') {
            echo <<<HTML
            <html>
            <head><title>Registrace úspěšná</title></head>
            <body class="signup">
                <div id='popup'>
                    <h3>Byli jste úspěšně zaregistrováni!</h3>
                    <a href="main.php"><button>Přejít na hlavní stránku</button></a>
                </div>
            </body>
            </html>
HTML;
        }
    }
}

handleSignupRequest();
?>

<!DOCTYPE html>
<html lang="cs" class="signup">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link rel="stylesheet" href="styles.css">
    <script src="script.js"></script>
</head>
<body>
    <Section>
        <h2>Registrace</h2>
        <form action="signup.php" method="post" id="signup-form">
            <div>
                <label for="email">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480v58q0 59-40.5 100.5T740-280q-35 0-66-15t-52-43q-29 29-65.5 43.5T480-280q-83 0-141.5-58.5T280-480q0-83 58.5-141.5T480-680q83 0 141.5 58.5T680-480v58q0 26 17 44t43 18q26 0 43-18t17-44v-58q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93h200v80H480Zm0-280q50 0 85-35t35-85q0-50-35-85t-85-35q-50 0-85 35t-35 85q0 50 35 85t85 35Z"/></svg>
                </label>
                <input type="email" name="email" id="email" placeholder="Email" required><span class="required">*</span>
            </div>
            <div>
                <label for="password">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M80-200v-80h800v80H80Zm46-242-52-30 34-60H40v-60h68l-34-58 52-30 34 58 34-58 52 30-34 58h68v60h-68l34 60-52 30-34-60-34 60Zm320 0-52-30 34-60h-68v-60h68l-34-58 52-30 34 58 34-58 52 30-34 58h68v60h-68l34 60-52 30-34-60-34 60Zm320 0-52-30 34-60h-68v-60h68l-34-58 52-30 34 58 34-58 52 30-34 58h68v60h-68l34 60-52 30-34-60-34 60Z"/></svg>
                </label>
                <input type="password" name="password" id="password" placeholder="Heslo" required><span class="required">*</span>
            </div>
            <div>
                <label for="secondpassword">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M80-200v-80h800v80H80Zm46-242-52-30 34-60H40v-60h68l-34-58 52-30 34 58 34-58 52 30-34 58h68v60h-68l34 60-52 30-34-60-34 60Zm320 0-52-30 34-60h-68v-60h68l-34-58 52-30 34 58 34-58 52 30-34 58h68v60h-68l34 60-52 30-34-60-34 60Zm320 0-52-30 34-60h-68v-60h68l-34-58 52-30 34 58 34-58 52 30-34 58h68v60h-68l34 60-52 30-34-60-34 60Z"/></svg>
                </label>
                <input type="password" name="secondpassword" id="secondpassword" placeholder="Heslo" required><span class="required">*</span>
            </div>
            <div id="error-message" ></div>
            <button type="submit">Registrovat se</button>
        </form>
        <ul>
            <li><a href="main.php">Zpět</a></li>
            <li><a href="login.php">Přihlásit se</a></li>
        </ul>
    </Section>
</body>
</html>