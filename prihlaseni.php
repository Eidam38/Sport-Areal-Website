<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $file = 'users.txt';
    if (file_exists($file)) {
        $f = fopen($file, 'r');
        $users = fread($f, filesize($file));
        fclose($f);
        $users = $users ? unserialize($users) : [];
    } else {
        $users = [];
    }

    $success = false;
    foreach ($users as $user) {
        if ($user[0] === $username and password_verify($_POST['password'], $user[1])) {
            $success = true;
            break;
        }
    }

    if ($success) {
        echo <<<HTML
        <html>
        <head>
            <title>Přihlášení úspěšné</title>
            <style>
                #popup {
                    position: fixed;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    border: 1px solid #ccc;
                    padding: 20px;
                    text-align: center;
                    background-color: #fff;
                }
                body {
                    background-color: rgba(255, 255, 255, 0.5);
                    margin: 0;
                }
            </style>
        </head>
        <body>
            <div id='popup'>
                <h3>Byli jste úspěšně přihlášen!</h3>
                <button onclick="location.href='main.html'">Přejít na hlavní stránku</button>
            </div>
        </body>
        </html>
        HTML;
    } else {
        echo <<<HTML
        <html>
        <head>
            <title>Přihlášení neuspěšné</title>
            <style>
                #popup {
                    position: fixed;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    border: 1px solid #ccc;
                    padding: 20px;
                    text-align: center;
                    background-color: #fff;
                }
                body {
                    background-color: rgba(0, 0, 0, 0.5);
                    margin: 0;
                }
            </style>
        </head>
        <body>
            <div id='popup'>
                <h3>Chybně zadané údaje</h3>
                <button onclick="location.href='login.html'">Zkusit znovu</button>
            </div>
        </body>
        </html>
        HTML;
    }
}
?>