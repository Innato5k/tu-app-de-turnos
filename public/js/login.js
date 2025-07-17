const loginForm = document.getElementById('loginForm');
const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');
const appMessage = document.getElementById('appMessage');
const loginButton = document.getElementById('loginButton');
const tipContent = document.getElementById('tipContent');
const tipLoading = document.getElementById('tipLoading');

const API_LOGIN_URL = '/api/auth/login'; 
const REDIRECT_URL = '/dashboard';

// Manejador de envío del formulario
loginForm.addEventListener('submit', async (e) => {
    e.preventDefault(); 

    const email = emailInput.value;
    const password = passwordInput.value;

    // Validación básica en el front-end
    if (!email || !password) {
        appMessage.textContent = 'Por favor, introduce tu correo y contraseña.';
        appMessage.classList.remove('text-success');
        appMessage.classList.add('text-danger');
        return;
    }

    // Deshabilita el botón y muestra un mensaje de carga
    loginButton.disabled = true;
    loginButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Iniciando sesión...'; // Spinner de Bootstrap
    appMessage.textContent = ''; // Limpia mensajes anteriores
    appMessage.classList.remove('text-success', 'text-danger');

    try {
        // Realiza la llamada a tu API de login
        const response = await fetch(API_LOGIN_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json', // Indica que esperas una respuesta JSON
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '' // Si usas CSRF para API
            },
            body: JSON.stringify({ email, password })
        });

        const data = await response.json(); // Parsea la respuesta JSON

        if (response.ok) { // response.ok es true si el status es 2xx
            // Login exitoso
            appMessage.textContent = 'Inicio de sesión exitoso. Redireccionando...';
            appMessage.classList.remove('text-danger');
            appMessage.classList.add('text-success');

            // Almacena el token de acceso (ej. en localStorage)
            if (data.access_token) {
                localStorage.setItem('auth_token', data.access_token);
                localStorage.setItem('token_type', data.token_type || 'Bearer');
                // Opcional: guardar información del usuario
                if (data.user) {
                    localStorage.setItem('user_info', JSON.stringify(data.user));
                }
            }

            // Redirecciona a la URL deseada
            setTimeout(() => {
                window.location.href = REDIRECT_URL;
            }, 500); // Pequeño retraso para ver el mensaje
        } else {
            // Login fallido (ej. 401 Unauthorized, 422 Unprocessable Entity)
            const errorMessage = data.message || 'Credenciales inválidas.';
            appMessage.textContent = `Error: ${errorMessage}`;
            appMessage.classList.remove('text-success');
            appMessage.classList.add('text-danger');
        }
    } catch (error) {
        appMessage.textContent = 'Ocurrió un error de red. Inténtalo de nuevo.';
        appMessage.classList.remove('text-success');
        appMessage.classList.add('text-danger');
        console.error('Error al llamar a la API de login:', error);
    } finally {
        // Habilita el botón de nuevo y restaura su texto original
        loginButton.disabled = false;
        loginButton.textContent = 'Iniciar Sesión';
    }
});


