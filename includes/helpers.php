<?php
require_once __DIR__ . '/config.php';

session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
session_start();

/** PDO connection. Creates the database and users table on first run. */
function db(): PDO {
    static $pdo = null;
    if ($pdo) return $pdo;
    $opts = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];
    $pdo = new PDO('mysql:host=' . DB_HOST . ';charset=utf8mb4', DB_USER, DB_PASS, $opts);
    $pdo->exec('CREATE DATABASE IF NOT EXISTS `' . DB_NAME . '` CHARACTER SET utf8mb4');
    $pdo->exec('USE `' . DB_NAME . '`');
    $pdo->exec('CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(30) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    )');
    return $pdo;
}

function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

function flash(string $msg, string $type = 'error'): void { $_SESSION['flash'][] = [$type, $msg]; }

function csrf(): string {
    return $_SESSION['csrf'] ??= bin2hex(random_bytes(16));
}

function check_csrf(): void {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(400);
        exit('Invalid form token. Go back, reload the page and try again.');
    }
}

function flashes_html(): string {
    $out = '';
    foreach ($_SESSION['flash'] ?? [] as [$type, $msg]) {
        $out .= '<div class="flash ' . e($type) . '" role="alert">' . e($msg) . '</div>';
    }
    unset($_SESSION['flash']);
    return $out;
}

function head(string $title): void { ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?> · OMSU</title>
<link rel="icon" href="assets/seal.png">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Figtree:wght@400;500;600&display=swap">
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php }

function foot(): void { ?>
<script>
document.querySelectorAll('[data-toggle]').forEach(b => b.addEventListener('click', () => {
  const i = document.getElementById(b.dataset.toggle), show = i.type === 'password';
  i.type = show ? 'text' : 'password'; b.textContent = show ? 'Hide' : 'Show';
}));
</script>
</body>
</html>
<?php }

/** Split-screen shell used by the Sign In / Sign Up pages. */
function auth_start(string $title, string $sub): void {
    head($title); ?>
<main class="split">
  <aside class="brand">
    <div class="rays" aria-hidden="true"></div>
    <img class="seal" src="assets/seal.png" alt="Occidental Mindoro State University seal">
    <div class="brand-text">
      <h2>Occidental Mindoro<br>State University</h2>
      <p>Secure student portal · Est. 1966</p>
    </div>
  </aside>
  <section class="panel">
    <div class="card">
      <img class="seal-sm" src="assets/seal.png" alt="">
      <h1><?= e($title) ?></h1>
      <p class="sub"><?= e($sub) ?></p>
      <?= flashes_html() ?>
<?php }

function auth_end(): void { ?>
    </div>
    <p class="foot">Passwords are hashed with bcrypt and salted automatically. We never store the real thing.</p>
  </section>
</main>
<?php foot(); }
