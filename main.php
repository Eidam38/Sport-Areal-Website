<?php
/**
 * This script handles the reservation times and displays the main HTML structure of the Sport Areal website.
 * 
 * @author Adam Šilhavík
 */

session_start();

$allTimes = ['12:00','13:00','14:00','15:00','16:00','17:00'];
$court = null;
$date = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['court'], $_POST['date'])) {
    $court = $_POST['court'];
    $date = $_POST['date'];
    header("Location: main.php?court=" . urlencode($court) . "&date=" . urlencode($date));
    exit;
}

/**
 * Loads reservations from a file.
 *
 * @param string $file The path to the reservations file.
 * @return array The list of reservations.
 */
function loadReservations($file) {
    if (!file_exists($file)) {
        return [];
    }
    return file($file, FILE_IGNORE_NEW_LINES);
}


/**
 * Filters reserved times for a specific court and date.
 *
 * @param array $reservations The list of reservations.
 * @param string $court The court to filter reservations for.
 * @param string $date The date to filter reservations for.
 * @return array The list of reserved times.
 */
function filterReservedTimes($reservations, $court, $date) {
    $reservedTimes = [];
    foreach ($reservations as $line) {
        if (empty($line)) continue;
        list($user, $resCourt, $resDate, $resTime) = explode('|', $line);
        if ($resCourt === $court && $resDate === $date) {
            $reservedTimes[] = $resTime;
        }
    }
    return $reservedTimes;
}

if (isset($_GET['court']) && isset($_GET['date'])) {
    $court = $_GET['court'];
    $date = $_GET['date'];
    $file = 'Data/reservations.txt';
    $reservations = loadReservations($file);
    $reservedTimes = filterReservedTimes($reservations, $court, $date);
}
?>

<!DOCTYPE html>
<html lang="cs" class="main">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sport Areal</title>
    <link rel="stylesheet" href="styles.css">
    <script src="script.js"></script>
