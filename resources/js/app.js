import './bootstrap'; // El que trae Laravel con Axios
import * as bootstrap from 'bootstrap'; // Importamos la librería que instalaste con Sail
window.bootstrap = bootstrap; // LA CLAVE: Lo hacemos global para pacientes.js e index.jsimport './bootstrap';
import 'bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap-icons/font/bootstrap-icons.css';

document.addEventListener('DOMContentLoaded', () => {
    const authToken = localStorage.getItem('auth_token');
    const userInfo = localStorage.getItem('user_info');
    const userNameDisplay = document.getElementById('userNameDisplay');
    const logoutButton = document.getElementById('logoutButton');
    const loginLink = document.getElementById('loginLink');
    const linkInicio = document.getElementById('linkInicio');    
    const linkUsuarios = document.getElementById('linkUsuarios');
    const linkHoraiosProfesional = document.getElementById('linkHoraiosProfesional');
    const linkMisTurnos = document.getElementById('linkMisTurnos');
    const linkPacientes = document.getElementById('linkPacientes');
    const linkContactos = document.getElementById('linkContactos');

    if (authToken && userInfo) {
        // Si hay un token y info de usuario, el usuario está "logueado" en el front-end
        try {
            const user = JSON.parse(userInfo);
            userNameDisplay.textContent = `Hola, ${user.full_name}!`;
            userNameDisplay.classList.remove('d-none'); // Muestra el nombre
            logoutButton.classList.remove('d-none'); // Muestra el botón de logout
            loginLink.classList.add('d-none'); // Oculta el enlace de login
            linkInicio.classList.remove('d-none'); // Oculta el menú de navegación no autenticado
            if (user.role === 'admin') { // TODO: revisar si esto es necesario o no, y si es así, ver de ocultar este link para profesionales   
                linkUsuarios.classList.remove('d-none');//TODO: ver de ocultar este link para profesionales
            }
            linkHoraiosProfesional.classList.remove('d-none');
            linkMisTurnos.classList.remove('d-none');
            linkPacientes.classList.remove('d-none');
            linkContactos.classList.remove('d-none');
        } catch (e) {
            console.error("Error al parsear user_info:", e);
            userNameDisplay.classList.add('d-none');
            logoutButton.classList.add('d-none');
            loginLink.classList.remove('d-none');
            linkInicio.classList.add('d-none');
            linkUsuarios.classList.add('d-none');
            linkHoraiosProfesional.classList.add('d-none');
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
        linkUsuarios.classList.add('d-none');
        linkHoraiosProfesional.classList.add('d-none');
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



document.addEventListener("DOMContentLoaded", function() {
    document.body.classList.add('loaded');
});