<?php
session_start();
require_once '../patrones/Database.php';
require_once '../config/security.php';

use Patrones\Database;

if (!isset($_SESSION['generated']) || $_SESSION['generated'] < (time() - 3600)) {
    session_regenerate_id(true);
    $_SESSION['generated'] = time();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = limpiar_dato($_POST['email']);
    $password = limpiar_dato($_POST['password']);

    unset($_SESSION['error_email']);
    unset($_SESSION['error_password']);

    if (empty($email)) {
        $_SESSION['error_email'] = 'Por favor, ingresa un correo electrónico.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_email'] = 'El correo electrónico no es válido.';
    }

    if (empty($password)) {
        $_SESSION['error_password'] = 'Por favor, ingresa una contraseña.';
    }

    if (isset($_SESSION['error_email']) || isset($_SESSION['error_password'])) {
        header("Location: ../public/login.php");
        exit();
    }

    $pdo = Database::getInstance()->getConnection();
    $stmt = $pdo->prepare("SELECT il_user, username, email, password, role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        $_SESSION['error_email'] = 'Correo electrónico incorrecto.';
        header("Location: ../public/login.php");
        exit();
    }

    if (!verificar_password($password, $user['password'])) {
        $_SESSION['error_password'] = 'Contraseña incorrecta.';
        header("Location: ../public/login.php");
        exit();
    }

    $_SESSION['user_id'] = $user['il_user'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];

    if ($user['role'] == 'admin') {
        header("Location: ../public/home_admin.php");
    } else {
        header("Location: ../public/home_user.php");
    }
    exit();
}
?>
