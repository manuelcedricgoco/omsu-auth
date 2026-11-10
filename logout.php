<?php
require __DIR__ . '/includes/helpers.php';
session_unset();
session_regenerate_id(true);
flash('You have been signed out.', 'success');
header('Location: login.php');
