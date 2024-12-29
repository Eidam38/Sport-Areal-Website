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
        session_start();
        $_SESSION['username'] = $username;
        echo <<<HTML
        <html>
        <head>
            <title>Přihlášení úspěšné</title>
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
                <h3>Byli jste úspěšně přihlášen!</h3>
                <button onclick="location.href='main.php'">Přejít na hlavní stránku</button>
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
                <h3>Chybně zadané údaje</h3>
                <button onclick="location.href='login.html'">Zkusit znovu</button>
            </div>
        </body>
        </html>
        HTML;
    }
}
?>