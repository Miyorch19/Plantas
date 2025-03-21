<?php
require_once '../config/security.php';
require_once '../patrones/Database.php';

use Patrones\Database;

verificar_sesion();

if ($_SESSION['role'] !== 'admin') {
    echo "Acceso denegado.";
    exit();
}

$pdo = Database::getInstance()->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accion']) && $_POST['accion'] == "agregar") {
    $nombre = limpiar_dato($_POST['nombre']);
    $descripcion = limpiar_dato($_POST['descripcion']);
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

    $stmt = $pdo->prepare("INSERT INTO productos (nombre_prod, descripcion, imagen) VALUES (?, ?, ?)");
    $stmt->execute([$nombre, $descripcion, $imagenNombre]);

    header("Location: ../public/productos_admin.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accion']) && $_POST['accion'] == "eliminar") {
    $id = $_POST['id'];

    $stmt = $pdo->prepare("SELECT imagen FROM productos WHERE id_prod = ?");
    $stmt->execute([$id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($producto && $producto['imagen']) {
        $rutaImagen = '../uploads/' . $producto['imagen'];
        if (file_exists($rutaImagen)) {
            unlink($rutaImagen);
        }
    }

    $stmt = $pdo->prepare("DELETE FROM productos WHERE id_prod = ?");
    $stmt->execute([$id]);

    header("Location: ../public/productos_admin.php");
    exit();
}
// Editar producto
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accion']) && $_POST['accion'] == "editar") {
    $id = $_POST['id'];
    $nombre = limpiar_dato($_POST['nombre']);
    $descripcion = limpiar_dato($_POST['descripcion']);

    $stmt = $pdo->prepare("SELECT imagen FROM productos WHERE id_prod = ?");
    $stmt->execute([$id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);
    $imagenActual = $producto['imagen'];

    $imagenNombre = $imagenActual;

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

        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaImagen)) {
            if ($imagenActual && file_exists($directorioSubida . $imagenActual)) {
                unlink($directorioSubida . $imagenActual);
            }
        } else {
            echo "Error al subir la imagen.";
            exit();
        }
    }

    $stmt = $pdo->prepare("UPDATE productos SET nombre_prod = ?, descripcion = ?, imagen = ? WHERE id_prod = ?");
    $stmt->execute([$nombre, $descripcion, $imagenNombre, $id]);

    header("Location: ../public/productos_admin.php");
    exit();
}
?>
