<?php
/**
 * This script handles the user profile page for the Sport Areal website.
 */

session_start();

/**
 * Ensures the user is logged in. If not, redirects to the main page.
 *
 * @return void
 */
function ensureUserIsLoggedIn() {
    if (!isset($_SESSION['username'])) {
        header("Location: main.php");
        exit();
    }
}

/**
 * Returns the path to the profile picture of a user.
 *
 * @param string $username The username of the user.
 * @return string The path to the profile picture.
 */
function getProfilePicPath($username) {
    $defaultPic = "Picture/default-profile.jpg";
    $userPic = "Data/uploads/" . $username . ".jpg";
    return file_exists($userPic) ? $userPic : $defaultPic;
}

/**
 * Handles the profile picture upload request from a POST form submission.
 *
 * @param string $username The username of the user.
 * @return void
 */
function handleProfilePicUpload($username) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_pic'])) {
        $targetDir = "Data/uploads/";
        $targetFile = $targetDir . $username . ".jpg";
        $imageFileType = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));

        if ($imageFileType != "jpg" && $imageFileType != "jpeg") {
            header("Location: profile.php?invalid_image_type");
            exit();
        }

        $imageData = file_get_contents($_FILES['profile_pic']['tmp_name']);
        $image = imagecreatefromstring($imageData);

        $newWidth = 200;
        $newHeight = 200;
        $imageResized = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($imageResized, $image, 0, 0, 0, 0, $newWidth, $newHeight, imagesx($image), imagesy($image));
        imagejpeg($imageResized, $targetFile, 100);

        chmod($targetFile, 0777);
        header("Location: profile.php?pic_uploaded");
        exit();
    }
}

/**
 * Handles the email change request from a POST form submission.
 *
 * @param string $username The username of the user.
 * @param string $newEmail The new email to assign to the user.
 * @param string $users The array of users.
 * @param string $emailExists A flag indicating if the new email already exists.
 * @return void
 */
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
            header("Location: profile.php?email_exists");
        }
    }
}

/**
 * Handles the password change request from a POST form submission.
 *
 * @param string $username The username of the user.
 * @param string $newPassword The new password to assign to the user.
 * @param string $users The array of users.
 * @return void
 */
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
        header("Location: profile.php?password_changed");
        exit();
    }
}

ensureUserIsLoggedIn();
$username = $_SESSION['username'];
$profilePic = getProfilePicPath($username);

handleProfilePicUpload($username);
handleEmailChange($username);
handlePasswordChange($username);

if (isset($_GET['invalid_image_type'])) {
    echo "<p>špatný typ obrázku, použíte jpg</p>";
}
elseif (isset($_GET['pic_uploaded'])) {
    echo "<p>obrázek byl nahrán</p>";
}
elseif (isset($_GET['email_exists'])) {
    $emailError = "Email již existuje";
}
elseif (isset($_GET['email_changed'])) {
    echo "<p>Email byl změněn</p>";
}
elseif (isset($_GET['password_changed'])) {
    echo "<p>Heslo bylo změněno</p>";
}

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
        <img src="<?php echo htmlspecialchars($profilePic); ?>" alt="Profile Picture" width="200" height="200">
        <form action="profile.php" method="post" enctype="multipart/form-data">
            <label for="profile_pic">Nahraj novou fotku:</label>
            <input type="file" name="profile_pic" id="profile_pic">
            <input type="submit" value="Nahrát">
        </form>
        <h2>Změnit email</h2>
        <form action="profile.php" method="post">
            <label for="new_email">Nový email:</label>
            <input type="email" name="new_email" id="new_email" required>
            <input type="submit" value="Změnit email">
        </form>
        <?php if (isset($emailError)): ?>
            <p style="color:red;"><?php echo htmlspecialchars($emailError); ?></p>
        <?php endif; ?>
        <h2>Změnit heslo</h2>
        <form action="profile.php" method="post">
            <label for="new_password">Nové heslo:</label>
            <input type="password" name="new_password" id="new_password" required minlength="8">
            <input type="submit" value="Změnit heslo">
        </form>
    </section>
    <section id="reservations">
        <h2>Rezervace</h2>
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
            <a href="admin.php">Admin</a>
        <?php endif; ?>
</body>
</html>