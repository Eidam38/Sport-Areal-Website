<?php
session_start();

$username = $_SESSION['username'];
$role = $_SESSION['role'];

$now = new DateTime(); // Current date and time
$allReservations = file_exists('Data/reservations.txt') ? file('Data/reservations.txt', FILE_IGNORE_NEW_LINES) : [];
$futureReservations = [];

// Filter future reservations
foreach ($allReservations as $line) {
    if (empty(trim($line))) continue;
    list($user, $court, $date, $time) = explode('|', $line);
    $resDateTime = new DateTime("$date $time");
    if ($resDateTime >= $now) {
        $futureReservations[] = $line;
    }
}

// Save only future reservations back to the file
file_put_contents('Data/reservations.txt', implode("\n", $futureReservations) . "\n");

// Handle profile picture upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    $targetPath = "Data/uploads/{$username}.jpg";
    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetPath)) {
        header("Location: profile.php?status=success");
    } else {
        header("Location: profile.php?status=error");
    }
    exit;
}

// Handle email and password change
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['new_email'])) {
        $newUsername = trim($_POST['new_email']);
        $allUsers = file_exists('Data/users.txt') ? file('Data/users.txt', FILE_IGNORE_NEW_LINES) : [];
        $emailExists = false;

        foreach ($allUsers as $userLine) {
            list($storedUsername, $storedPassword, $storedRole) = explode('|', $userLine);
            if ($storedUsername === $newUsername) {
                $emailExists = true;
                break;
            }
        }

        if (!$emailExists) {
            // Update email in users.txt
            $updatedUsers = [];
            foreach ($allUsers as $userLine) {
                list($storedUsername, $storedPassword, $storedRole) = explode('|', $userLine);
                if ($storedUsername === $username) {
                    $updatedUsers[] = "$newUsername|$storedPassword|$storedRole";
                } else {
                    $updatedUsers[] = $userLine;
                }
            }
            file_put_contents('Data/users.txt', implode("\n", $updatedUsers) . "\n");
            header("Location: profile.php?status=email_success");
        } else {
            header("Location: profile.php?status=email_exists");
        }
        exit;
    }

    if (isset($_POST['new_password'])) {
        $newPassword = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $allUsers = file_exists('Data/users.txt') ? file('Data/users.txt', FILE_IGNORE_NEW_LINES) : [];
        // Update password in users.txt
        $updatedUsers = [];
        foreach ($allUsers as $userLine) {
            list($storedUsername, $storedPassword, $storedRole) = explode('|', $userLine);
            if ($storedUsername === $username) {
                $updatedUsers[] = "$storedUsername|$newPassword|$storedRole";
            } else {
                $updatedUsers[] = $userLine;
            }
        }
        file_put_contents('Data/users.txt', implode("\n", $updatedUsers) . "\n");
        header("Location: profile.php?status=password_success");
        exit;
    }

    // Admin change user role
    if ($role === 'admin' && isset($_POST['admin_new_role']) && isset($_POST['admin_username'])) {
        $adminNewRole = trim($_POST['admin_new_role']);
        $adminUsername = trim($_POST['admin_username']);
        $allUsers = file_exists('Data/users.txt') ? file('Data/users.txt', FILE_IGNORE_NEW_LINES) : [];
        $updatedUsers = [];
        foreach ($allUsers as $userLine) {
            list($storedUsername, $storedPassword, $storedRole) = explode('|', $userLine);
            if ($storedUsername === $adminUsername) {
                $updatedUsers[] = "$storedUsername|$storedPassword|$adminNewRole";
            } else {
                $updatedUsers[] = $userLine;
            }
        }
        file_put_contents('Data/users.txt', implode("\n", $updatedUsers) . "\n");
        header("Location: profile.php?status=admin_role_success");
        exit;
    }
}

// Set user photo to default or uploaded picture
$userPhoto = "Pictures/default-profile.jpg";
if (file_exists("Data/uploads/{$username}.jpg")) {
    $userPhoto = "Data/uploads/{$username}.jpg";
}

// Display status messages based on upload result
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'success') {
        echo '<div class="alert success">Profilový obrázek byl úspěšně nahrán.</div>';
    } elseif ($_GET['status'] === 'error') {
        echo '<div class="alert error">Chyba při nahrávání obrázku.</div>';
    } elseif ($_GET['status'] === 'email_success') {
        echo '<div class="alert success">Email byl úspěšně změněn.</div>';
    } elseif ($_GET['status'] === 'email_exists') {
        echo '<div class="alert error">Tento email již existuje.</div>';
    } elseif ($_GET['status'] === 'password_success') {
        echo '<div class="alert success">Heslo bylo úspěšně změněno.</div>';
    } elseif ($_GET['status'] === 'admin_role_success') {
        echo '<div class="alert success">Role uživatele byla úspěšně změněna.</div>';
    }
}

