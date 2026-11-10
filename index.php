<?php
require __DIR__ . '/includes/helpers.php';
header('Location: ' . (isset($_SESSION['uid']) ? 'dashboard.php' : 'login.php'));
