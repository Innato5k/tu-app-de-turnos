// =====================================================================
// 1. CONFIGURACIÓN Y VARIABLES GLOBALES
// =====================================================================

const API_PROFESSIONALSCHEDULES_URL = '/api/professionalSchedule'; // URL base de tu API para horarios de profesionales
const REDIRECT_LOGIN_URL = '/login'; // Ruta a la página de inicio de sesión (web)
const EDIT_PATIENT_BASE_URL = '/patients'; // Base para la URL de edición (ej. /patients/1/edit)
// Mapeo de valores de día (0-6) a nombres
const dayNames = {
    0: 'Domingo',
    1: 'Lunes',
    2: 'Martes',
    3: 'Miércoles',
    4: 'Jueves',
    5: 'Viernes',
    6: 'Sábado'
};
const scheduleForm = document.getElementById('scheduleForm');
const enableDateRangeCheckbox = document.getElementById('enableDateRange');
const dateRangeFields = document.getElementById('dateRangeFields');
const schedulesTableBody = document.getElementById('schedulesTableBody');

// =====================================================================
// 2. FUNCIONES DE UTILIDAD
// =====================================================================

/**
 * Obtiene el token JWT del almacenamiento local.
 * @returns {string|null} El token JWT o null si no existe.
 */
function getAuthToken() {

    return localStorage.getItem('auth_token');// Para depuración, puedes eliminarlo en producción
}

/**
 * Redirige al usuario a la página de inicio de sesión y limpia el token.
 * @param {string} message Mensaje a mostrar al usuario.
 */
function redirectToLogin(message) {
    alert(message); // Considera reemplazar esto con un modal personalizado para mejor UX
    localStorage.removeItem('auth_token');
    localStorage.removeItem('token_type');
    localStorage.removeItem('user_info');
    window.location.href = REDIRECT_LOGIN_URL;
    console.log('Redirigiendo a la página de inicio de sesión...');
}

