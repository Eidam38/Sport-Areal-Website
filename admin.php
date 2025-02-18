<?php
/**
 * This script handles the role update for the Sport Areal website.
 */

session_start();

/**
 * Ensures the user is an admin. If not, redirects to the main page.
 *
 * @return void
 */
function ensureAdmin() {
    if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
        header("Location: main.php");
        exit();
    }
}

/**
 * Updates the role of a user in the users.txt file.
 *
 * @param string $emailToUpdate The email of the user to update.
 * @param string $newRole The new role to assign to the user.
 * @return void
 */
function updateUserRole($emailToUpdate, $newRole) {
    $users = file('Data/users.txt', FILE_IGNORE_NEW_LINES);

    $updatedUsers = array_map(function($user) use ($emailToUpdate, $newRole) {
        list($email, $password, $role) = explode('|', $user);
        if ($email === $emailToUpdate) {
            return "$email|$password|$newRole";
        }
        return $user;
    }, $users);

    file_put_contents('Data/users.txt', implode("\n", $updatedUsers) . "\n");
}

/**
 * Handles the role update request from a POST form submission.
 *
 * @param string $emailToUpdate The email of the user to update.
 * @param string $newRole The new role to assign to the user.
 * @return void
 */
function handleRoleUpdateRequest() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email']) && isset($_POST['role'])) {
        $emailToUpdate = $_POST['email'];
        $newRole = $_POST['role'];
        updateUserRole($emailToUpdate, $newRole);
        header("Location: admin.php");
        exit();
    }
}

ensureAdmin();
handleRoleUpdateRequest();

$users = file('Data/users.txt', FILE_IGNORE_NEW_LINES);
?>

<!DOCTYPE html>
<html lang="cs" class="admin">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>List lidí</h1>
    <section>
        <ul>
            <?php foreach ($users as $user): ?>
                <?php list($email, $password, $role) = explode('|', $user); ?>
                <li>
                    <?php echo htmlspecialchars($email); ?> (<?php echo htmlspecialchars($role); ?>)
                    <form action="admin.php" method="post" style="display:inline;">
                        <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                        <select name="role">
                            <option value="user" <?php if ($role === 'user') echo 'selected'; ?>>User</option>
                            <option value="admin" <?php if ($role === 'admin') echo 'selected'; ?>>Admin</option>
                        </select>
                        <input type="submit" value="Změnit roli">
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <a href="main.php">Zpět</a>
</body>
</html>