</head>
<body>
    <header>
        <img src="Picture/SportArealLogo.png" alt="Sport Areal Logo">
        <nav id="header_nav">
            <a href="#about" id="nav_about">O nás</a>
            <a href="#gallery">Galerie</a>
            <a href="#price">Ceník</a>
            <a href="#reservation">Rezervace</a>
            <a href="#contacts">Kontakty</a>
        </nav>
        <div id="menu_button">
            <div class="line"></div>
            <div class="line"></div>
            <div class="line"></div>
        </div>
        <ul id="header_buttons">
            <?php if(isset($_SESSION['username'])):?>
                    <li><a href="profile.php"><button id="login"><?php echo $_SESSION['username']?></button></a></li>
                    <li><form method="post" action="logout.php"><button type="submit" id="signup">Odhlásit se</button></form></li>
            <?php else : ?>
                    <li><a id="login" href="login.php">Přihlásit se</a></li>
                    <li><a id="signup" href="signup.php">Zaregistrovat se</a></li>
            <?php endif; ?>
        </ul>
    </header>
    <main>
        <section id="hero">
            <h1 class="white">Trénuj naplno, žij naplno</h1>
            <h5 class="white">Sportovní areál pro každého</h5>
        </section>

        <section id="about">
            <div id="about_text_1">
                <h3>O nás</h3>
                <p>Vítejte v našem sportovním areálu, kde se sport setkává s vášní a odhodláním! 
                    Jsme moderní sportovní centrum, které nabízí širokou škálu sportovních aktivit pro všechny věkové kategorie a úrovně schopností. 
                    Ať už jste začátečník hledající nové výzvy, nebo zkušený sportovec toužící po zdokonalení svých dovedností, u nás najdete to pravé.</p>
                    <p>Naším cílem je poskytovat profesionální zázemí a vytvářet motivující prostředí, ve kterém můžete rozvíjet své schopnosti, užívat si pohyb a posouvat své limity</p>
            </div>
            <div id="about_text_2">
                <h3>Proč právě my?</h3>
                <p><b>Moderní sportoviště:</b>Od fitness centra přes multifunkční hřiště až po specializované prostory pro různé sporty.</p>
                <p><b>Komunitní atmosféra:</b> Věříme v podporu týmového ducha a zdravé soutěžení. U nás se budete cítit jako doma.</p>    
                <p><b>Různé aktivity:</b> Ať už jde o tenis, plavání, běh nebo zimní sporty a máme něco pro každého.</p>
            </div>
        </section>
        
        <section id="gallery">
            <h2>Galerie</h2>
            <div>
                <img src="Picture/football_field.png" alt="Football">
                <img src="Picture/football_field(2).png" alt="Football">
                <img src="Picture/football_field(3).png" alt="Football">
                <img src="Picture/tennis_court.png" alt="Tennis">
                <img src="Picture/pingpong.png" alt="Ping pong">
                <img src="Picture/pimgpong(1).png" alt="Ping pong">
                <img src="Picture/pindpong(2).png" alt="Ping pong">
                <img src="Picture/soccer.png" alt="Soccer">
                <img src="Picture/soccer(1).png" alt="Soccer">
            </div>
        </section>

        <section id="price">
            <h2>Ceník</h2>
            <div class="direction">
                <div class="prices_menu">
                    <h3>Kavárna</h3>
                    <ul>
                        <li>Káva: 50 Kč</li>
                        <li>Čaj: 40 Kč</li>
                        <li>Koláč: 30 Kč</li>
                        <li>Kola: 60 Kč</li>
                        <li>Pivo: 40 Kč</li>
                        <li>Párek: 30 Kč</li>
                    </ul>
                </div>
                <div class="prices_menu">
                    <h3>Kurty</h3>
                    <ul>
                        <li>Fotbalové hřiště: 250 Kč/hod</li>
                        <li>Tenisový kurt: 200 Kč/hod</li>
                        <li>Badmintonový kurt: 180 Kč/hod</li>
                    </ul>
                </div>
            </div>
        </section>

        <section id="reservation">
            <h2>Rezervace</h2>
            <?php if(isset($_SESSION['username'])): ?>
            <div class="reservation-box-logged">
                <form method="post" action="">
                    <select name="court" id="court">
                        <option value="Fotbalové hřiště" <?php if (isset($court) && $court == 'Fotbalové hřiště') echo 'selected'; ?>>Fotbalové hřiště</option>
                        <option value="Tennisový kurt" <?php if (isset($court) && $court == 'Tennisový kurt') echo 'selected'; ?>>Tennisový kurt</option>
                        <option value="Badmintonový kurt" <?php if (isset($court) && $court == 'Badmintonový kurt') echo 'selected'; ?>>Badmintonový kurt</option>
                    </select>
                    <label for="date">Datum:</label>
                    <input type="date" name="date" id="date" min="<?php echo date('Y-m-d'); ?>" value="<?php echo isset($date) ? htmlspecialchars($date) : ''; ?>">
                    <button type="submit">Podívat se</button>
                </form>
                <?php
                if (isset($court) && isset($date)) {
                    $now = new DateTime();
                    $formattedDate = DateTime::createFromFormat('Y-m-d', $date)->format('d.m.Y');
                    foreach ($allTimes as $time) {
                        $dateTimeStr = $date . ' ' . $time . ':00';
                        $selectedDateTime = new DateTime($dateTimeStr);
                        if ($selectedDateTime < $now) {
                            echo "<p>$time: Nedostupné</p>";
                        } elseif (in_array($time, $reservedTimes)) {
                            echo "<p>$time: Obsazeno</p>";
                        } else {
                            echo "<p>$time: <a href='reservation.php?court=$court&date=$formattedDate&time=$time'>Reservovat</a></p>";
                        }
                    }
                }
                ?>
            </div>
            <?php else : ?>
            <div class="reservation-box">
                <p>Pro rezervaci kurtu se prosím přihlašte</p>
            </div>
            <?php endif; ?>
        </section>

        <section id="contacts">
            <h2>Kontakty</h2>
            <div id="contacts_div">
                <div id="contacts_info">
                    <p><b>Adresa:</b> Xaveriova 3369/2, 150 00 Praha 5</p>
                    <p><b>Telefon:</b> 123 456 789</p>
                    <p><b>Email:</b>sportareal@gmail.com</p>
                </div>
                <div id="map">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d905.5273971101419!2d14.398774652481729!3d50.06347810735772!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x470b9451e528fcb9%3A0x86dcf49f972b1617!2sDTJ%20Santo%C5%A1ka!5e0!3m2!1scs!2scz!4v1729871923562!5m2!1scs!2scz" 
                        width="600" 
                        height="450" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </section>
    </main>
    <footer>
        <p>&copy; 2023 Sport Areal.</p>
    </footer>
</body>
</html>