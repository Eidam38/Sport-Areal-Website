<?php
session_start();

$username = $_SESSION['username'];
$role = $_SESSION['role'];

$now = new DateTime();
$allReservations = file_exists('Data/reservations.txt') ? file('Data/reservations.txt', FILE_IGNORE_NEW_LINES) : [];
$futureReservations = [];

foreach ($allReservations as $line) {
    if (empty(trim($line))) continue;
    list($user, $court, $date, $time) = explode('|', $line);
    $resDateTime = new DateTime("$date $time");
    if ($resDateTime >= $now) {
        $futureReservations[] = $line;
    }
}

file_put_contents('Data/reservations.txt', implode("\n", $futureReservations) . "\n");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    $targetPath = "Data/uploads/{$username}.jpg";
    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetPath)) {
        header("Location: profile.php?status=success");
    } else {
        header("Location: profile.php?status=error");
    }
    exit;
}

$userPhoto = "Pictures/default-profile.jpg";
if (file_exists("Data/uploads/{$username}.jpg")) {
    $userPhoto = "Data/uploads/{$username}.jpg";
}

if (isset($_GET['status'])) {
    if ($_GET['status'] === 'success') {
        echo '<div class="alert success">Profilový obrázek byl úspěšně nahrán.</div>';
    } elseif ($_GET['status'] === 'error') {
        echo '<div class="alert error">Chyba při nahrávání obrázku.</div>';
    }
}

$reservations = [];
$allReservations = file_exists('Data/reservations.txt') ? file('Data/reservations.txt', FILE_IGNORE_NEW_LINES) : [];

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
        <section id="reservations">
            <h2>Vaše rezervace:</h2>
            <?php if (empty($reservations)): ?>
                <p>Nemáte žádné aktivní rezervace.</p>
            <?php else: ?>
                <?php foreach ($reservations as $reservation): ?>
                    <div class="reservation-item">
                        <span><?php echo "{$reservation['court']} dne {$reservation['date']} v {$reservation['time']}"; ?></span>
                        <form action="delete_reservation.php" method="POST" style="display: inline;">
                            <input type="hidden" name="reservation" value="<?php echo htmlspecialchars($reservation['fullLine']); ?>">
                            <button type="submit" class="delete-btn">Zrušit rezervaci</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
        <a href="main.php"><button type="submit">Zpět</button></a>
    </main>
    <footer>
        <p>&copy; 2023 Sport Areal.</p>
    </footer>
</body>
</html>