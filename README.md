# OMSU Secure Login — Laboratory Activity #4 (PHP + MySQL, XAMPP)

## Run with XAMPP
1. Start **Apache** and **MySQL** in the XAMPP Control Panel.
2. Copy this folder to `C:\xampp\htdocs\omsu-auth` (macOS/Linux: `/opt/lampp/htdocs/`).
3. Open http://localhost/omsu-auth/ — the database `omsu_auth` and table `users` are created automatically.
   (Or import `database.sql` in phpMyAdmin.)

If your MySQL root has a password, edit `includes/config.php`.

## Proof of hashing
Open http://localhost/phpmyadmin → `omsu_auth` → `users` → Browse, and screenshot the `password_hash` column.

## How it works
- **Sign up:** `password_hash($pw, PASSWORD_DEFAULT)` → bcrypt with an automatic random salt → stored in `password_hash`.
- **Sign in:** fetch the row by username, then `password_verify($pw, $storedHash)`.
- Wrong password and unknown username show the same message.
- Also: PDO prepared statements, CSRF tokens, escaped output, session ID regenerated on login.
