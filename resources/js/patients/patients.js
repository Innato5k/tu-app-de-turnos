// =====================================================================
// 1. CONFIGURACIÓN Y VARIABLES GLOBALES
// =====================================================================

const API_PATIENTS_URL = '/api/patients'; // URL base de tu API para pacientes
const REDIRECT_LOGIN_URL = '/login'; // Ruta a la página de inicio de sesión (web)
const EDIT_PATIENT_BASE_URL = '/patients'; // Base para la URL de edición (ej. /patients/1/edit)

// Elementos del DOM
const patientsTableBody = document.getElementById('patientsTableBody');
const patientsPagination = document.getElementById('patientsPagination');
const searchInput = document.getElementById('searchInput');
const searchButton = document.getElementById('searchButton');

// Variables para el modal de confirmación de eliminación
const deleteConfirmationModalElement = document.getElementById('deleteConfirmationModal');
let deleteConfirmationModal; // Se inicializará como un objeto Bootstrap Modal
const confirmDeleteButton = document.getElementById('confirmDeleteButton');
const modalPatientIdSpan = document.getElementById('modalPatientId');
const deleteSpinner = document.getElementById('deleteSpinner');
let patientToDeleteId = null; // Almacena el ID del paciente seleccionado para eliminar

// =====================================================================
// 2. FUNCIONES DE UTILIDAD
// =====================================================================

/**
 * Obtiene el token JWT del almacenamiento local.
 * @returns {string|null} El token JWT o null si no existe.
 */
function getAuthToken() {
    return localStorage.getItem('auth_token');
}

/**
 * Redirige al usuario a la página de inicio de sesión y limpia el token.
 * @param {string} message Mensaje a mostrar al usuario.
 */
function redirectToLogin(message) {
    alert(message); // Considera reemplazar esto con un modal personalizado para mejor UX
    localStorage.removeItem('auth_token');
    localStorage.removeItem('token_type');
    localStorage.removeItem('user_info');
    window.location.href = REDIRECT_LOGIN_URL;
}

// =====================================================================
// 3. FUNCIONES PRINCIPALES (CARGA Y RENDERIZADO DE PACIENTES)
// =====================================================================

/**
 * Carga los pacientes desde la API y actualiza la tabla y la paginación.
 * @param {number} page Número de página a cargar.
 */
async function fetchPatients(page = 1, searchQuery = '') { // MODIFICADO: Añadido searchQuery como parámetro
    const token = getAuthToken();

    if (!token) {
        redirectToLogin('No autenticado. Por favor, inicia sesión.');
        return;
    }

    // Muestra el estado de carga en la tabla
    patientsTableBody.innerHTML = `
        <tr>
            <td colspan="5" class="text-center py-4">
                <div class="spinner-border text-info" role="status">
                    <span class="visually-hidden">Cargando pacientes...</span>
                </div>
                <p class="text-muted mt-2">Cargando pacientes...</p>
            </td>
        </tr>
    `;
    patientsPagination.innerHTML = ''; // Limpia la paginación anterior

    try {
        // MODIFICADO: Añadido el parámetro 'search' a la URL si searchQuery no está vacío
        let url = `${API_PATIENTS_URL}?page=${page}`;
        if (searchQuery) {
            url += `&search=${encodeURIComponent(searchQuery)}`;
        }

        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}` // Envía el token JWT en el encabezado
            }
        });

        if (response.status === 401 || response.status === 403) {
            redirectToLogin('Sesión expirada o no autorizada. Por favor, inicia sesión de nuevo.');
            return;
        }

        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || 'Error al cargar pacientes.');
        }

        const data = await response.json();
        populateTable(data.data); // Asume que la respuesta de la API tiene los datos en 'data.data'
        updatePagination(data); // Actualiza los controles de paginación
    } catch (error) {
        console.error('Error al cargar pacientes:', error);
        patientsTableBody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center py-4 text-danger">
                    Error al cargar los pacientes. Por favor, inténtalo de nuevo.
                </td>
            </tr>
        `;
    }
}

/**
 * Rellena la tabla con los datos de los pacientes.
 * @param {Array<Object>} patients Array de objetos paciente.
 */
function populateTable(patients) {
    patientsTableBody.innerHTML = ''; // Limpia las filas existentes

    if (!Array.isArray(patients) || patients.length === 0) {
        patientsTableBody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center py-4 text-muted">No hay pacientes registrados.</td>
            </tr>
        `;
        return;
    }

    patients.forEach(patient => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="py-3 px-4 text-muted">${patient.id || ''}</td>
            <td class="py-3 px-4 text-dark">${patient.name || ''} ${patient.last_name || ''}</td>
            <td class="py-3 px-4 text-muted">${patient.email || ''}</td>
            <td class="py-3 px-4 text-muted">${patient.phone || ''}</td>
            <td class="py-3 px-4 text-center">
                <a href="${EDIT_PATIENT_BASE_URL}/${patient.id}/edit" class="btn btn-sm btn-info text-white  me-2" >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                        <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325"/>
                    </svg>
                </a>
                <button type="button" class="btn btn-sm btn-danger text-white fw-semibold" data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal" data-patient-id="${patient.id}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                        <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                    </svg>
                </button>
            </td>
        `;
        patientsTableBody.appendChild(row);
    });

    // Añade el event listener para los botones de eliminar después de que se hayan agregado al DOM
    // Usamos delegación de eventos para manejar botones creados dinámicamente
    patientsTableBody.querySelectorAll('button[data-bs-target="#deleteConfirmationModal"]').forEach(button => {
        button.addEventListener('click', (event) => {
            patientToDeleteId = event.currentTarget.dataset.patientId;
            modalPatientIdSpan.textContent = patientToDeleteId;
        });
    });
}

