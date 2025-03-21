<?php
require_once '../config/security.php';
require_once '../patrones/Database.php';
use Patrones\Database;
verificar_sesion();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SESSION['role'] !== 'admin') {
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['Id_planta'])) {
    $id = limpiar_dato($_POST['Id_planta']);
    $nombre = limpiar_dato($_POST['nombre']);
    $descripcion = limpiar_dato($_POST['descripcion']);
    $clima = limpiar_dato($_POST['clima']);
    
    $pdo = Database::getInstance()->getConnection();
    
    $stmt = $pdo->prepare("SELECT Imagen FROM plantas WHERE Id_planta = ?");
    $stmt->execute([$id]);
    $planta = $stmt->fetch(PDO::FETCH_ASSOC);
    $imagenActual = $planta['Imagen'];
    
    $imagenNombre = $imagenActual;
    
    if (!empty($_FILES['imagen']['name'])) {
        $directorioSubida = '../uploads/';
        $imagenNombre = time() . '_' . basename($_FILES['imagen']['name']);
        $rutaImagen = $directorioSubida . $imagenNombre;
        $tipoArchivo = strtolower(pathinfo($rutaImagen, PATHINFO_EXTENSION));
    
        $formatosPermitidos = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($tipoArchivo, $formatosPermitidos)) {
            die("Error: Formato de imagen no permitido.");
        }
    
        if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaImagen)) {
            die("Error: No se pudo mover el archivo al directorio de subida.");
        }
        
        if ($imagenActual && file_exists($directorioSubida . $imagenActual)) {
            unlink($directorioSubida . $imagenActual);
        }
    }
    
    $stmt = $pdo->prepare("UPDATE plantas SET Nombre_planta = ?, Descripcion = ?, Clima = ?, Imagen = ? WHERE Id_planta = ?");
    $resultado = $stmt->execute([$nombre, $descripcion, $clima, $imagenNombre, $id]);
    
    if ($resultado) {
        header("Location: ../public/home_admin.php");
        exit();
    } else {
        echo "Error al actualizar la planta";
    }
}
?>