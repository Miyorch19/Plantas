<?php
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);

session_start();

define('CSRF_TOKEN_NAME', 'csrf_token');
define('SESSION_LIFETIME', 1800); // 30 minutos

error_reporting(E_ALL);
ini_set('display_errors', 1);

function init_security() {
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    
    $_SESSION['last_activity'] = time();
    
    header("X-Frame-Options: SAMEORIGIN");
    header("X-XSS-Protection: 1; mode=block");
    header("X-Content-Type-Options: nosniff");
}

function limpiar_dato($dato) {
    if (is_array($dato)) {
        return array_map('limpiar_dato', $dato);
    }
    return htmlspecialchars(strip_tags(trim($dato)), ENT_QUOTES, 'UTF-8');
}

function verificar_sesion() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../public/login.php");
        exit();
    }
    
    if (isset($_SESSION['last_activity']) && 
        (time() - $_SESSION['last_activity'] > SESSION_LIFETIME)) {
        cerrar_sesion();
    }
}

function cerrar_sesion() {
    session_unset();
    session_destroy();
    setcookie(session_name(), '', time() - 3600, '/');
    header("Location: ../public/login.php");
    exit();
}

function encriptar_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verificar_password($password, $hash) {
    return password_verify($password, $hash);
}

function obtener_csrf_token() {
    return $_SESSION[CSRF_TOKEN_NAME] ?? '';
}

function verificar_csrf_token($token) {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && 
           hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

function validar_campo($valor, $tipo = 'texto') {
    switch ($tipo) {
        case 'email':
            return filter_var($valor, FILTER_VALIDATE_EMAIL);
        case 'numero':
            return filter_var($valor, FILTER_VALIDATE_INT);
        case 'url':
            return filter_var($valor, FILTER_VALIDATE_URL);
        default:
            return !empty(trim($valor));
    }
}

function log_debug($mensaje) {
    error_log("[DEBUG] " . date('Y-m-d H:i:s') . " - " . $mensaje);
}

init_security();
?>