@extends('layouts.app')

@section('title', 'Gestión de Usuarios')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fs-3 fw-bold text-dark">Gestión de Usuarios</h1>

        <div class="d-flex align-items-center">
            <div class="input-group me-3" style="max-width: 400px;">
                <input type="text" id="searchInput" class="form-control" placeholder="Buscar por nombre, email o CUIL...">
                <button class="btn btn-primary" type="button" id="searchButton">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.085.12c.1.138.204.275.304.414.04.056.08.112.119.168a.75.75 0 0 0 .573.284.75.75 0 0 0 .573-.284c.039-.056.079-.112.119-.168.1-.139.204-.276.304-.414q.041-.06.085-.12zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                    </svg>
                </button>
            </div>

            <a href="{{ route('users.create') }}" class="btn btn-primary fw-semibold">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-person-plus-fill" viewBox="0 0 16 16">
                    <path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
                    <path fill-rule="evenodd" d="M13.5 5a.5.5 0 0 1 .5.5V7h1.5a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0V8h-1.5a.5.5 0 0 1 0-1H13V5.5a.5.5 0 0 1 .5-.5"/>
                </svg>
                <span class="ms-2">Nuevo Usuario</span>
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th scope="col" class="py-3 px-4">ID</th>
                            <th scope="col" class="py-3 px-4">Usuario</th>
                            <th scope="col" class="py-3 px-4">Teléfono</th>
                            <th scope="col" class="py-3 px-4">Rol</th>
                            <th scope="col" class="py-3 px-4 text-center">Estado</th>
                            <th scope="col" class="py-3 px-4 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="usersTableBody">
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="spinner-border text-primary" role="status"></div>
                                <p class="text-muted mt-2">Cargando personal...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Paginación --}}
    <nav aria-label="Paginación de usuarios" class="mt-4">
        <ul class="pagination justify-content-center" id="usersPagination"></ul>
    </nav>

    {{-- Modal de Estado (Alta/Baja) --}}
    <div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div id="statusModalHeader" class="modal-header text-white">
                    <h5 class="modal-title" id="statusModalLabel">Confirmar Acción</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="statusModalMessage" class="fs-5"></p>
                    <p class="text-muted small">ID de Usuario: <span id="modalUserId"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" id="confirmStatusButton" class="btn">
                        <span id="statusSpinner" class="spinner-border spinner-border-sm d-none"></span>
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @vite('resources/js/users/users.js')
@endsection