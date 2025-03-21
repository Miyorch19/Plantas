<!DOCTYPE html>
<html lang="es">
<head>
    <?php session_start(); ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="styles.css">
    <script>
function showError(message, inputElement) {
    // Eliminar mensajes de error anteriores
    const previousError = inputElement.parentElement.querySelector('.error-message');
    if (previousError) {
        previousError.remove();
    }

    // Crear y mostrar nuevo mensaje de error
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.textContent = message;
    inputElement.parentElement.insertBefore(errorDiv, inputElement.nextSibling);
    
    // Añadir clase de error al input
    inputElement.classList.add('input-error');

    // Remover el error después de 5 segundos
    setTimeout(() => {
        errorDiv.remove();
        inputElement.classList.remove('input-error');
    }, 5000);
}

function validateForm() {
    const form = document.forms["registerForm"];
    const username = form["username"];
    const email = form["email"];
    const password = form["password"];
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    let isValid = true;

    // Limpiar errores anteriores
    document.querySelectorAll('.error-message').forEach(error => error.remove());
    document.querySelectorAll('.input-error').forEach(input => input.classList.remove('input-error'));

    if (username.value === "") {
        showError("Por favor, ingresa un nombre de usuario.", username);
        isValid = false;
    }

    if (email.value === "") {
        showError("Por favor, ingresa un correo electrónico.", email);
        isValid = false;
    } else if (!emailPattern.test(email.value)) {
        showError("El correo electrónico no es válido.", email);
        isValid = false;
    }

    if (password.value === "") {
        showError("Por favor, ingresa una contraseña.", password);
        isValid = false;
    } else if (password.value.length < 8) {
        showError("La contraseña debe tener al menos 8 caracteres.", password);
        isValid = false;
    }

    return isValid;
}

// Validación en tiempo real
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('input');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            // Remover mensaje de error si existe
            const error = this.parentElement.querySelector('.error-message');
            if (error) {
                error.remove();
            }
            this.classList.remove('input-error');
        });
    });
});
    </script>
</head>
<body>
    <div class="container">
        <h2>Registro</h2>


        
        <form name="registerForm" action="../controllers/register.php" method="post" onsubmit="return validateForm()">
            <div class="form-group">
                <input type="text" name="username" placeholder="Usuario" required>
            </div>
            
            <div class="form-group">
    <input type="email" name="email" placeholder="Correo Electrónico" required>
    <!-- Mostrar error específico del correo aquí -->
    <?php if (isset($_SESSION['error_email'])): ?>
        <div class="error-message"><?php echo $_SESSION['error_email']; unset($_SESSION['error_email']); ?></div>
    <?php endif; ?>
</div>
            
            <div class="form-group">
                <input type="password" name="password" placeholder="Contraseña" required>
                <?php if (isset($_SESSION['error_password'])): ?>
                    <div class="error-message"><?php echo $_SESSION['error_password']; unset($_SESSION['error_password']); ?></div>
                <?php endif; ?>
            </div>

            <button type="submit">Registrarse</button>
        </form>
        <a href="login.php">Iniciar Sesión</a>
    </div>
</body>
</html>
