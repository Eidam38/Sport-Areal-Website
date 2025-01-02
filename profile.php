<?php
session_start();

$username = $_SESSION['username'];
$role = $_SESSION['role'];
$userPhoto = "images/default-profile.jpg";
$reservations = [];

if (file_exists(__DIR__ . '/Data/reservations.txt')) {
    $allReservations = file(__DIR__ . '/Data/reservations.txt', FILE_IGNORE_NEW_LINES);
    foreach ($allReservations as $line) {
        if (empty(trim($line))) continue;
        
        $parts = explode('|', $line);

        list($user, $court, $date, $time) = $parts;
            
        if ($user === $username or $role === 'admin') {
            $reservations[] = [
                'court' => htmlspecialchars($court),
                'date' => htmlspecialchars($date),
                'time' => htmlspecialchars($time),
                'fullLine' => htmlspecialchars($line)
            ];
        }        
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
            <img src="<?php echo $userPhoto; ?>" alt="Profilová fotka" id="profile_photo">
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