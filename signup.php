<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $file = 'users.txt';
    $emailExists = false;
    if (file_exists($file)) {
        $users = file($file, FILE_IGNORE_NEW_LINES);
        foreach ($users as $line) {
            list($existingEmail) = explode('|', $line);
            if ($existingEmail === $username) {
                $emailExists = true;
                break;
            }
        }
    }

    if ($emailExists) {
        echo <<<HTML
        <html>
        <head>
            <title>Registrace neuspěšná</title>
            <style>
                :root{
                    --primary-color: #FFFFFF;
                    --input-color: #cccaca;
                    --secondary-color: #2C2C2C;
                    }
                @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
                body{
                    margin: 0;
                    padding: 0;
                    font-family: 'Poppins', sans-serif;
                    background-color: var(--primary-color);
                }
                #popup {
                    position: fixed;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    border: 5px solid var(--secondary-color);
                    border-radius: 25px;
                    padding: 50px;
                    text-align: center;
                    color: var(--secondary-color);
                    background-color: #fff;
                }
                button{
                    background-color: var(--secondary-color);
                    color: var(--primary-color);
                    padding: 10px 20px;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                    margin-top: 10px;
                }
            </style>
        </head>
        <body>
            <div id='popup'>
                <h3>Email již už existuje</h3>
                <button onclick="location.href='signup.php'">Zkusit znovu</button>
            </div>
        </body>
        </html>
        HTML;
    } else {
        $userLine = $username . '|' . $password . '|user' . PHP_EOL;
        file_put_contents($file, $userLine, FILE_APPEND);
        session_start();
        $_SESSION['username'] = $username;
        echo <<<HTML
        <html>
        <head>
            <title>Registrace úspěšná</title>
            <style>
                :root{
                    --primary-color: #FFFFFF;
                    --input-color: #cccaca;
                    --secondary-color: #2C2C2C;
                    }
                @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
                body{
                    margin: 0;
                    padding: 0;
                    font-family: 'Poppins', sans-serif;
                    background-color: var(--primary-color);
                }
                #popup {
                    position: fixed;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    border: 5px solid var(--secondary-color);
                    border-radius: 25px;
                    padding: 50px;
                    text-align: center;
                    color: var(--secondary-color);
                    background-color: #fff;
                }
                button{
                    background-color: var(--secondary-color);
                    color: var(--primary-color);
                    padding: 10px 20px;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                    margin-top: 10px;
                }
            </style>
        </head>
        <body>
            <div id='popup'>
                <h3>Byli jste úspěšně zaregistrováni!</h3>
                <button onclick="location.href='main.php'">Přejít na hlavní stránku</button>
            </div>
        </body>
        </html>
        HTML;
    }
}
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
        <form action="signup.php" method="post" onsubmit="return validatePasswords()">
            <div>
                <label for="email">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480v58q0 59-40.5 100.5T740-280q-35 0-66-15t-52-43q-29 29-65.5 43.5T480-280q-83 0-141.5-58.5T280-480q0-83 58.5-141.5T480-680q83 0 141.5 58.5T680-480v58q0 26 17 44t43 18q26 0 43-18t17-44v-58q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93h200v80H480Zm0-280q50 0 85-35t35-85q0-50-35-85t-85-35q-50 0-85 35t-35 85q0 50 35 85t85 35Z"/></svg>
                </label>
                <input type="email" name="email" id="email" placeholder="Email" required>
            </div>
            <div>
                <label for="password">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M80-200v-80h800v80H80Zm46-242-52-30 34-60H40v-60h68l-34-58 52-30 34 58 34-58 52 30-34 58h68v60h-68l34 60-52 30-34-60-34 60Zm320 0-52-30 34-60h-68v-60h68l-34-58 52-30 34 58 34-58 52 30-34 58h68v60h-68l34 60-52 30-34-60-34 60Zm320 0-52-30 34-60h-68v-60h68l-34-58 52-30 34 58 34-58 52 30-34 58h68v60h-68l34 60-52 30-34-60-34 60Z"/></svg>
                </label>
                <input type="password" name="password" id="password" placeholder="Heslo" minlength="8" required>
            </div>
            <div>
                <label for="secondpassword">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M80-200v-80h800v80H80Zm46-242-52-30 34-60H40v-60h68l-34-58 52-30 34 58 34-58 52 30-34 58h68v60h-68l34 60-52 30-34-60-34 60Zm320 0-52-30 34-60h-68v-60h68l-34-58 52-30 34 58 34-58 52 30-34 58h68v60h-68l34 60-52 30-34-60-34 60Zm320 0-52-30 34-60h-68v-60h68l-34-58 52-30 34 58 34-58 52 30-34 58h68v60h-68l34 60-52 30-34-60-34 60Z"/></svg>
                </label>
                <input type="password" name="secondpassword" id="secondpassword" placeholder="Heslo" minlength="8" required>
            </div>
            <div id="error-message" style="color: red; display: none;"></div>
            <button type="submit">Registrovat se</button>
        </form>
        <ul>
            <li><a href="main.php"><button type="submit">Zpět</button></a></li>
            <li><a href="login.php"><button type="submit">Přihlásit se</button></a></li>
        </ul>
    </Section>
</body>
</html>