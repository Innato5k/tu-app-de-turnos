@extends('layouts.app')

@section('title', 'Listado de Pacientes')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fs-3 fw-bold text-dark">Listado de Pacientes</h1>

        <div class=" d-flex justify-content-between align-items-center">
            <!-- Campo de búsqueda -->
            <div class="input-group" style="max-width: 400px;">
                <input type="text" id="searchInput" class="form-control rounded-l-md" placeholder="Buscar pacientes por nombre, apellido o email...">
                <button class="btn btn-primary rounded-r-md" type="button" id="searchButton">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.085.12c.1.138.204.275.304.414.04.056.08.112.119.168a.75.75 0 0 0 .573.284.75.75 0 0 0 .573-.284c.039-.056.079-.112.119-.168.1-.139.204-.276.304-.414q.041-.06.085-.12zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                    </svg>
                    Buscar
                </button>
            </div>


        </div>

        <a href="{{ route('patients.create') }}" class="btn btn-primary fw-semibold">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-person-add" viewBox="0 0 16 16">
                <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7m.5-5v1h1a.5.5 0 0 1 0 1h-1v1a.5.5 0 0 1-1 0v-1h-1a.5.5 0 0 1 0-1h1v-1a.5.5 0 0 1 1 0m-2-6a3 3 0 1 1-6 0 3 3 0 0 1 6 0M8 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4" />
                <path d="M8.256 14a4.5 4.5 0 0 1-.229-1.004H3c.001-.246.154-.986.832-1.664C4.484 10.68 5.711 10 8 10q.39 0 .74.025c.226-.341.496-.65.804-.918Q8.844 9.002 8 9c-5 0-6 3-6 4s1 1 1 1z" />
            </svg>
            <span class="me-2 mx-1">Nuevo Paciente</span>
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
    <div>

        <nav aria-label="Paginación de pacientes" class="mt-4">
            <ul class="pagination justify-content-center" id="patientsPagination">

            </ul>
        </nav>
    </div>
    {{-- Modal de Confirmación de Eliminación --}}
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white rounded-top">
                    <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-dark">¿Estás seguro de que deseas eliminar este paciente?</p>
                    <p class="text-danger fw-bold">¡Esta acción no se puede deshacer y el registro será marcado como eliminado lógicamente!</p>
                    <p class="text-muted small">ID del Paciente: <span id="modalPatientId"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteButton">
                        <span id="deleteSpinner" class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span>
                        Confirmar Eliminación
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
@vite('resources/js/patients/patients.js')
@endsection