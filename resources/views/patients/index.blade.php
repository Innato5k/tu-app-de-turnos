
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
                
            </ul>
        </nav>
    </div>
@endsection
@section('scripts')
<script type="module" src="{{ asset('js/patients/patients.js') }}">
    
    </script>
@endsection