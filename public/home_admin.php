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

$search = isset($_GET['search']) ? limpiar_dato($_GET['search']) : '';
$query = "SELECT * FROM plantas WHERE Nombre_planta LIKE ? OR Descripcion OR clima LIKE ?";
$stmt = $pdo->prepare($query);
$stmt->execute(["%$search%", "%$search%"]);
$plantas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Plantas</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --dark-green: #123524;
            --medium-green: #3E7B27;
            --light-green: #85A947;
            --cream: #EFE3C2;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-800: #1f2937;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'DM Sans', sans-serif;
        }

        body {
            background-color: var(--gray-100);
            min-height: 100vh;
            color: var(--gray-800);
            
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 3rem;
            padding: 1rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            animation: fadeIn 1s ease-in-out;
        }

        .header h1 {
            font-size: 1.5rem;
            color: var(--dark-green);
        }

        .search-bar {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            width: 100%;
            max-width: 600px;
            margin: 2rem auto;
            animation: slideIn 0.5s ease-in-out;
        }

        .search-bar input {
            flex: 1;
            padding: 0.8rem 1.2rem;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .search-bar input:focus {
            outline: none;
            border-color: var(--medium-green);
            box-shadow: 0 0 0 2px rgba(62, 123, 39, 0.1);
        }

        .btn {
            background: var(--medium-green);
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-decoration: none;
            display: inline-block;
        }

        .btn:hover {
            background: var(--dark-green);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--medium-green);
            color: var(--medium-green);
        }

        .btn-outline:hover {
            background: var(--medium-green);
            color: white;
        }

        .plantas-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
        }

        .plant-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .plant-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .plant-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .plant-card:hover .plant-image {
            transform: scale(1.05);
        }

        .plant-info {
            padding: 1.5rem;
        }

        .plant-info h3 {
            color: var(--dark-green);
            margin-bottom: 0.5rem;
        }

        .plant-info p {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .card-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            backdrop-filter: blur(5px);
            animation: fadeIn 0.3s ease-in-out;
        }

        .modal-content {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            position: relative;
            transform: translateY(20px);
            opacity: 0;
            transition: all 0.3s ease;
        }

        .modal.active .modal-content {
            transform: translateY(0);
            opacity: 1;
        }

        .modal-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #666;
            padding: 0.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 0.9rem;
        }

        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        .nav-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            .plants-grid {
                grid-template-columns: 1fr;
            }

            .header {
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }
        }

                /* Navbar Styles */
                .navbar {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-logo {
            color: var(--dark-green);
            font-size: 1.5rem;
            font-weight: 700;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .nav-link {
            color: var(--gray-800);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-link:hover {
            color: var(--medium-green);
        }

        .nav-link.active {
            color: var(--medium-green);
        }

        .main-content {
            padding: 2rem;
        }
                .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
    </style>
</head>
<body>
            <!-- Navbar -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="home_admin.php" class="nav-logo">
                Plantas
            </a>
            <div class="nav-links">
                <a href="home_admin.php" class="nav-link ">
                    <i class="fas fa-home"></i>
                    Inicio
                </a>
                <a href="productos_admin.php" class="nav-link">
                    <i class="fas fa-shopping-bag"></i>
                    Productos
                </a>
                <a href="../controllers/logout.php" class="btn btn-outline">
                    <i class="fas fa-sign-out-alt"></i>
                    Cerrar sesión
                </a>
            </div>
        </div>
    </nav>

        <main class="main-content">
        <div class="container">

        <form method="GET" class="search-bar">
            <input type="text" name="search" placeholder="Buscar planta..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn">Buscar</button>
        </form>

        <button id="openModal" class="btn" style="margin-bottom: 30px;">Agregar Nueva Planta</button>

        <div class="plantas-grid">
            <?php foreach ($plantas as $planta): ?>
                <div class="plant-card">
                    <?php if (!empty($planta['Imagen'])): ?>
                        <img class="plant-image" src="../uploads/<?php echo htmlspecialchars($planta['Imagen']); ?>" alt="<?php echo htmlspecialchars($planta['Nombre_planta']); ?>">
                    <?php endif; ?>
                    <div class="plant-info">
                        <h3><?php echo htmlspecialchars($planta['Nombre_planta']); ?></h3>
                        <p><?php echo htmlspecialchars($planta['Descripcion']); ?></p>
                        <p><strong>Clima:</strong> <?php echo htmlspecialchars($planta['Clima']); ?></p>
                        <div class="card-actions">
                            <button class="btn btn-outline" onclick="openEditModal('<?php echo $planta['Id_planta']; ?>', '<?php echo htmlspecialchars($planta['Nombre_planta']); ?>', '<?php echo htmlspecialchars($planta['Descripcion']); ?>', '<?php echo htmlspecialchars($planta['Clima']); ?>', '<?php echo htmlspecialchars($planta['Imagen'] ?? ''); ?>')">Editar</button>
                            <button class="btn" onclick="openDeleteModal('<?php echo $planta['Id_planta']; ?>', '<?php echo htmlspecialchars($planta['Nombre_planta']); ?>')">Eliminar</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        </div>
        </main>

        <!-- Modal para agregar plantas -->
        <div id="modal" class="modal">
            <div class="modal-content">
                <button class="modal-close" id="closeModal">&times;</button>
                <h2>Agregar Nueva Planta</h2>
                <form action="../controllers/agregar_planta.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="nombre">Nombre de la planta</label>
                        <input type="text" id="nombre" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea id="descripcion" name="descripcion" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="clima">Clima</label>
                        <input type="text" id="clima" name="clima" required>
                    </div>
                    <div class="form-group">
                        <label for="imagen">Imagen</label>
                        <input type="file" id="imagen" name="imagen" required accept="image/*">
                    </div>
                    <button type="submit" class="btn">Agregar Planta</button>
                </form>
            </div>
        </div>

        <!-- Modal para editar plantas -->
        <div id="editModal" class="modal">
            <div class="modal-content">
                <button class="modal-close" id="closeEditModal">&times;</button>
                <h2>Editar Planta</h2>
                <form action="../controllers/editar_planta.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="Id_planta" id="editId">
                    <div class="form-group">
                        <label for="editNombre">Nombre de la planta</label>
                        <input type="text" id="editNombre" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="editDescripcion">Descripción</label>
                        <textarea id="editDescripcion" name="descripcion" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="editClima">Clima</label>
                        <input type="text" id="editClima" name="clima" required>
                    </div>
                    <div class="form-group">
                        <label for="editImagen">Nueva imagen (opcional)</label>
                        <input type="file" id="editImagen" name="imagen" accept="image/*">
                        <img id="editImagenPreview" src="" alt="Imagen actual" style="max-width: 150px; margin-top: 1rem; display: none;">
                    </div>
                    <button type="submit" class="btn">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para confirmar eliminación -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <button class="modal-close" id="closeDeleteModal">&times;</button>
        <h2>Confirmar Eliminación</h2>
        <p style="margin: 1rem 0;">¿Estás seguro que deseas eliminar esta planta? Esta acción no se puede deshacer.</p>
        <form id="deleteForm" action="../controllers/eliminar_planta.php" method="POST">
            <input type="hidden" name="id" id="deleteId">
            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <button type="button" class="btn btn-outline" onclick="closeModal('deleteModal')">Cancelar</button>
                <button type="submit" class="btn">Eliminar</button>
            </div>
        </form>
    </div>
</div>

    <script>
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.style.display = 'flex';
            setTimeout(() => modal.classList.add('active'), 10);
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.remove('active');
            setTimeout(() => modal.style.display = 'none', 300);
        }

        document.getElementById('openModal').addEventListener('click', () => openModal('modal'));
        document.getElementById('closeModal').addEventListener('click', () => closeModal('modal'));
        document.getElementById('closeEditModal').addEventListener('click', () => closeModal('editModal'));

        function openEditModal(id, nombre, descripcion, clima, imagen) {
            document.getElementById('editId').value = id;
            document.getElementById('editNombre').value = nombre;
            document.getElementById('editDescripcion').value = descripcion;
            document.getElementById('editClima').value = clima;
            const preview = document.getElementById('editImagenPreview');
            if (imagen) {
                preview.src = '../uploads/' + imagen;
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }
            openModal('editModal');
        }

function openDeleteModal(id, nombre) {
    document.getElementById('deleteId').value = id;
    const modalContent = document.querySelector('#deleteModal .modal-content p');
    modalContent.textContent = `¿Estás seguro que deseas eliminar la planta "${nombre}"? Esta acción no se puede deshacer.`;
    openModal('deleteModal');
}

document.getElementById('closeDeleteModal').addEventListener('click', () => closeModal('deleteModal'));
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                closeModal(event.target.id);
            }
        }
    </script>
</body>
</html>