<?php
require __DIR__ . '/includes/helpers.php';
if (!isset($_SESSION['uid'])) { header('Location: login.php'); exit; }

$stmt = db()->prepare('SELECT username, password_hash, created_at FROM users WHERE id = ?');
$stmt->execute([$_SESSION['uid']]);
$user = $stmt->fetch();
if (!$user) { session_destroy(); header('Location: login.php'); exit; }

// bcrypt format: $2y$ + 2-digit cost + $ + 22-char salt + 31-char hash
$h = $user['password_hash'];
$info = password_get_info($h);
$cost = $info['options']['cost'] ?? '?';
$salt = substr($h, 7, 22);
$digest = substr($h, 29);

head('Dashboard'); ?>
<header class="top">
  <div class="id"><img src="assets/seal.png" alt=""><strong>OMSU Portal</strong></div>
  <a class="ghost" href="logout.php" style="text-decoration:none">Sign out</a>
</header>
<main class="dash">
  <?= flashes_html() ?>
  <h1>Hello, <?= e($user['username']) ?></h1>
  <p class="sub">You are signed in. Member since <?= e($user['created_at']) ?>.</p>

  <section class="proof">
    <h2>How your password is stored</h2>
    <p>This is what the database holds for your account. The password you typed is not in it, and it cannot be read back.</p>
    <dl>
      <dt>Algorithm</dt><dd><?= e($info['algoName']) ?> <span class="muted">(cost = <?= e((string)$cost) ?>)</span></dd>
      <dt>Salt (random, per user)</dt><dd class="mono"><?= e($salt) ?></dd>
      <dt>Hash</dt><dd class="mono"><?= e($digest) ?></dd>
      <dt>Full value in the database</dt><dd class="mono"><?= e($h) ?></dd>
    </dl>
    <ol class="how">
      <li>On sign up, <code>password_hash()</code> adds a random salt and runs the password through bcrypt.</li>
      <li>Only that one hash string is saved.</li>
      <li>On sign in, <code>password_verify()</code> hashes the typed password with the saved salt and compares the result.</li>
    </ol>
  </section>
</main>
<?php foot();
