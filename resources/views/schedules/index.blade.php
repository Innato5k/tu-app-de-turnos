@extends('layouts.app')

@section('title', 'Mis Horarios Agendados')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fs-3 fw-bold text-dark">Mis Horarios Agendados</h1>
        
        <a href="{{ route('schedules.create') }}" class="btn btn-primary fw-semibold d-flex align-items-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-calendar-plus me-2" viewBox="0 0 16 16">
                <path d="M8 7.5a.5.5 0 0 1 .5.5V9h1a.5.5 0 0 1 0 1H8.5v1.5a.5.5 0 0 1-1 0V10H6a.5.5 0 0 1 0-1h1.5V8a.5.5 0 0 1 .5-.5"/>
                <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/>
            </svg>
            <span>Agregar Nuevo Bloque</span>
        </a>
    </div>

    <div class="card shadow-sm mb-4 p-3"> {{-- Añadí p-3 para padding interno --}}
        <div id='Calendar'></div> {{-- Este es el div donde FullCalendar se renderizará --}}
    </div>
</div>
@endsection

@section('scripts')
@vite('resources/js/schedules/index.js') 
@endsection