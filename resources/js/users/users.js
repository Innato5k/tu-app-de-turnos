// =====================================================================
// 1. CONFIGURACIÓN, SEGURIDAD Y VARIABLES GLOBALES
// =====================================================================

const API_USERS_URL = '/api/users';
const REDIRECT_LOGIN_URL = '/login';
const EDIT_USER_BASE_URL = '/users';

// --- BLOQUEO DE SEGURIDAD (Analista Level) ---
const userInfo = JSON.parse(localStorage.getItem('user_info'));
if (!userInfo || userInfo.role !== 'admin') {
    window.location.href = '/dashboard';
}

// Elementos del DOM
const usersTableBody = document.getElementById('usersTableBody');
const usersPagination = document.getElementById('usersPagination');
const searchInput = document.getElementById('searchInput');
const searchButton = document.getElementById('searchButton');

// Variables para el modal de estado (Alta/Baja)
const statusModalElement = document.getElementById('statusModal');
let statusModal;
const confirmStatusButton = document.getElementById('confirmStatusButton');
const modalUserIdSpan = document.getElementById('modalUserId');
const statusSpinner = document.getElementById('statusSpinner');
let userToToggleId = null;
let isUserInactive = false;

//variables para el modal de edición/creación
const userModal = new bootstrap.Modal(document.getElementById('userModal'));
const userForm = document.getElementById('userForm');

// =====================================================================
// 2. FUNCIONES DE UTILIDAD
// =====================================================================

function getAuthToken() {
    return localStorage.getItem('auth_token');
}

function redirectToLogin(message) {
    alert(message);
    localStorage.clear();
    window.location.href = REDIRECT_LOGIN_URL;
}

// =====================================================================
// 3. FUNCIONES PRINCIPALES
// =====================================================================

async function fetchUsers(page = 1, searchQuery = '') {
    const token = getAuthToken();
    if (!token) {
        redirectToLogin('No autenticado.');
        return;
    }

    usersTableBody.innerHTML = `
        <tr>
            <td colspan="6" class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="text-muted mt-2">Cargando personal...</p>
            </td>
        </tr>
    `;

    try {
        let url = `${API_USERS_URL}?page=${page}`;
        if (searchQuery) url += `&search=${encodeURIComponent(searchQuery)}`;

        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`
            }
        });

        if (response.status === 401 || response.status === 403) {
            redirectToLogin('Sesión expirada o no autorizada.');
            return;
        }

        const data = await response.json();
        populateTable(data.data);
        updatePagination(data); 
    } catch (error) {
        console.error('Error:', error);
        usersTableBody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-danger">Error al cargar datos.</td></tr>';
    }
}

function populateTable(users) {
    usersTableBody.innerHTML = '';

    if (!Array.isArray(users) || users.length === 0) {
        usersTableBody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">No se encontraron usuarios.</td></tr>';
        return;
    }

    users.forEach(user => {
        const isActive = user.is_active;
        const row = document.createElement('tr');

        row.innerHTML = `
            <td class="py-3 px-4 text-muted">${user.id}</td>
            <td class="py-3 px-4">
                <div class="fw-bold text-dark">${user.full_name}</div>
                <small class="text-muted">${user.email}</small>
            </td>
             <td class="py-3 px-4 text-muted">${user.cuil || 'N/A'}</td>
            <td class="py-3 px-4 text-muted">${user.phone || 'N/A'}</td>
            <td class="py-3 px-4">
                <span class="badge ${user.role === 'admin' ? 'bg-primary' : 'bg-info'} text-white">
                    ${user.role}
                </span>
            </td>
            <td class="py-3 px-4 text-center">
                <span class="badge rounded-pill ${isActive ? 'bg-success': 'bg-danger' }">
                    ${isActive ? 'Activo' : 'Inactivo'}
                </span>
            </td>
            <td class="py-3 px-4 text-center">
                <a href="${EDIT_USER_BASE_URL}/${user.id}/edit" class="btn btn-sm btn-outline-info me-2 'opacity-100'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16"><path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325"/></svg>
                </a>
            </td>
        `;
        usersTableBody.appendChild(row);
    });
}

/**
 * Prepara el modal para dar de baja o alta (restaurar)
 */
window.prepareStatusModal = function (id, inactive) {
    userToToggleId = id;
    isUserInactive = inactive;
    modalUserIdSpan.textContent = id;

    const header = document.getElementById('statusModalHeader');
    const message = document.getElementById('statusModalMessage');
    const btn = confirmStatusButton;

    if (inactive) {
        header.className = 'modal-header bg-success text-white';
        message.innerHTML = '¿Deseas <strong>reactivar</strong> a este usuario para que pueda acceder al sistema?';
        btn.className = 'btn btn-success';
        btn.textContent = 'Confirmar Alta';
    } else {
        header.className = 'modal-header bg-danger text-white';
        message.innerHTML = '¿Estás seguro de <strong>dar de baja</strong> a este usuario?';
        btn.className = 'btn btn-danger';
        btn.textContent = 'Confirmar Baja';
    }
    statusModal.show();
};

async function toggleUserStatus() {
    const token = getAuthToken();
    statusSpinner.classList.remove('d-none');
    confirmStatusButton.disabled = true;

    // Si está inactivo, usamos RESTORE (POST), si está activo usamos DELETE
    const method = isUserInactive ? 'POST' : 'DELETE';
    const url = isUserInactive ? `${API_USERS_URL}/${userToToggleId}/restore` : `${API_USERS_URL}/${userToToggleId}`;

    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`
            }
        });

        if (response.ok) {
            statusModal.hide();
            fetchUsers(); // Recarga la tabla
        } else {
            alert('Error al procesar la solicitud.');
        }
    } catch (error) {
        console.error(error);
    } finally {
        statusSpinner.classList.add('d-none');
        confirmStatusButton.disabled = false;
    }
}