$reservations = [];
$allReservations = file_exists('Data/reservations.txt') ? file('Data/reservations.txt', FILE_IGNORE_NEW_LINES) : [];

// Filter reservations based on user role
foreach ($allReservations as $line) {
    if (empty(trim($line))) continue; 
    list($user, $court, $date, $time) = explode('|', $line);
    if ($user === $username or $role === 'admin') {
        $reservations[] = [
            'court' => htmlspecialchars($court),
            'date' => htmlspecialchars($date),
            'time' => htmlspecialchars($time),
            'fullLine' => htmlspecialchars($line)
        ];
    }        
}

// Stats for paging
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 5;
$totalReservations = count($reservations);
$totalPages = ceil($totalReservations / $perPage);
$offset = ($page - 1) * $perPage;
$currentReservations = array_slice($reservations, $offset, $perPage);
?>

<!DOCTYPE html>
<html lang="cs" class="profile">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Profil uživatele: <?php echo $username; ?></h1>
    </header>
    <main>
        <section id="user_photo">
            <img src="<?php echo $userPhoto; ?>" alt="Profilová fotka" width="200" height="200">
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="file" name="profile_picture" accept="image/*">
                <button type="submit">Nahrát novou fotku</button>
            </form>
        </section>
        <section id="user_settings">
            <h2>Změna emailu a hesla</h2>
            <form action="" method="POST">
                <label for="new_email">Nový email:</label>
                <input type="email" name="new_email" id="new_email" required>
                <button type="submit">Změnit email</button>
            </form>
            <form action="" method="POST">
                <label for="new_password">Nové heslo:</label>
                <input type="password" name="new_password" id="new_password" required minlength="8">
                <button type="submit">Změnit heslo</button>
            </form>
        </section>
        <?php if ($role === 'admin'): ?>
        <section id="admin_user_list">
            <h2>Seznam uživatelů</h2>
            <?php
            $allUsers = file_exists('Data/users.txt') ? file('Data/users.txt', FILE_IGNORE_NEW_LINES) : [];
            foreach ($allUsers as $userLine) {
                list($storedUsername, $storedPassword, $storedRole) = explode('|', $userLine);
                echo "<div class='user-item'>";
                echo "<span>Uživatel: $storedUsername, Role: $storedRole</span>";
                echo "<form action='' method='POST'>";
                echo "<input type='hidden' name='admin_username' value='$storedUsername'>";
                echo "<label for='admin_new_role'>Nová role:</label>";
                echo "<select name='admin_new_role'>";
                echo "<option value='user'" . ($storedRole === 'user' ? ' selected' : '') . ">User</option>";
                echo "<option value='admin'" . ($storedRole === 'admin' ? ' selected' : '') . ">Admin</option>";
                echo "</select>";
                echo "<button type='submit'>Změnit roli</button>";
                echo "</form>";
                echo "</div>";
            }
            ?>
        </section>
        <?php endif; ?>
        <section id="reservations">
            <h2>
                <?php 
                if ($role === 'admin') {
                    echo 'Všechny rezervace';
                } else {
                    echo 'Vaše rezervace';
                }
                ?>
            </h2>
            <?php if (empty($reservations)): ?>
                <p>Nemáte žádné aktivní rezervace.</p>
            <?php else: ?>
                <!-- Display reservations with delete button -->
                <?php foreach ($currentReservations as $reservation): ?>
                    <div class="reservation-item">
                        <span><?php echo "{$reservation['court']} dne {$reservation['date']} v {$reservation['time']}"; ?></span>
                        <form action="delete_reservation.php" method="POST">
                            <input type="hidden" name="reservation" value="<?php echo htmlspecialchars($reservation['fullLine']); ?>">
                            <button type="submit" class="delete-btn">Zrušit rezervaci</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
        <!-- Paging section with links to other pages -->
        <?php if ($totalPages > 1): ?>
        <section id="paging">
            <?php if ($page > 1): ?>
            <a href="?page=1">First</a>
            <?php endif; ?>
            <?php if ($page > 1 && $page != 2): ?>
            <a href="?page=<?php echo $page - 1; ?>">Previous</a>
            <?php endif; ?>
            <span>Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>
            <?php if ($page < $totalPages && $page != $totalPages - 1): ?>
            <a href="?page=<?php echo $page + 1; ?>">Next</a>
            <?php endif; ?>
            <?php if ($page < $totalPages): ?>
            <a href="?page=<?php echo $totalPages; ?>">Last</a>
            <?php endif; ?>
        </section>
        <?php endif; ?>
        <a href="main.php"><button type="submit">Zpět</button></a>
    </main>
    <footer>
        <p>&copy; 2023 Sport Areal.</p>
    </footer>
</body>
</html>