<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];

    $file = 'users.txt';
    $users = file_exists($file) ? unserialize(file_get_contents($file)) : [];

    if (!is_array($users)) {
        $users = [];
    }

    $login_successful = false;
    foreach ($users as $user) {
        if ($user[0] === $username && password_verify($password, $user[1])) {
            $login_successful = true;
            break;
        }
    }

    if ($login_successful) {
        echo "Přihlášení úspěšné!";
    } else {
        echo "Neplatné přihlašovací údaje.";
    }
}
?>