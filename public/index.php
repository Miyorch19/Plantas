<?php
require_once '../config/security.php';
verificar_sesion();

if ($_SESSION['role'] !== 'admin') {
    echo "Acceso denegado.";
    exit();
}
?>

<h1>Bienvenido Admin <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
<form action="../controllers/agregar_planta.php" method="post">
    <input type="text" name="nombre" placeholder="Nombre de la planta" required>
    <textarea name="descripcion" placeholder="Descripción" required></textarea>
    <input type="text" name="clima" placeholder="Clima" required>
    <button type="submit">Agregar Planta</button>
</form>
<a href="../controllers/logout.php">Cerrar sesión</a>
