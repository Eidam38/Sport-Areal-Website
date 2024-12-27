<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $file = 'users.txt';
    $users = file_exists($file) ? unserialize(file_get_contents($file)) : [];

    if (!is_array($users)) {
        $users = [];
    }

    $emailExists = false;
    foreach ($users as $user) {
        if ($user[0] === $username) {
            $emailExists = true;
            break;
        }
    }

    if ($emailExists) {
        echo "Účet s tímto emailem již existuje!";
    } else {
        $users[] = [$username, $password];
        file_put_contents($file, serialize($users), LOCK_EX);
        echo "Registrace úspěšná!";
    }
}
?>