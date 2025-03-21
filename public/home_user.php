<?php
require_once '../config/security.php';
require_once '../patrones/Database.php';

use Patrones\Database;

verificar_sesion();


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
    <title>Inicio - Usuario</title>
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

        /* Main Content Styles */
        .main-content {
            padding: 2rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .welcome-section {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        h1 {
            font-size: 2rem;
            color: var(--dark-green);
            margin-bottom: 0.5rem;
        }

        h2 {
            font-size: 1.5rem;
            color: var(--medium-green);
            margin-bottom: 2rem;
        }

        .plantas-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
        }

        .planta-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .planta-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .planta-img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .planta-info {
            padding: 1.5rem;
            text-align: center;
        }

        .planta-card h3 {
            color: var(--dark-green);
            margin-bottom: 1rem;
            font-size: 1.2rem;
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
            color: var(--medium-green);
            border: 2px solid var(--medium-green);
        }

        .btn-outline:hover {
            background: var(--medium-green);
            color: white;
        }

        /* Modal Styles */
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

        @media (max-width: 768px) {
            .nav-container {
                flex-direction: column;
                gap: 1rem;
            }

            .nav-links {
                flex-direction: column;
                gap: 1rem;
            }

            .main-content {
                padding: 1rem;
            }

            .plantas-grid {
                grid-template-columns: 1fr;
            }
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
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="home_user.php" class="nav-logo">
                Plantas
            </a>
            <div class="nav-links">
                <a href="home_user.php" class="nav-link">
                    <i class="fas fa-home"></i>
                    Inicio
                </a>
                <a href="productos_user.php" class="nav-link">
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
            <div class="welcome-section">
                <h1>Hola <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
                <p>Explora nuestra colección de plantas y encuentra la perfecta para ti.</p>
            </div>

            <h2>Lista de Plantas</h2>

            <form method="GET" class="search-bar">
            <input type="text" name="search" placeholder="Buscar planta..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn">Buscar</button>
        </form>

            <div class="plantas-grid">
                <?php if (count($plantas) > 0): ?>
                    <?php foreach ($plantas as $planta): ?>
                        <div class="planta-card">
                            <?php if (!empty($planta['Imagen'])): ?>
                                <img src="../uploads/<?php echo htmlspecialchars($planta['Imagen']); ?>" alt="<?php echo htmlspecialchars($planta['Nombre_planta']); ?>" class="planta-img">
                            <?php endif; ?>
                            <div class="planta-info">
                                <h3><?php echo htmlspecialchars($planta['Nombre_planta']); ?></h3>
                                <button class="btn" onclick="openModal('<?php echo $planta['Id_planta']; ?>', '<?php echo htmlspecialchars($planta['Nombre_planta']); ?>', '<?php echo htmlspecialchars($planta['Descripcion']); ?>', '<?php echo htmlspecialchars($planta['Clima']); ?>', '<?php echo htmlspecialchars($planta['Imagen'] ?? ''); ?>')">
                                    <i class="fas fa-info-circle"></i>
                                    Ver información
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No hay plantas registradas.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Modal -->
        <div id="plantModal" class="modal">
            <div class="modal-content">
                <button class="modal-close" id="closeModal">&times;</button>
                <h2 id="modalNombre"></h2>
                <img id="modalImagen" src="" alt="Imagen de la planta" style="width: 100%; height: 250px; object-fit: cover; margin-bottom: 1rem; border-radius: 8px;">
                <p id="modalDescripcion" style="margin-bottom: 1rem; line-height: 1.6;"></p>
                <p><strong><i class="fas fa-cloud-sun"></i> Clima:</strong> <span id="modalClima"></span></p>
            </div>
        </div>
    </main>

    <script>
        function openModal(id, nombre, descripcion, clima, imagen) {
            document.getElementById('modalNombre').textContent = nombre;
            document.getElementById('modalDescripcion').textContent = descripcion;
            document.getElementById('modalClima').textContent = clima;
            const modalImagen = document.getElementById('modalImagen');
            if (imagen) {
                modalImagen.src = '../uploads/' + imagen;
                modalImagen.style.display = 'block';
            } else {
                modalImagen.style.display = 'none';
            }
            const modal = document.getElementById('plantModal');
            modal.style.display = 'flex';
            setTimeout(() => modal.classList.add('active'), 10);
        }

        function closeModal() {
            const modal = document.getElementById('plantModal');
            modal.classList.remove('active');
            setTimeout(() => modal.style.display = 'none', 300);
        }

        document.getElementById('closeModal').addEventListener('click', closeModal);

        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                closeModal();
            }
        }
    </script>
</body>
</html>