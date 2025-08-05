@extends('layouts.app')

@section('title', 'Gestión de Horarios Profesionales')

@section('content')


<div class="container mt-5">
    <h1 class="mb-4 text-center">Gestión de Horarios Laborales</h1>

    <div class="card">
        <div class="card-header bg-info text-white">
            <h4>Horarios Laborales Cargados</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Días</th>
                            <th>Hora Inicio</th>
                            <th>Hora Fin</th>
                            <th>Desde</th>
                            <th>Hasta</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="schedulesTableBody">
                        <tr>
                            <td colspan="6" class="text-center text-muted">Cargando horarios...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h4>Agregar Nuevo Horario</h4>
        </div>
        <div class="card-body">
            <form id="scheduleForm">
                <div class="row mb-3">
                    <label class="form-label mb-2">Días de la Semana:</label>
                    <div class="col-12 day-checkboxes">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="dayMonday" value="1">
                            <label class="form-check-label" for="dayMonday">Lunes</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="dayTuesday" value="2">
                            <label class="form-check-label" for="dayTuesday">Martes</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="dayWednesday" value="3">
                            <label class="form-check-label" for="dayWednesday">Miércoles</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="dayThursday" value="4">
                            <label class="form-check-label" for="dayThursday">Jueves</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="dayFriday" value="5">
                            <label class="form-check-label" for="dayFriday">Viernes</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="daySaturday" value="6">
                            <label class="form-check-label" for="daySaturday">Sábado</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="daySunday" value="0">
                            <label class="form-check-label" for="daySunday">Domingo</label>
                        </div>
                    </div>
                </div>

                <div class="row mb-3 align-items-end">
                    <div class="col-md-6">
                        <div class="time-input-group">
                            <label for="startTime" class="form-label">Hora de Inicio:</label>
                            <input type="time" class="form-control" id="startTime" value="09:00" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="time-input-group">
                            <label for="endTime" class="form-label">Hora de Fin:</label>
                            <input type="time" class="form-control" id="endTime" value="17:00" required>
                        </div>
                    </div>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="enableDateRange">
                    <label class="form-check-label" for="enableDateRange">
                        Aplicar en un rango de fechas específico
                    </label>
                </div>

                <div id="dateRangeFields" class="date-range-fields " hidden>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="startDate" class="form-label">Desde:</label>
                            <input type="date" class="form-control" id="startDate">
                        </div>
                        <div class="col-md-6">
                            <label for="endDate" class="form-label">Hasta:</label>
                            <input type="date" class="form-control" id="endDate">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-success mt-3">Guardar Horario</button>
            </form>
        </div>
    </div>

    
</div>

@endsection

@section('scripts')
@vite('resources/js/professionalSchedule/professionalSchedule.js') 
@endsection