<?php
/**
 * This script handles the user login process for the Sport Areal website.
 */

function authenticateUser($username, $password) {
    $file = 'Data/users.txt'; 
    $users = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES) : [];

    foreach ($users as $line) {
        list($existingEmail, $hashedPassword, $role) = explode('|', $line);
        if ($existingEmail === $username && password_verify($password, $hashedPassword)) {
            session_start();
            $_SESSION['username'] = $username; 
            $_SESSION['role'] = $role;
            return true;
        }
    }
    return false;
}

function handleLoginRequest() {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['email']; 
        $password = $_POST['password']; 

        if (authenticateUser($username, $password)) {
            header("Location: login.php?status=success");
            exit;
        } else {
            header("Location: login.php?status=error");
            exit;
        }
    } else {
        if (isset($_GET['status']) && $_GET['status'] === 'success') {
            echo <<<HTML
            <html>
            <head><title>Přihlášení úspěšné</title></head>
            <body class="login">
                <div id='popup'>
                    <h3>Přihlášení úspěšné!</h3>
                    <a href="main.php"><button>Přejít na hlavní stránku</button></a>
                </div>
            </body>
            </html>
HTML;
        } elseif (isset($_GET['status']) && $_GET['status'] === 'error') {
            echo <<<HTML
            <html>
            <head><title>Přihlášení neúspěšné</title></head>
            <body class="login">
                <div id='popup'>
                    <h3>Neplatné přihlašovací údaje</h3>
                    <a href="login.php"><button>Zkusit znovu</button></a>
                </div>
            </body>
            </html>
HTML;
        }
    }
}

handleLoginRequest();
?>

<!DOCTYPE html>
<html lang="cs" class="login">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
    <script src="script.js"></script>
</head>
<body>
    <Section id="login_section">
        <h2>Přihlášení</h2>
        <form action="login.php" method="post">
            <div>
                <label for="email">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480v58q0 59-40.5 100.5T740-280q-35 0-66-15t-52-43q-29 29-65.5 43.5T480-280q-83 0-141.5-58.5T280-480q0-83 58.5-141.5T480-680q83 0 141.5 58.5T680-480v58q0 26 17 44t43 18q26 0 43-18t17-44v-58q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93h200v80H480Zm0-280q50 0 85-35t35-85q0-50-35-85t-85-35q-50 0-85 35t-35 85q0 50 35 85t85 35Z"/></svg>
                </label>
                <input type="email" name="email" id="email" placeholder="Email" required ><span class="required">*</span>
            </div>
            <div>
                <label for="password">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M80-200v-80h800v80H80Zm46-242-52-30 34-60H40v-60h68l-34-58 52-30 34 58 34-58 52 30-34 58h68v60h-68l34 60-52 30-34-60-34 60Zm320 0-52-30 34-60h-68v-60h68l-34-58 52-30 34 58 34-58 52 30-34 58h68v60h-68l34 60-52 30-34-60-34 60Zm320 0-52-30 34-60h-68v-60h68l-34-58 52-30 34 58 34-58 52 30-34 58h68v60h-68l34 60-52 30-34-60-34 60Z"/></svg>
                </label>
                <input type="password" name="password" id="password" placeholder="Heslo" minlength="8" required><span class="required">*</span>
            </div>
            <button type="submit">Přihlásit se</button>
        </form>
        <ul>
            <li><a href="main.php">Zpět</a></li>
            <li><a href="signup.php">Registrovat se</a></li>
        </ul>
    </Section>
</body>
</html>
