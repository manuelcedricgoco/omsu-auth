<?php
require __DIR__ . '/includes/helpers.php';
if (isset($_SESSION['uid'])) { header('Location: dashboard.php'); exit; }
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $username = trim($_POST['username'] ?? '');
    $pw       = $_POST['password'] ?? '';

    $stmt = db()->prepare('SELECT id, password_hash FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // VERIFY: password_verify() re-hashes the typed password with the stored salt and compares.
    $ok = password_verify($pw, $user['password_hash'] ?? password_hash('dummy', PASSWORD_DEFAULT)); // dummy work for unknown users, so timing doesn't reveal them

    if ($user && $ok) {
        session_regenerate_id(true);
        $_SESSION['uid'] = (int)$user['id'];
        flash('Login successful. Welcome back!', 'success');
        header('Location: dashboard.php'); exit;
    }
    flash('Incorrect username or password.'); // same message for both failures
}

auth_start('Sign in', 'Use your username and password to continue.'); ?>
      <form method="post">
        <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">
        <label for="username">Username</label>
        <input id="username" name="username" value="<?= e($username) ?>" required autofocus autocomplete="username">
        <label for="password">Password</label>
        <div class="pw"><input id="password" name="password" type="password" required autocomplete="current-password">
          <button type="button" data-toggle="password">Show</button></div>
        <button class="btn" type="submit">Sign in</button>
      </form>
      <p class="alt">New here? <a href="signup.php">Create an account</a></p>
<?php auth_end();
