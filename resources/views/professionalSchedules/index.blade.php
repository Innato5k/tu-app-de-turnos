@extends('layouts.app')

@section('title', 'Gestión de Horarios Profesionales')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="display-6 fw-bold">Horarios Laborales</h1>
            <p class="text-muted">Configura tus rangos de atención semanal</p>
        </div>
        <button class="btn btn-primary rounded-pill px-4 shadow-sm" onclick="openScheduleModal()">
            <i class="fas fa-plus me-2"></i>Nuevo Horario
        </button>
    </div>

    <!-- Grid de Horarios (Reemplaza a la tabla) -->
   <div id="schedulesGrid" class="row g-4 justify-content-center">
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2 text-muted">Sincronizando agenda...</p>
        </div>
    </div>
</div>

<!-- MODAL (Popup de Carga) -->
<div class="modal fade" id="scheduleEntryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title fw-bold">Configurar Horario</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="scheduleForm">
                <div class="modal-body p-4">
                    <label class="form-label fw-bold mb-3">Días de la Semana:</label>
                    <div class="day-selector d-flex justify-content-between mb-4">
                        @foreach([1=>'L', 2=>'M', 3=>'X', 4=>'J', 5=>'V', 6=>'S', 0=>'D'] as $val => $label)
                        <div class="day-btn">
                            <input type="checkbox" class="btn-check day-checkbox" id="day{{$val}}" value="{{$val}}" autocomplete="off">
                            <label class="btn btn-outline-primary rounded-circle day-circle" for="day{{$val}}">{{$label}}</label>
                        </div>
                        @endforeach
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="startTime" class="form-label small fw-bold">Hora Inicio</label>
                            <input type="time" class="form-control" id="startTime" value="09:00" required>
                        </div>
                        <div class="col-md-6">
                            <label for="endTime" class="form-label small fw-bold">Hora Fin</label>
                            <input type="time" class="form-control" id="endTime" value="17:00" required>
                        </div>
                    </div>

                    <div class="card bg-light border-0 mb-3">
                        <div class="card-body p-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="enableDateRange">
                                <label class="form-check-label fw-bold" for="enableDateRange">Rango de fechas específico</label>
                            </div>
                            <div id="dateRangeFields" class="row g-2 mt-2" hidden>
                                <div class="col-6">
                                    <input type="date" class="form-control form-control-sm" id="startDate">
                                </div>
                                <div class="col-6">
                                    <input type="date" class="form-control form-control-sm" id="endDate">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-link text-muted fw-bold text-decoration-none" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4 rounded-pill shadow-sm">Guardar Horario</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@vite('resources/js/professionalSchedule/professionalSchedule.js')
@endsection