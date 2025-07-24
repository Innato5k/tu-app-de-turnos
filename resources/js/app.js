import './bootstrap';
// Importa los estilos de Bootstrap Icons
import 'bootstrap-icons/font/bootstrap-icons.css';

document.addEventListener('DOMContentLoaded', () => {
    const authToken = localStorage.getItem('auth_token');
    const userInfo = localStorage.getItem('user_info');
    const userNameDisplay = document.getElementById('userNameDisplay');
    const logoutButton = document.getElementById('logoutButton');
    const loginLink = document.getElementById('loginLink');
    const linkInicio = document.getElementById('linkInicio');
    const linkMisTurnos = document.getElementById('linkMisTurnos');
    const linkPacientes = document.getElementById('linkPacientes');
    const linkContactos = document.getElementById('linkContactos');

    if (authToken && userInfo) {
        // Si hay un token y info de usuario, el usuario está "logueado" en el front-end
        try {
            const user = JSON.parse(userInfo);
            userNameDisplay.textContent = `Hola, ${user.name}!`;
            userNameDisplay.classList.remove('d-none'); // Muestra el nombre
            logoutButton.classList.remove('d-none'); // Muestra el botón de logout
            loginLink.classList.add('d-none'); // Oculta el enlace de login
            linkInicio.classList.remove('d-none'); // Oculta el menú de navegación no autenticado
            linkMisTurnos.classList.remove('d-none');
            linkPacientes.classList.remove('d-none');
            linkContactos.classList.remove('d-none');
        } catch (e) {
            console.error("Error al parsear user_info:", e);
            userNameDisplay.classList.add('d-none');
            logoutButton.classList.add('d-none');
            loginLink.classList.remove('d-none');
            linkInicio.classList.add('d-none');
            linkMisTurnos.classList.add('d-none');
            linkPacientes.classList.add('d-none');
            linkContactos.classList.add('d-none');
        }
    } else {
        // Si no hay token, el usuario no está logueado
        userNameDisplay.classList.add('d-none');
        logoutButton.classList.add('d-none');
        loginLink.classList.remove('d-none');
        linkInicio.classList.add('d-none');
        linkMisTurnos.classList.add('d-none');
        linkPacientes.classList.add('d-none');
        linkContactos.classList.add('d-none');
    }

    // Manejador del botón de Cerrar Sesión
    logoutButton.addEventListener('click', () => {
        // Elimina el token y la info del usuario del localStorage
        localStorage.removeItem('auth_token');
        localStorage.removeItem('token_type');
        localStorage.removeItem('user_info');

        // Redirige a la página de login (o a la raíz si el login es la raíz)
        window.location.href = "/login";
    });
});