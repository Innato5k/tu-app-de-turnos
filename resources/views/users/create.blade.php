@extends('layouts.app') {{-- Extiende el layout principal --}}

@section('title', 'Crear Usuario') {{-- Título de la página --}}

@section('content')
<div class="container ">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h1 class="fs-3 fw-bold text-dark">Crear Usuario</h1>
        <a href="{{ route('users.index') }}" class="btn btn-secondary fw-semibold px-3">
            Volver al Listado
        </a>
    </div>
    <div class="card shadow-sm p-2">
        <div class="card-body">
            <form id="createuserForm">
                @csrf
                @method('POST')

                <div class="row d-flex justify-content-between">
                    <div class="mb-3 col-md-1 ">
                        <label for="userId" class="form-label text-dark small">ID</label>
                        <input type="text" id="userId" class="form-control form-control-sm bg-light" readonly disabled>
                    </div>

                    <div class="mb-3 col-md-4">
                        <label for="name" class="form-label text-dark small">Nombre</label>
                        <input type="text" id="name" name="name" class="required form-control form-control-sm border-secondary" required>
                    </div>

                    <div class="mb-3 col-md-4">
                        <label for="last_name" class="form-label text-dark small">Apellido</label>
                        <input type="text" id="last_name" name="last_name" class="required form-control form-control-sm border-secondary" required>
                    </div>

                    <div class="mb-3 col-md-3">
                        <label for="cuil" class="form-label text-dark small">CUIL</label>
                        <input type="text" id="cuil" name="cuil" class="required form-control form-control-sm border-secondary" required maxlength="13" placeholder="XX-XXXXXXXX-X">
                    </div>

                    <div class="row d-flex justify-content-between">
                        <div class="mb-3 col-md-2">
                            <label for="birth_date" class="form-label text-dark small">Fecha de Nacimiento</label>
                            <input type="date" id="birth_date" name="birth_date" class="required form-control form-control-sm border-secondary" required>
                        </div>
                        <div class="mb-3 col-md-1">
                            <label for="gender" class="form-label text-dark small">Género</label>
                            <select id="gender" name="gender" class="required form-select form-select-sm border-secondary">
                                <option value="" selected disabled>Seleccione una opción</option>
                                <option value="male">M</option>
                                <option value="female">F</option>
                                <option value="other">Otro</option>
                            </select>
                        </div>
                        <div class="mb-3 col-md-3">
                            <label for="email" class="form-label text-dark small">Correo Electrónico</label>
                            <input type="email" id="email" name="email" class="required form-control form-control-sm border-secondary" required>
                        </div>

                        <div class="mb-3 col-md-3">
                            <label for="phone" class="form-label text-dark small">Teléfono</label>
                            <input type="number" id="phone" name="phone" class="required form-control form-control-sm border-secondary">
                        </div>

                        <div class="mb-3 col-md-3">
                            <label for="phone_opt" class="form-label text-dark small">Teléfono Alternativo</label>
                            <input type="number" id="phone_opt" name="phone_opt" class="form-control form-control-sm border-secondary">
                        </div>
                    </div>
                </div>

                <div class="row d-flex justify-content-between">
                    <div class="mb-3 col-md-6">
                        <label for="speciality" class="form-label text-dark small">Especialidad</label>
                        <input type="text" id="speciality" name="speciality" class="required form-control form-control-sm border-secondary">
                    </div>
                    <div class="mb-3 col-md-3">
                        <label for="national_md_lic" class="form-label text-dark small">Matricula Nacional</label>
                        <input type="text" id="national_md_lic" name="national_md_lic" class="required form-control form-control-sm border-secondary">
                    </div>

                    <div class="mb-3 col-md-3">
                        <label for="provincial_md_lic" class="form-label text-dark small">Matricula Provincial</label>
                        <input type="text" id="provincial_md_lic" name="provincial_md_lic" class="required form-control form-control-sm border-secondary">
                    </div>
                </div>
                <div class="row d-flex justify-content-end mb-1">
                    <div class=" d-flex justify-content-end mb-1 col-md-4">
                        <button type="submit" class="btn btn-success text-white fw-semibold py-2 px-4" id="saveuserButton">
                            Guardar Cambios
                        </button>
                    </div>

                </div>
            </form>
            <div id="userMessage" class="mt-3 text-center small fw-medium"></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{{-- Enlaza el archivo JavaScript externo para esta vista --}}
@vite('resources/js/users/create.js')
@endsection