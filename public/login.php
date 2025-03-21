<!DOCTYPE html>
<html lang="es">
<head>
    <?php session_start(); ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function showError(message, inputElement) {
            const previousError = inputElement.parentElement.querySelector('.error-message');
            if (previousError) {
                previousError.remove();
            }

            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.textContent = message;
            inputElement.parentElement.insertBefore(errorDiv, inputElement.nextSibling);
            
            inputElement.classList.add('input-error');

            setTimeout(() => {
                errorDiv.remove();
                inputElement.classList.remove('input-error');
            }, 5000);
        }

        function validateLoginForm() {
            const form = document.forms["loginForm"];
            const email = form["email"];
            const password = form["password"];
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            let isValid = true;

            document.querySelectorAll('.error-message').forEach(error => error.remove());
            document.querySelectorAll('.input-error').forEach(input => input.classList.remove('input-error'));

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
            }

            return isValid;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('input');
            inputs.forEach(input => {
                input.addEventListener('input', function() {
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
        <h2>Iniciar Sesión</h2>
        <form name="loginForm" action="../controllers/login.php" method="post" onsubmit="return validateLoginForm()">
            
            <div class="form-group">
                <input type="email" name="email" placeholder="Correo Electrónico" required>
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
            <button type="submit">Ingresar</button>
        </form>
        <a href="register.php">Registrarse</a>
    </div>
</body>
</html>
