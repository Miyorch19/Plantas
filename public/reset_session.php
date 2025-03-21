<?php
session_start();
session_destroy();
header("Location: login.php"); // O la página de inicio de sesión
exit();
?>
