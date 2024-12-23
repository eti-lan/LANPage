<?php
require_once 'config.php';
session_start();

// Einfache Authentifizierung
if (!isset($_SESSION['admin']) && (!isset($_POST['password']) || $_POST['password'] !== $admin_password)) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Admin Login</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 40px; }
            .login-form {
                max-width: 300px;
                margin: 0 auto;
                padding: 20px;
                border: 1px solid #ddd;
                border-radius: 5px;
            }
            input, button {
                width: 100%;
                padding: 8px;
                margin: 5px 0;
            }
            button { 
                background: #4CAF50;
                color: white;
                border: none;
                cursor: pointer;
            }
        </style>
    </head>
    <body>
        <div class="login-form">
            <h2>Admin Login</h2>
            <form method="post">
                <input type="password" name="password" placeholder="Admin-Passwort" required>
                <button type="submit">Login</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}
else {
	$_SESSION['admin'] = true;
}
?>