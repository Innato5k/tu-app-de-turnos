@extends('layouts.app')

@section('title', 'Calendario de turnos')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fs-3 fw-bold text-dark">Calendario de turnos</h1>

        <a href="{{ route('schedules.create') }}" class="btn btn-primary fw-semibold d-flex align-items-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-calendar-plus me-2" viewBox="0 0 16 16">
                <path d="M8 7.5a.5.5 0 0 1 .5.5V9h1a.5.5 0 0 1 0 1H8.5v1.5a.5.5 0 0 1-1 0V10H6a.5.5 0 0 1 0-1h1.5V8a.5.5 0 0 1 .5-.5" />
                <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z" />
            </svg>
            <span>Agregar Nuevo Bloque</span>
        </a>
    </div>

    <div id="contenedorCalendar" class="card shadow-sm mb-4 p-1">
        <div id='Calendar' class="p-3"></div>
    </div>
</div>

<!-- Modal para confirmar reserva -->

<div class="modal fade" id="reservationModal" id="reservationModal" aria-labelledby="reservationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Confirmar Reserva</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="reservationForm">
                    <input type="hidden" id="slotIdInput" name="available_slot_id">

                    <p>Complete los detalles para la reserva:</p>

                    <div class="mb-3">
                        <label for="patient_id" class="form-label fw-bold">Paciente</label>
                        <select id="patient_id" name="patient_id" class="form-control" placeholder="Escriba nombre o CUIL para buscar..." required>
                            <option value=""></option>
                        </select>
                        <div class="form-text">Mínimo 3 caracteres para buscar.</div>
                    </div>

                    <div class="mb-3">
                        <label for="modality" class="form-label">Modalidad</label>
                        <select class="form-select" id="modality" name="modality" required>
                            <option value="">Seleccione...</option>
                            <option value="presencial">Presencial</option>
                            <option value="virtual">Virtual</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="cost" class="form-label">Costo ($)</label>
                        <input type="number" class="form-control" id="cost" name="cost" required value="5000">
                    </div>

                    <div class="mb-3">
                        <label for="duration" class="form-label">Duración</label>
                        <select class="form-select" id="duration" name="duration" required>
                            <option value="30">30 minutos</option>
                            <option value="60">1 hora</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="repeat" class="form-label">Repetición</label>
                        <select class="form-select" id="repeat" name="repeat" required>
                            <option value="none">No se repite</option>
                            <option value="weekly">Semanal</option>
                            <option value="biweekly">Quincenal</option>
                            <option value="monthly">Mensual</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notas (Opcional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="confirmReservationBtn">Confirmar Reserva</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@vite('resources/js/schedules/index.js')
@endsection