// --- Cargar horarios existentes al iniciar la página ---
async function fetchAndDisplaySchedules() {
    schedulesTableBody.innerHTML = '<tr><td colspan="6" class="text-center text-info">Cargando horarios...</td></tr>';

    try {
        const token = getAuthToken();

        if (!token) {
            redirectToLogin('No autenticado. Por favor, inicia sesión.');
            return;
        }

        const response = await fetch(`${API_PROFESSIONALSCHEDULES_URL}`, {
            method: 'GET',
            headers: {

                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`
            }
        });

        if (!response.ok) {
            redirectToLogin('Sesión expirada o no autorizada. Por favor, inicia sesión de nuevo.');

        }
        const schedules = await response.json();

        schedulesTableBody.innerHTML = ''; // Limpiar el mensaje de carga

        if (schedules.length === 0) {
            schedulesTableBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No hay horarios cargados.</td></tr>';
            return;
        }

        schedules.forEach(schedule => {
            const row = schedulesTableBody.insertRow(); 
            const daysText = dayNames[schedule.day_of_week] || 'Desconocido';
            const startDateTime = schedule.start_time;
            const endDateTime = schedule.end_time;
            const effectiveStartDate = schedule.effective_start_date ? new Date(schedule.effective_start_date).toLocaleDateString() : 'Siempre';
            const effectiveEndDate = schedule.effective_end_date ? new Date(schedule.effective_end_date).toLocaleDateString() : 'Siempre';

            row.innerHTML = `
                    <td>${daysText}</td>
                    <td>${startDateTime.substring(0, 5)}</td>
                    <td>${endDateTime.substring(0, 5)}</td>
                    <td>${effectiveStartDate}</td>
                    <td>${effectiveEndDate}</td>
                    <td>
                        <button class="btn btn-danger btn-sm delete-schedule" data-id="${schedule.id}">Eliminar</button>
                    </td>
                `;
        });
        attachDeleteListeners(); // Volver a adjuntar listeners después de recargar la tabla
    } catch (error) {
        console.error('Error al cargar horarios:', error);
        schedulesTableBody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error al cargar horarios.</td></tr>';
    }
}

// --- Funcionalidad para mostrar/ocultar el rango de fechas ---
enableDateRangeCheckbox.addEventListener('change', function () {
    if (this.checked) {
        dateRangeFields.hidden = false;
        // Hacemos que los campos de fecha sean requeridos si están habilitados
        document.getElementById('startDate').setAttribute('required', 'required');
        document.getElementById('endDate').setAttribute('required', 'required');
    } else {
       dateRangeFields.hidden = true;
        // Removemos el atributo required si están deshabilitados
        document.getElementById('startDate').removeAttribute('required');
        document.getElementById('endDate').removeAttribute('required');
        // Limpiamos los valores al ocultar
        document.getElementById('startDate').value = '';
        document.getElementById('endDate').value = '';
    }
});



// --- Guardar nuevo horario ---
scheduleForm.addEventListener('submit', async function (event) {
    event.preventDefault(); // Evitar el envío por defecto del formulario
    const token = getAuthToken();
        if (!token) {
            redirectToLogin('No autenticado. Por favor, inicia sesión.');
            return; 
        }

    const selectedDays = Array.from(document.querySelectorAll('input[type="checkbox"]:checked:not(#enableDateRange)'))
        .map(checkbox => parseInt(checkbox.value));

    if (selectedDays.length === 0) {
        alert('Por favor, selecciona al menos un día de la semana.');
        return;
    }

    const startTime = document.getElementById('startTime').value;
    const endTime = document.getElementById('endTime').value;
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;

    // Validación básica de horas
    if (startTime >= endTime) {
        alert('La hora de inicio debe ser anterior a la hora de fin.');
        return;
    }

    // Validación básica de fechas si están habilitadas
    if (enableDateRangeCheckbox.checked) {
        if (!startDate || !endDate) {
            alert('Por favor, ingresa ambas fechas para el rango.');
            return;
        }
        if (new Date(startDate) > new Date(endDate)) {
            alert('La fecha de inicio debe ser anterior o igual a la fecha de fin.');
            return;
        }
    }

    const scheduleData = {
        days_of_week: selectedDays,
        start_time: startTime,
        end_time: endTime,
        start_date: enableDateRangeCheckbox.checked ? startDate : null,
        end_date: enableDateRangeCheckbox.checked ? endDate : null,        
    };
   

    try {
        const response = await fetch(`${API_PROFESSIONALSCHEDULES_URL}`, { 
            method: 'POST',
            headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
            body: JSON.stringify(scheduleData)
        });

        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(`HTTP error! status: ${response.status} - ${errorData.message || ''}`);
        }

        alert('Horario guardado con éxito!');
        scheduleForm.reset(); // Limpiar formulario
        enableDateRangeCheckbox.checked = false; // Desmarcar rango de fechas
        dateRangeFields.classList.add('hidden'); // Ocultar campos de rango
        fetchAndDisplaySchedules(); // Recargar la tabla de horarios
    } catch (error) {
        console.error('Error al guardar horario:', error);
        alert('Hubo un error al guardar el horario: ' + error.message);
    }
});

// --- Eliminar horario ---
function attachDeleteListeners() {
    const token = getAuthToken();

    if (!token) {
        redirectToLogin('No autenticado. Por favor, inicia sesión.');
        return;
    }
    document.querySelectorAll('.delete-schedule').forEach(button => {
        button.onclick = async function () {
            if (!confirm('¿Estás seguro de que quieres eliminar este horario?')) {
                return;
            }
            const scheduleId = this.dataset.id;
            try {
                const response = await fetch(`${API_PROFESSIONALSCHEDULES_URL}/${scheduleId}`, { // Reemplaza con tu ruta de API
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}` // Envía el token JWT
                    }
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(`HTTP error! status: ${response.status} - ${errorData.message || ''}`);
                }

                alert('Horario eliminado con éxito!');
                fetchAndDisplaySchedules(); // Recargar la tabla
            } catch (error) {
                console.error('Error al eliminar horario:', error);
                alert('Hubo un error al eliminar el horario: ' + error.message);
            }
        };
    });
}


document.addEventListener('DOMContentLoaded', function () {

    // Cargar los horarios al cargar la página por primera vez
    fetchAndDisplaySchedules();
});