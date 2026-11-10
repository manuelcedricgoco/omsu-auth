<?php
require __DIR__ . '/includes/helpers.php';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $username = trim($_POST['username'] ?? '');
    $pw       = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    if (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $username)) {
        flash('Username must be 3–30 characters: letters, numbers or underscore.');
    } elseif (strlen($pw) < 8) {
        flash('Password must be at least 8 characters.');
    } elseif ($pw !== $confirm) {
        flash('Passwords do not match.');
    } else {
        // HASHING: password_hash() creates a random salt and hashes with bcrypt.
        $hash = password_hash($pw, PASSWORD_DEFAULT);
        try {
            db()->prepare('INSERT INTO users (username, password_hash) VALUES (?, ?)')
                ->execute([$username, $hash]);
            flash('Account created. You can sign in now.', 'success');
            header('Location: login.php'); exit;
        } catch (PDOException $ex) {
            flash($ex->getCode() === '23000' ? 'That username is already taken.' : 'Could not create the account. Check the database connection.');
        }
    }
}

auth_start('Create your account', 'Pick a username and a password of at least 8 characters.'); ?>
      <form method="post">
        <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">
        <label for="username">Username</label>
        <input id="username" name="username" value="<?= e($username) ?>" required autofocus minlength="3" maxlength="30" pattern="[A-Za-z0-9_]+" autocomplete="username">
        <label for="password">Password</label>
        <div class="pw"><input id="password" name="password" type="password" required minlength="8" autocomplete="new-password">
          <button type="button" data-toggle="password">Show</button></div>
        <label for="confirm">Confirm password</label>
        <div class="pw"><input id="confirm" name="confirm" type="password" required minlength="8" autocomplete="new-password">
          <button type="button" data-toggle="confirm">Show</button></div>
        <button class="btn" type="submit">Create account</button>
      </form>
      <p class="alt">Already registered? <a href="login.php">Sign in</a></p>
<?php auth_end();
