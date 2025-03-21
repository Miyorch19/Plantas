<?php
session_start();
require_once '../Patrones/Database.php';
require_once '../config/security.php';

use Patrones\Database;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = limpiar_dato($_POST['username']);
    $email = limpiar_dato($_POST['email']);
    $password = limpiar_dato($_POST['password']);

    if (empty($username) || empty($email) || empty($password)) {
        $_SESSION['error'] = 'Por favor, completa todos los campos.';
        header("Location: ../public/register.php");
        exit();
    }

    if (!preg_match("/^[a-zA-Z0-9]+$/", $username)) {
        $_SESSION['error'] = 'El nombre de usuario solo puede contener letras y números.';
        header("Location: ../public/register.php");
        exit();
    }

    if (strlen($password) < 8) {
        $_SESSION['error'] = 'La contraseña debe tener al menos 8 caracteres.';
        header("Location: ../public/register.php");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'El correo electrónico no es válido.';
        header("Location: ../public/register.php");
        exit();
    }

    $pdo = Database::getInstance()->getConnection();
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $emailCount = $stmt->fetchColumn();

    if ($emailCount > 0) {
        $_SESSION['error_email'] = 'El correo ya está registrado.';
        header("Location: ../public/register.php");
        exit();
    }


    $hashedPassword = encriptar_password($password);

    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");

    try {
        $stmt->execute([$username, $email, $hashedPassword]);
        header("Location: ../public/login.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Error al registrar el usuario: ' . $e->getMessage();
        header("Location: ../public/register.php");
        exit();
    }
}
?>