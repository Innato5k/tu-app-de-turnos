@extends('layouts.app')

@section('title', 'Listado de Pacientes')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fs-3 fw-bold text-dark">Listado de Pacientes</h1>
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
    <nav aria-label="Paginación de pacientes" class="mt-4">
        <ul class="pagination justify-content-center" id="patientsPagination">

        </ul>
    </nav>
</div>
@endsection
@section('scripts')
<script type="module" src="{{ asset('js/patients/patients.js') }}">

</script>
@endsection