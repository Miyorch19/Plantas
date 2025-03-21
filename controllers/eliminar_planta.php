<?php
require_once '../config/security.php';
require_once '../patrones/Database.php';

use Patrones\Database;

verificar_sesion();

$pdo = Database::getInstance()->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_POST['id']) ? $_POST['id'] : null;

    if ($id) {
        $query = "DELETE FROM plantas WHERE Id_planta = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$id]);

        header("Location: ../public/home_admin.php");
        exit();
    } else {
        echo "ID no válido.";
    }
}
?>
