<?php
require_once '../config/security.php';
require_once '../patrones/Database.php';
require_once '../patrones/PlantaBuilder.php';

use Patrones\Database;
use Patrones\PlantaBuilder;

verificar_sesion();

if ($_SESSION['role'] !== 'admin') {
    echo "Acceso denegado.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $builder = (new PlantaBuilder())
        ->setNombre(limpiar_dato($_POST['nombre']))
        ->setDescripcion(limpiar_dato($_POST['descripcion']))
        ->setClima(limpiar_dato($_POST['clima']));

    $imagenNombre = null;

    if (!empty($_FILES['imagen']['name'])) {
        $directorioSubida = '../uploads/';
        $imagenNombre = time() . '_' . basename($_FILES['imagen']['name']);
        $rutaImagen = $directorioSubida . $imagenNombre;
        $tipoArchivo = strtolower(pathinfo($rutaImagen, PATHINFO_EXTENSION));

        $formatosPermitidos = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($tipoArchivo, $formatosPermitidos)) {
            echo "Error: Formato de imagen no permitido.";
            exit();
        }

        if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaImagen)) {
            echo "Error al subir la imagen.";
            exit();
        }
    }

    $planta = $builder->build();

    if (!empty($planta->nombre) && !empty($planta->descripcion) && !empty($planta->clima)) {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("INSERT INTO plantas (Nombre_planta, Descripcion, Clima, Imagen) VALUES (?, ?, ?, ?)");
        $stmt->execute([$planta->nombre, $planta->descripcion, $planta->clima, $imagenNombre]);

        header("Location: ../public/home_admin.php");
        exit();

    } else {
        echo "Por favor, completa todos los campos.";
    }
}
?>
