<?php
session_start();

function ensureUserIsLoggedIn() {
    if (!isset($_SESSION['username'])) {
        header("Location: main.php");
        exit();
    }
}

function getProfilePicPath($username) {
    $defaultPic = "Pictures/default-profile.jpg";
    $userPic = "Data/uploads/" . $username . ".jpg";
    return file_exists($userPic) ? $userPic : $defaultPic;
}

function handleProfilePicUpload($username) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_pic'])) {
        $targetDir = "Data/uploads/";
        $targetFile = $targetDir . $username . ".jpg";
        move_uploaded_file($_FILES['profile_pic']['tmp_name'], $targetFile);
        chmod($targetFile, 0777);
        header("Location: profile.php");
        exit();
    }
}

function handleEmailChange($username) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_email'])) {
        $newEmail = $_POST['new_email'];
        $users = file('Data/users.txt', FILE_IGNORE_NEW_LINES);
        $emailExists = false;

        foreach ($users as $user) {
            list($email, $password, $role) = explode('|', $user);
            if ($email === $newEmail) {
                $emailExists = true;
                break;
            }
        }

        if (!$emailExists) {
            $updatedUsers = array_map(function($user) use ($username, $newEmail) {
                list($email, $password, $role) = explode('|', $user);
                if ($email === $username) {
                    return "$newEmail|$password|$role";
                }
                return $user;
            }, $users);

            file_put_contents('Data/users.txt', implode(PHP_EOL, $updatedUsers) . PHP_EOL);
            $_SESSION['username'] = $newEmail;
            header("Location: profile.php");
            exit();
        } else {
            echo "<p>Email already used. Please choose another one.</p>";
        }
    }
}

function handlePasswordChange($username) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_password'])) {
        $newPassword = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $users = file('Data/users.txt', FILE_IGNORE_NEW_LINES);

        $updatedUsers = array_map(function($user) use ($username, $newPassword) {
            list($email, $password, $role) = explode('|', $user);
            if ($email === $username) {
                return "$email|$newPassword|$role";
            }
            return $user;
        }, $users);

        file_put_contents('Data/users.txt', implode(PHP_EOL, $updatedUsers) . PHP_EOL);
        header("Location: profile.php");
        exit();
    }
}

ensureUserIsLoggedIn();
$username = $_SESSION['username'];
$profilePic = getProfilePicPath($username);

handleProfilePicUpload($username);
handleEmailChange($username);
handlePasswordChange($username);

$reservations = file('Data/reservations.txt', FILE_IGNORE_NEW_LINES);
$userReservations = array_filter($reservations, function($line) use ($username) {
    return strpos($line, $username) !== false;
});

$isAdmin = false;
if ($_SESSION['role'] === 'admin') {
    $isAdmin = true;
}

if ($isAdmin) {
    $userReservations = array_filter($reservations, function($line) {
        return !empty(trim($line));
    });
}

$perPage = 5;
$totalReservations = count($userReservations);
$totalPages = ceil($totalReservations / $perPage);
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, min($totalPages, $page));
$start = ($page - 1) * $perPage;
$paginatedReservations = array_slice($userReservations, $start, $perPage);
?>

<!DOCTYPE html>
<html lang="en" class="profile">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div id="row">
    <section id="details">
        <h1><?php echo htmlspecialchars($username); ?></h1>
        <img src="<?php echo htmlspecialchars($profilePic); ?>" alt="Profile Picture" width="150" height="150">
        <form action="profile.php" method="post" enctype="multipart/form-data">
            <label for="profile_pic">Upload new profile picture:</label>
            <input type="file" name="profile_pic" id="profile_pic">
            <input type="submit" value="Upload">
        </form>
        <h2>Změnit email</h2>
        <form action="profile.php" method="post">
            <label for="new_email">New Email:</label>
            <input type="email" name="new_email" id="new_email" required>
            <input type="submit" value="Change Email">
        </form>
        <?php if (isset($emailError)): ?>
            <p style="color:red;"><?php echo htmlspecialchars($emailError); ?></p>
        <?php endif; ?>
        <h2>Změnit heslo</h2>
        <form action="profile.php" method="post">
            <label for="new_password">New Password:</label>
            <input type="password" name="new_password" id="new_password" required minlength="8">
            <input type="submit" value="Change Password">
        </form>
    </section>
    <section id="reservations">
        <h2>Reservace</h2>
        <ul>
            <?php foreach ($paginatedReservations as $reservation): ?>
                <?php list($email, $sport, $date, $time) = explode('|', $reservation); ?>
                <li>
                    <?php echo htmlspecialchars("$sport : $date : $time"); ?>
                    <form action="delete_reservation.php" method="post">
                        <input type="hidden" name="reservation" value="<?php echo htmlspecialchars($reservation); ?>">
                        <input type="submit" value="Zrušit">
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
        <div id="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=1">První /</a>
                <a href="?page=<?php echo $page - 1; ?>"><</a>
            <?php endif; ?>
            <span>Stránka <?php echo $page; ?> z <?php echo $totalPages; ?></span>
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?>">></a>
                <a href="?page=<?php echo $totalPages; ?>">/ Poslední</a>
            <?php endif; ?>
        </div>
    </section>
    </div>
    <a href="main.php">Zpět</a>
        <?php if ($isAdmin): ?>
            <a href="admin.php">Admin Page</a>
        <?php endif; ?>
</body>
</html>