// ... (Acá iría tu misma lógica de updatePagination que ya tenés) ...

function updatePagination(paginationData) {
    usersPagination.innerHTML = ''; // Limpia la paginación existente

    // Ejemplo básico de paginación (ajusta según tu API)
    const currentPage = paginationData.meta.current_page;
    const lastPage = paginationData.meta.last_page;

    if (lastPage > 1) {
        // Botón "Anterior"
        const prevItem = document.createElement('li');
        prevItem.classList.add('page-item');
        if (!paginationData.links.prev) {
            prevItem.classList.add('disabled');
        }
        prevItem.innerHTML = `<a class="page-link" href="#" data-page="${currentPage - 1}">Anterior</a>`;
        usersPagination.appendChild(prevItem);

        // Números de página
        for (let i = 1; i <= lastPage; i++) {
            const pageItem = document.createElement('li');
            pageItem.classList.add('page-item');
            if (i === currentPage) {
                pageItem.classList.add('active');
            }
            pageItem.innerHTML = `<a class="page-link" href="#" data-page="${i}">${i}</a>`;
            usersPagination.appendChild(pageItem);
        }

        // Botón "Siguiente"
        const nextItem = document.createElement('li');
        nextItem.classList.add('page-item');
        if (!paginationData.links.next) {
            nextItem.classList.add('disabled');
        }
        nextItem.innerHTML = `<a class="page-link" href="#" data-page="${currentPage + 1}">Siguiente</a>`;
        usersPagination.appendChild(nextItem);

        // Agrega event listener a los enlaces de paginación
        usersPagination.querySelectorAll('.page-link').forEach(link => {
            link.addEventListener('click', (event) => {
                event.preventDefault();
                const page = parseInt(event.target.dataset.page);
                if (!isNaN(page) && page > 0 && page <= lastPage) {
                    fetchUsers(page);
                }
            });
        });
    }
}

// Función para guardar (Alta o Edición)
userForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(userForm);
    const data = Object.fromEntries(formData);
    const id = document.getElementById('userId').value;

    // Si hay ID, es PUT (edición); si no, es POST (alta)
    const url = id ? `/api/users/${id}` : '/api/users';
    const method = id ? 'PUT' : 'POST';

    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (response.ok) {
            alert(id ? "Usuario actualizado" : "Usuario creado");
            userModal.hide();
            location.reload(); // Recarga simple para ver cambios
        } else {
            alert("Errores: " + Object.values(result.errors).flat().join('\n'));
        }
    } catch (error) {
        console.error("Error en la petición:", error);
    }
});

// Función para abrir el modal en modo EDICIÓN
window.editUser = (user) => {
    document.getElementById('userModalLabel').innerText = "Editar Usuario";
    document.getElementById('userId').value = user.id;
    document.getElementById('userFullName').value = user.full_name;
    document.getElementById('userCuil').value = user.cuil;
    document.getElementById('userEmail').value = user.email;
    document.getElementById('userRole').value = user.role;
    userModal.show();
};
// =====================================================================
// 4. INICIALIZACIÓN
// =====================================================================

document.addEventListener('DOMContentLoaded', () => {
    if (statusModalElement) {
        statusModal = new bootstrap.Modal(statusModalElement);
    }

    if (confirmStatusButton) {
        confirmStatusButton.addEventListener('click', toggleUserStatus);
    }

    if (searchButton) {
        searchButton.addEventListener('click', () => {
            fetchUsers(1, searchInput.value.trim());
        });
    }

    fetchUsers();
});