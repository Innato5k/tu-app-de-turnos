import bootstrap from 'bootstrap/dist/js/bootstrap.bundle.min.js';

// 1. CONFIGURACIÓN
const API_URL = '/api/professionalSchedule';
const REDIRECT_LOGIN_URL = '/login';
let scheduleModal;

const dayNames = {
    0: 'Dom', 1: 'Lun', 2: 'Mar', 3: 'Mié', 4: 'Jue', 5: 'Vie', 6: 'Sáb'
};

document.addEventListener('DOMContentLoaded', function () {
    scheduleModal = new bootstrap.Modal(document.getElementById('scheduleEntryModal'));

    // Listener Rango de Fechas (Tu lógica original)
    const rangeSwitch = document.getElementById('enableDateRange');
    const rangeFields = document.getElementById('dateRangeFields');
    rangeSwitch.addEventListener('change', function () {
        rangeFields.hidden = !this.checked;
        const inputs = rangeFields.querySelectorAll('input');
        inputs.forEach(i => this.checked ? i.setAttribute('required', 'required') : i.removeAttribute('required'));
    });

    fetchAndDisplaySchedules();

    document.getElementById('scheduleForm').addEventListener('submit', handleFormSubmit);
});

// --- FUNCIONES CORE ---

function getAuthToken() {
    return localStorage.getItem('auth_token');
}

function redirectToLogin(message) {
    alert(message);
    localStorage.removeItem('auth_token');
    window.location.href = REDIRECT_LOGIN_URL;
}

window.openScheduleModal = function () {
    document.getElementById('scheduleForm').reset();
    document.getElementById('dateRangeFields').hidden = true;
    scheduleModal.show();
}

async function fetchAndDisplaySchedules() {
    const container = document.getElementById('schedulesGrid');
    const token = getAuthToken();

    if (!token) return redirectToLogin('No autenticado.');

    try {
        const response = await fetch(API_URL, {
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
        });

        if (!response.ok) throw new Error('Error en la petición');

        const result = await response.json();
        const rawSchedules = result.data || result;


        container.innerHTML = '';

        if (rawSchedules.length === 0) {
            container.innerHTML = '<div class="col-12 text-center text-muted">No hay horarios configurados.</div>';
            return;
        }

        // --- LÓGICA DE ANALISTA: AGRUPAR POR DÍA ---
        const grouped = rawSchedules.reduce((acc, curr) => {
            const days = curr.day_of_week.split(',');
            days.forEach(d => {
                const dayNum = d.trim();
                if (!acc[dayNum]) acc[dayNum] = [];
                acc[dayNum].push(curr);
            });
            return acc;
        }, {});

        const sortedDays = Object.keys(grouped).sort((a, b) => (a == 0 ? 7 : a) - (b == 0 ? 7 : b));

        sortedDays.forEach(dayNum => {
            const dayCard = document.createElement('div');
            dayCard.className = 'col-12 col-xl-10 mb-3'; // Tarjeta ancha centrada

            // Ordenar horarios del mismo día por hora de inicio
            const daySchedules = grouped[dayNum].sort((a, b) => a.start_time.localeCompare(b.start_time));

            const itemsHtml = daySchedules.map(s => {
                const isTemporal = s.effective_start_date || s.effective_end_date;
                return `
            <div class="d-flex align-items-center p-3 mb-2 bg-white rounded border-start ${isTemporal ? 'border-warning border-4' : 'border-primary border-4'} shadow-sm">
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center">
                        <h5 class="mb-0 fw-bold text-dark me-3">${s.start_time.substring(0, 5)} - ${s.end_time.substring(0, 5)}</h5>
                        ${isTemporal ? '<span class="badge bg-warning text-dark px-2 py-1" style="font-size:0.7rem">TEMPORAL</span>' : '<span class="badge bg-primary-subtle text-primary px-2 py-1" style="font-size:0.7rem">FIJO</span>'}
                    </div>
                    ${isTemporal ? `
                        <div class="small text-muted mt-1">
                            <i class="far fa-calendar-alt me-1"></i> Vigencia: 
                            ${s.effective_start_date || '...'} hasta ${s.effective_end_date || '...'}
                        </div>` : ''}
                </div>
                <button class="btn btn-outline-danger btn-sm border-0 rounded-circle" onclick="deleteSchedule(${s.id})">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        `;
            }).join('');

            dayCard.innerHTML = `
        <div class="card border-0 shadow-sm overflow-hidden" style="background-color: #f8f9fa;">
            <div class="card-body p-0">
                <div class="row g-0">
                    <div class="col-md-2 bg-dark text-white d-flex align-items-center justify-content-center py-3">
                        <div class="text-center">
                            <div class="text-uppercase small opacity-75">Día</div>
                            <div class="fs-4 fw-bold">${dayNames[dayNum]}</div>
                        </div>
                    </div>
                    <div class="col-md-10 p-3">
                        ${itemsHtml}
                    </div>
                </div>
            </div>
        </div>
    `;
            container.appendChild(dayCard);
        });

    } catch (error) { /* ... manejo error ... */ }
}

async function handleFormSubmit(e) {
    e.preventDefault();
    const token = getAuthToken();

    const selectedDays = Array.from(document.querySelectorAll('.day-checkbox:checked')).map(cb => parseInt(cb.value));
    if (selectedDays.length === 0) return alert('Seleccioná al menos un día.');

    const startTime = document.getElementById('startTime').value;
    const endTime = document.getElementById('endTime').value;

    if (startTime >= endTime) return alert('La hora de inicio debe ser anterior a la de fin.');

    const payload = {
        days_of_week: selectedDays,
        start_time: startTime,
        end_time: endTime,
        effective_start_date: document.getElementById('enableDateRange').checked ? document.getElementById('startDate').value : null,
        effective_end_date: document.getElementById('enableDateRange').checked ? document.getElementById('endDate').value : null
    };

    try {
        const response = await fetch(API_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify(payload)
        });

        if (response.ok) {
            alert('Horario guardado con éxito!');
            scheduleModal.hide();
            fetchAndDisplaySchedules();
        } else {
            const err = await response.json();
            alert('Error: ' + (err.message || 'No se pudo guardar'));
        }
    } catch (error) {
        alert('Error de conexión con el servidor');
    }
}

window.deleteSchedule = async function (id) {
    if (!confirm('¿Eliminar este rango horario?')) return;

    const token = getAuthToken();
    try {
        const response = await fetch(`${API_URL}/${id}`, {
            method: 'DELETE',
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
        });

        if (response.ok) {
            fetchAndDisplaySchedules();
        }
    } catch (error) {
        alert('Error al eliminar');
    }
}