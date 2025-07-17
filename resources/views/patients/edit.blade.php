@extends('layouts.app') {{-- Extiende el layout principal --}}

@section('title', 'Editar Paciente') {{-- Título de la página --}}

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fs-3 fw-bold text-dark">Editar Paciente</h1>
        <a href="{{ route('patients.index') }}" class="btn btn-secondary fw-semibold">
            Volver al Listado
        </a>
    </div>

    <div class="card shadow-sm p-4">
        <div class="card-body">
            <form id="editPatientForm">
                @csrf {{-- Token CSRF para protección, aunque en API con JWT no es estrictamente necesario para el endpoint de API, es buena práctica en el formulario Blade --}}
                @method('PUT') {{-- Método PUT para actualizar recursos --}}
                <div class="mb-3 col-md-1">
                    <label for="patientId" class="form-label text-dark small">ID</label>
                    <input type="text" id="patientId" class="form-control form-control-sm bg-light" readonly disabled>
                </div>
                <div class="row d-flex justify-content-between">
                    <div class="mb-3 col-md-3">
                        <label for="name" class="form-label text-dark small">Nombre</label>
                        <input type="text" id="name" name="name" class="form-control form-control-sm" required>
                    </div>

                    <div class="mb-3 col-md-3">
                        <label for="last_name" class="form-label text-dark small">Apellido</label>
                        <input type="text" id="last_name" name="last_name" class="form-control form-control-sm" required>
                    </div>

                    <div class="mb-3 col-md-2">
                        <label for="cuil" class="form-label text-dark small">CUIL</label>
                        <input type="text" id="cuil" name="cuil" class="form-control form-control-sm" maxlength="13" placeholder="XX-XXXXXXXX-X">
                    </div>

                    <div class="mb-3 col-md-2">
                        <label for="birth_date" class="form-label text-dark small">Fecha de Nacimiento</label>
                        <input type="date" id="birth_date" name="birth_date" class="form-control form-control-sm">
                    </div>

                    <div class="mb-3 col-md-2">
                        <label for="gender" class="form-label text-dark small">Género</label>
                        <select id="gender" name="gender" class="form-select form-select-sm">
                            <option value="" selected disabled>Seleccione una opción</option>
                            <option value="male">Masculino</option>
                            <option value="female">Femenino</option>
                            <option value="other">Otro</option>
                        </select>
                    </div>

                    <div class="row d-flex justify-content-between">
                        <div class="mb-3 col-md-3">
                            <label for="email" class="form-label text-dark small">Correo Electrónico</label>
                            <input type="email" id="email" name="email" class="form-control form-control-sm" required>
                        </div>

                        <div class="mb-3 col-md-3">
                            <label for="phone" class="form-label text-dark small">Teléfono</label>
                            <input type="number" id="phone" name="phone" class="form-control form-control-sm">
                        </div>

                        <div class="mb-3 col-md-3">
                            <label for="phone_opt" class="form-label text-dark small">Teléfono Alternativo</label>
                            <input type="number" id="phone_opt" name="phone_opt" class="form-control form-control-sm">
                        </div>

                        <div class="mb-3 col-md-3">
                            <label for="medical_coverage" class="form-label text-dark small">Obra Social</label>
                            <input type="text" id="medical_coverage" name="medical_coverage" class="form-control form-control-sm">
                        </div>


                    </div>
                </div>

                <div class="row d-flex justify-content-between">
                    <div class="mb-3 col-md-3">
                        <label for="address" class="form-label text-dark small">Dirección</label>
                        <input type="text" id="address" name="address" class="form-control form-control-sm">
                    </div>

                    <div class="mb-3 col-md-3">
                        <label for="city" class="form-label text-dark small">Ciudad</label>
                        <input type="text" id="city" name="city" class="form-control form-control-sm">
                    </div>

                    <div class="mb-3 col-md-3">
                        <label for="province" class="form-label text-dark small">Provincia</label>
                        <input type="text" id="province" name="province" class="form-control form-control-sm">
                    </div>

                    <div class="mb-3 col-md-3">
                        <label for="postal_code" class="form-label text-dark small">Código Postal</label>
                        <input type="text" id="postal_code" name="postal_code" class="form-control form-control-sm">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="observations" class="form-label text-dark small">Observaciones</label>
                    <textarea id="observations" name="observations" class="form-control form-control-sm" rows="3"></textarea>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-info text-white fw-semibold py-2 px-4" id="savePatientButton">
                        Guardar Cambios
                    </button>
                </div>
            </form>
            <div id="patientMessage" class="mt-3 text-center small fw-medium"></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{{-- Enlaza el archivo JavaScript externo para esta vista --}}
<script type="module" src="{{ asset('js/patients/edit.js') }}"></script>
@endsection