/**
 * Actualiza los controles de paginación.
 * Esta es una función placeholder. Deberías implementarla según la estructura
 * de tu respuesta de API (ej. data.links, data.current_page, data.last_page).
 * @param {Object} paginationData Objeto con la información de paginación de la API.
 */
function updatePagination(paginationData) {
    patientsPagination.innerHTML = ''; // Limpia la paginación existente

    // Ejemplo básico de paginación (ajusta según tu API)
    const currentPage = paginationData.current_page;
    const lastPage = paginationData.last_page;

    if (lastPage > 1) {
        // Botón "Anterior"
        const prevItem = document.createElement('li');
        prevItem.classList.add('page-item');
        if (!paginationData.prev_page_url) {
            prevItem.classList.add('disabled');
        }
        prevItem.innerHTML = `<a class="page-link" href="#" data-page="${currentPage - 1}">Anterior</a>`;
        patientsPagination.appendChild(prevItem);

        // Números de página
        for (let i = 1; i <= lastPage; i++) {
            const pageItem = document.createElement('li');
            pageItem.classList.add('page-item');
            if (i === currentPage) {
                pageItem.classList.add('active');
            }
            pageItem.innerHTML = `<a class="page-link" href="#" data-page="${i}">${i}</a>`;
            patientsPagination.appendChild(pageItem);
        }

        // Botón "Siguiente"
        const nextItem = document.createElement('li');
        nextItem.classList.add('page-item');
        if (!paginationData.next_page_url) {
            nextItem.classList.add('disabled');
        }
        nextItem.innerHTML = `<a class="page-link" href="#" data-page="${currentPage + 1}">Siguiente</a>`;
        patientsPagination.appendChild(nextItem);

        // Agrega event listener a los enlaces de paginación
        patientsPagination.querySelectorAll('.page-link').forEach(link => {
            link.addEventListener('click', (event) => {
                event.preventDefault();
                const page = parseInt(event.target.dataset.page);
                if (!isNaN(page) && page > 0 && page <= lastPage) {
                    fetchPatients(page);
                }
            });
        });
    }
}

/**
 * Elimina un paciente a través de la API.
 * @param {string} patientId El ID del paciente a eliminar.
 */
async function deletePatient(patientId) {
    const token = getAuthToken();

    if (!token) {
        redirectToLogin('No autenticado. Por favor, inicia sesión.');
        return;
    }

    // Muestra el spinner y deshabilita el botón de confirmación
    deleteSpinner.classList.remove('hidden');
    confirmDeleteButton.disabled = true;

    try {
        const response = await fetch(`${API_PATIENTS_URL}/${patientId}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}` // Envía el token JWT
            }
        });

        if (response.status === 401 || response.status === 403) {
            redirectToLogin('Sesión expirada o no autorizada. Por favor, inicia sesión de nuevo.');
            return;
        }

        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || 'Error al eliminar el paciente.');
        }

        // Si la eliminación fue exitosa, oculta el modal y recarga la lista de pacientes
        deleteConfirmationModal.hide();
        document.body.classList.remove('modal-open');
        const backdrops = document.getElementsByClassName('modal-backdrop');
        while(backdrops.length > 0){
            backdrops[0].parentNode.removeChild(backdrops[0]);
        }
        alert('Paciente eliminado exitosamente.'); // Considera un modal de éxito
        fetchPatients(); // Recarga la lista para reflejar el cambio

    } catch (error) {
        console.error('Error al eliminar paciente:', error);
        alert(`Error al eliminar el paciente: ${error.message}`); // Muestra el error al usuario
    } finally {
        // Oculta el spinner y habilita el botón de confirmación
        deleteSpinner.classList.add('hidden');
       
        confirmDeleteButton.disabled = false;
        patientToDeleteId = null; // Limpia el ID del paciente a eliminar
    }
}

// =====================================================================
// 4. INICIALIZACIÓN (DOMContentLoaded)
// =====================================================================

document.addEventListener('DOMContentLoaded', () => {
    // Inicializa el modal de Bootstrap
    if (deleteConfirmationModalElement) {
        deleteConfirmationModal = new bootstrap.Modal(deleteConfirmationModalElement);
    } else {
        console.error('El elemento del modal de confirmación (deleteConfirmationModal) no se encontró en el DOM.');
        // Considera una alternativa o un mensaje de error visible para el usuario
    }

    // Manejador del botón de confirmación dentro del modal
    if (confirmDeleteButton) {
        confirmDeleteButton.addEventListener('click', () => {
            if (patientToDeleteId) {
                deletePatient(patientToDeleteId);
            }
        });
    } else {
        console.error('El botón de confirmación de eliminación (confirmDeleteButton) no se encontró en el DOM.');
    }
    if (searchButton) {
        searchButton.addEventListener('click', () => {
            const query = searchInput.value.trim();
            fetchPatients(1, query); // Inicia la búsqueda desde la primera página con la query
        });
    }

    // NUEVO: Manejador para buscar al presionar "Enter" en el campo de búsqueda
    if (searchInput) {
        searchInput.addEventListener('keypress', (event) => {
            if (event.key === 'Enter') {
                const query = searchInput.value.trim();
                fetchPatients(1, query); // Inicia la búsqueda desde la primera página con la query
            }
        });
    }

    // Carga los pacientes al cargar la página
    fetchPatients();
});
