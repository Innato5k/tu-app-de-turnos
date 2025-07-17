
@extends('layouts.app')

@section('title', 'Listado de Pacientes')

@section('content')
    <div class="container py-4">
       
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fs-3 fw-bold text-dark">Listado de Pacientes</h1>
            
            <a href="#" class="btn btn-success fw-semibold">
                + Crear Paciente
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">               
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th scope="col" class="py-3 px-4 text-dark">ID</th>
                                <th scope="col" class="py-3 px-4 text-dark">Nombre Completo</th>
                                <th scope="col" class="py-3 px-4 text-dark">Email</th>
                                <th scope="col" class="py-3 px-4 text-dark">Teléfono</th>
                                <th scope="col" class="py-3 px-4 text-dark text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="patientsTableBody">
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <div class="spinner-border text-info" role="status">
                                        <span class="visually-hidden">Cargando pacientes...</span>
                                    </div>
                                    <p class="text-muted mt-2">Cargando pacientes...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Controles de paginación --}}
        <nav aria-label="Paginación de pacientes" class="mt-4">
            <ul class="pagination justify-content-center" id="patientsPagination">
                {{-- Los enlaces de paginación se insertarán aquí dinámicamente con JavaScript --}}
            </ul>
        </nav>
    </div>
@endsection
@section('scripts')
<script type="module">
        // URL de tu API para obtener el listado de pacientes
        const API_PATIENTS_URL = '/api/patients'; // ¡Asegúrate de que esta sea la URL correcta de tu API!
        // CORRECCIÓN: Usar la ruta de Laravel para el login de la vista Blade
        const REDIRECT_LOGIN_URL = '{{ route('login') }}'; // Ruta de login para redireccionar en caso de token inválido
        const CREATE_PATIENT_URL = '{{ route('patients.create') }}'; // Ruta para crear un nuevo paciente
        const EDIT_PATIENT_BASE_URL = '{{ url('/patients') }}'; // Base para la URL de edición (ej. /patients/1/edit)

        const patientsTableBody = document.getElementById('patientsTableBody');
        const patientsPagination = document.getElementById('patientsPagination');

        // Función para obtener el token JWT del localStorage
        function getAuthToken() {
            return localStorage.getItem('auth_token');
        }

        // Función para cargar los pacientes desde la API
        async function fetchPatients(page = 1) {
            const token = getAuthToken();

            // Si no hay token, redirige al login
            if (!token) {
                alert('No autenticado. Por favor, inicia sesión.'); // Usar modal personalizado en producción
                window.location.href = REDIRECT_LOGIN_URL;
                return;
            }

            // Muestra el estado de carga
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
                const ITEMS_PER_PAGE = 10; // Número de pacientes por página

                const response = await fetch(`${API_PATIENTS_URL}?page=${page}&per_page=${ITEMS_PER_PAGE}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}` // Envía el token JWT
                    }
                });

                if (response.status === 401 || response.status === 403) {
                    // Token inválido o expirado, redirigir al login
                    alert('Sesión expirada o no autorizada. Por favor, inicia sesión de nuevo.'); // Usar modal personalizado
                    localStorage.removeItem('auth_token');
                    localStorage.removeItem('token_type');
                    localStorage.removeItem('user_info');
                    window.location.href = REDIRECT_LOGIN_URL;
                    return;
                }

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Error al cargar pacientes.');
                }

                const data = await response.json();
                populateTable(data.data); // 'data.data' si tu API devuelve paginación con 'data' anidado
                // CORRECCIÓN: Pasa el objeto 'data' completo a updatePagination
                updatePagination(data);

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

        // Función para poblar la tabla con los datos de los pacientes
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
                        <a href="${EDIT_PATIENT_BASE_URL}/${patient.id}/edit" class="btn btn-sm btn-info text-white fw-semibold">
                            ✎ Editar
                        </a>
                    </td>
                `;
                patientsTableBody.appendChild(row);
            });
        }

        // Función para actualizar los enlaces de paginación
        function updatePagination(paginationData) {
            patientsPagination.innerHTML = ''; // Limpia la paginación existente

            // Verifica si paginationData y sus propiedades existen en el nivel superior
            // Las propiedades current_page y last_page deben estar directamente en paginationData
            if (!paginationData || typeof paginationData.current_page === 'undefined' || typeof paginationData.last_page === 'undefined') {
                console.warn('Datos de paginación incompletos o incorrectos:', paginationData);
                patientsPagination.classList.add('d-none'); // Oculta la paginación si los datos no son válidos
                return;
            }

            const currentPage = paginationData.current_page;
            const lastPage = paginationData.last_page;
            const linksArray = Array.isArray(paginationData.links) ? paginationData.links : [];

            // Obtener URLs de los links 'prev' y 'next' del array de links
            // Los links de Laravel suelen tener el formato [{url: null, label: "&laquo; Anterior", active: false}, ...]
            // El primer elemento es "Anterior", el último es "Siguiente"
            const prevLink = linksArray.find(link => link.label && (link.label.includes('Anterior') || link.label.includes('Previous')));
            const nextLink = linksArray.find(link => link.label && (link.label.includes('Siguiente') || link.label.includes('Next')));

            const prevPageUrl = prevLink ? prevLink.url : null;
            const nextPageUrl = nextLink ? nextLink.url : null;

            // Botón "Anterior"
            const prevItem = document.createElement('li');
            prevItem.classList.add('page-item');
            if (!prevPageUrl) {
                prevItem.classList.add('disabled');
            }
            prevItem.innerHTML = `<a class="page-link" href="#" data-page="${currentPage - 1}" tabindex="-1" aria-disabled="${!prevPageUrl}">Anterior</a>`;
            patientsPagination.appendChild(prevItem);

            // Números de página
            linksArray.forEach(link => {
                if (link.label && (link.label.includes('Anterior') || link.label.includes('Previous') || link.label.includes('Siguiente') || link.label.includes('Next'))) {
                    return;
                }
                const pageNumber = parseInt(link.label);

                const pageItem = document.createElement('li');
                pageItem.classList.add('page-item');
                if (link.active) {
                    pageItem.classList.add('active');
                }
                pageItem.innerHTML = `<a class="page-link" href="#" data-page="${pageNumber}">${pageNumber}</a>`;
                patientsPagination.appendChild(pageItem);
            });

            // Botón "Siguiente"
            const nextItem = document.createElement('li');
            nextItem.classList.add('page-item');
            if (!nextPageUrl) {
                nextItem.classList.add('disabled');
            }
            nextItem.innerHTML = `<a class="page-link" href="#" data-page="${currentPage + 1}">Siguiente</a>`;
            patientsPagination.appendChild(nextItem);

            // Asegura que la paginación sea visible si hay páginas
            if (lastPage > 1) {
                patientsPagination.classList.remove('d-none');
            } else {
                patientsPagination.classList.add('d-none');
            }

            // Añadir event listeners a los nuevos enlaces de paginación
            patientsPagination.querySelectorAll('.page-link').forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const page = parseInt(e.target.dataset.page);
                    if (!isNaN(page) && page > 0 && page <= lastPage && !e.target.closest('.page-item').classList.contains('disabled')) {
                        fetchPatients(page);
                    }
                });
            });
        }

        // Cargar los pacientes al cargar la página
        document.addEventListener('DOMContentLoaded', () => {
            fetchPatients();
        });
    </script>
@endsection