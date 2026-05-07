import './bootstrap';
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

import 'bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap-icons/font/bootstrap-icons.css';

document.addEventListener('DOMContentLoaded', () => {
    const authToken = localStorage.getItem('auth_token');
    const userInfo = localStorage.getItem('user_info');
    
    // Selectores
    const elements = {
        userNameDisplay: document.getElementById('userNameDisplay'),
        logoutButton: document.getElementById('logoutButton'),
        loginLink: document.getElementById('loginLink'),
        userDropdown: document.getElementById('userDropdownItem'),
        links: {
            inicio: document.getElementById('linkInicio'),
            usuarios: document.getElementById('linkUsuarios'),
            horarios: document.getElementById('linkHorariosProfesional'),
            turnos: document.getElementById('linkMisTurnos'),
            pacientes: document.getElementById('linkPacientes'),
            contactos: document.getElementById('linkContactos')
        }
    };

    if (authToken && userInfo) {
        try {
            const user = JSON.parse(userInfo);
            
            // 1. Datos de sesión
            if (elements.userNameDisplay) {
                elements.userNameDisplay.textContent = `Hola, ${user.full_name}!`;
                elements.userNameDisplay.classList.remove('d-none');
            }
            
            elements.userDropdown?.classList.remove('d-none');
            elements.logoutButton?.classList.remove('d-none');
            elements.loginLink?.classList.add('d-none');

            // 2. Control de visibilidad por Rol
            elements.links.inicio?.classList.remove('d-none');
            elements.links.horarios?.classList.remove('d-none');
            elements.links.turnos?.classList.remove('d-none');
            elements.links.pacientes?.classList.remove('d-none');
            elements.links.contactos?.classList.remove('d-none');

            // SOLO ADMIN: CRUD Usuarios
            if (user.role === 'admin') {
                elements.links.usuarios?.classList.remove('d-none');
            } else {
                elements.links.usuarios?.classList.add('d-none');
            }

        } catch (e) {
            console.error("Error al parsear user_info:", e);
            renderGuestUI(elements);
        }
    } else {
        renderGuestUI(elements);
    }

    // 3. Indicador de Pantalla Activa (Active State)
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.nav-link');
    
    navLinks.forEach(link => {
        // Normalizamos los paths para comparar
        const linkPath = link.getAttribute('href');
        if (linkPath === currentPath) {
            link.classList.add('active', 'fw-bold');
            link.classList.add('text-info');
        } else {
            link.classList.remove('active', 'fw-bold');            
            link.classList.remove('text-info');
        }
    });

    // Manejador de Cerrar Sesión
    elements.logoutButton?.addEventListener('click', (e) => {
        e.preventDefault();
        localStorage.removeItem('auth_token');
        localStorage.removeItem('token_type');
        localStorage.removeItem('user_info');
        window.location.href = "/login";
    });
});

function renderGuestUI(el) {
    el.userNameDisplay?.classList.add('d-none');
    el.userDropdown?.classList.add('d-none');
    el.logoutButton?.classList.add('d-none');
    el.loginLink?.classList.remove('d-none');
    
    // Ocultar todos los links protegidos
    Object.values(el.links).forEach(link => link?.classList.add('d-none'));
}

document.addEventListener("DOMContentLoaded", function() {
    document.body.classList.add('loaded');
});