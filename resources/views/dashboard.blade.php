@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="">
            <div class="card shadow border-0 bgFraseDelDía  text-center p-4 h-100 d-flex align-items-center justify-content-center">
                <div id="quote-container">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando frase...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Widgets de Resumen Financiero -->
    <div class="row">
        <div class="col-8 p-1">
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Facturación </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="widget-facturacion">$0.00</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-left-danger shadow h-100 py-2">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Adeudado</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="widget-deuda">$0.00</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">% de Morosidad</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="widget-ratio">0%</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- Gráfico de Asistencia -->
                <div class="col-xl-12 col-lg-7">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Ratio de Asistencia y Turnos Extra</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-area">
                                <canvas id="attendanceChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-4 p-1">
            <div class="card shadow border-start bg-info text-white mb-4">
                <div class="card-header p-2 bg-info text-white">
                        <h4 id="next-appointment-card-title" class="m-0 font-weight-bold">Próximo Turno en: </h4>
                    </div>
                <div class="card-body px-3 py-1">
                    <div id="next-appointment-box" class="">
                        <!-- Se llena vía JS -->
                    </div>
                </div>
            </div>
            <div class="  mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-primary text-white">
                        <h6 class="m-0 font-weight-bold">Avisos del Sistema</h6>
                    </div>
                    <div class="card-body p-2" id="announcements-container" style="max-height: 400px; overflow-y: auto;">
                        <!-- Se llena vía JS -->
                    </div>
                </div>
            </div>
        </div>
        <!-- Listado de Deudores -->
        <div class="col-4 ">
            
        </div>
    </div>

</div>
@endsection

@section('scripts')
@vite(['resources/js/dashboard.js'])
<script>
    // Esperamos a que el DOM esté listo y llamamos a la función
    document.addEventListener('DOMContentLoaded', function() {
        window.initDashboard();
    });
</script>
@endsection