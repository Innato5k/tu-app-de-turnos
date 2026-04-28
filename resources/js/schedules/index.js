
import TomSelect from 'tom-select';
import 'tom-select/dist/css/tom-select.bootstrap5.css'

// Importaciones correctas de FullCalendar y sus plugins desde node_modules
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import bootstrap5Plugin from '@fullcalendar/bootstrap5';
import esLocale from '@fullcalendar/core/locales/es'; // Importa el locale de español


const REDIRECT_LOGIN_URL = '/login'; // Ruta a la página de inicio de sesión (web)
const patientsApiUrl = '/api/patients/listActivePatients';
let calendar
var tomSelectInstance = null;
let reservationModalInstance = null;
let isEditing = false;
let currentAppointmentId = null;
let loadedPatients = [];

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
}


document.addEventListener('DOMContentLoaded', function () {

    var calendarEl = document.getElementById('Calendar');

    calendar = new Calendar(calendarEl, {
        // Añadir los plugins 
        plugins: [
            dayGridPlugin,
            timeGridPlugin,
            interactionPlugin,
            bootstrap5Plugin
        ],

        // Configuración básica del calendario
        initialView: 'timeGridWeek', // Vista inicial: semanal con franjas de tiempo
        locale: esLocale, // Usar el objeto de locale importado
        headerToolbar: {
            left: 'prev,next today', // Botones de navegación
            center: 'title', // Título (ej. "Julio 22 - 28, 2024")
            right: 'timeGridDay,timeGridWeek,dayGridMonth' // Opciones de vista: semanal y mensual
        },
        slotMinTime: '00:00:00', // Horario de inicio del calendario
        slotMaxTime: '24:00:00', // Horario de fin del calendario
        slotDuration: '00:30:00', // Duración de cada slot (ej. 30 minutos)
        editable: true, // Permite arrastrar y redimensionar eventos (útil para gestionar turnos)
        selectable: true, // Permite seleccionar rangos de tiempo
        nowIndicator: true, // Muestra la línea de la hora actual
        allDaySlot: false, // Oculta la sección "todo el día"
        eventClassNames: 'fc-event-custom', // Clase CSS personalizada para eventos
        timeZone: 'America/Argentina/Buenos_Aires',

        events: function (info, successCallback, failureCallback) {
            const token = localStorage.getItem('auth_token');
            if (!token) {
                redirectToLogin('No autenticado. Por favor, inicia sesión.');
                return;
            }

            // FullCalendar nos da info.startStr e info.endStr automáticamente (ISO8601)
            // Pero tu BE espera d/m/Y, así que formateamos:
            const start = info.start.toLocaleDateString('es-AR'); // d-m-Y
            const end = info.end.toLocaleDateString('es-AR');
            fetch(`/api/professionalAppointments/?start_date=${start}&end_date=${end}`, {
                headers: { 'Authorization': 'Bearer ' + token }
            })
                .then(response => response.json())
                .then(json => {
                    // El Resource de Laravel devuelve la data en json.data

                    successCallback(json.data);

                })
                .catch(error => failureCallback(error));
        },

        eventClick: function (info) {
            handleSlotClick(info.event);
        },

        // Opciones de estilo para Bootstrap 5
        themeSystem: 'bootstrap5',

        //TODO: trabajar ya que puede tener 2 estados, presente/ausente/cancelado + pago/impago
        eventDidMount: function (info) {

            const props = info.event.extendedProps;
            const status = props.status; // 'available', 'booked', 'attended', etc.
            const payment = props.payment_status; // 'paid', 'pending'

            // 1. Agregamos la clase de estado (ej: fc-event-booked)
            info.el.classList.add(`fc-event-${status}`);

            if (props.is_extra) info.el.classList.add(`fc-event-extra`);

            // 2. Si el turno no está disponible o bloqueado, verificamos pago
            if (status !== 'available' && status !== 'blocked') {
                const paymentStrip = document.createElement('div');
                paymentStrip.className = 'payment-strip';

                // Si pagó, barrita verde; si debe, barrita roja o gris
                paymentStrip.style.backgroundColor = (payment === 'paid') ? '#2ecc71' : '#e74c3c';

                // La insertamos al principio del evento
                info.el.prepend(paymentStrip);

                // Si está pagado, podemos agregar un check sutil al título
                if (payment === 'paid') {
                    const titleEl = info.el.querySelector('.fc-event-title');
                    if (titleEl) titleEl.innerHTML += ' <small>✓</small>';
                }
            }
        },
    });

    const confirmBtn = document.getElementById('confirmReservationBtn');
    confirmBtn.addEventListener('click', function () {
        submitReservation();
    });
    calendar.render();
});

window.openExtraAppointmentModal = function () {
    // 1. Limpiamos y preparamos el modal
    const modalElement = document.getElementById('reservationModal');

    // 2. Usamos la función de mostrar modal con parámetros de "Extra"
    // Pasamos slotId = null y appointmentId = null
    showReservationModal(null, "Definir horario extra", null, null, true);
}

//TODO: Corregir , esta ok para post pero no para put       
function sendReservationRequest(url, method, payload) {
    const token = localStorage.getItem('auth_token');
    if (!token) {

        redirectToLogin('No autenticado. Por favor, inicia sesión.');
        return;
    }
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': 'Bearer ' + token
        },
        body: JSON.stringify(payload)
    })
        .then(async response => {
            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.message || 'Error al reservar o modificar el turno');
            }
            return data;
        })
        .then(data => {
            alert('Turno reservado/actualizado con éxito');
            const modalElement = document.getElementById('reservationModal');
            const modal = bootstrap.Modal.getInstance(modalElement);
            modal.hide();
            calendar.refetchEvents();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error: ' + error.message);
        });
}

function handleSlotClick(event) {
    const status = event.extendedProps.status;
    const appointmentId = event.extendedProps.appointment_id;
    const slotId = event.id;

    if (appointmentId) {
        // En lugar de abrir el modal directo, vamos a buscar la data fresca
        fetchFreshAppointmentData(appointmentId, slotId, event);
    } else if (status === 'available') {
        showReservationModal(slotId, event.startStr);
    }
}

function fetchFreshAppointmentData(appointmentId, slotId, event) {
    const token = localStorage.getItem('auth_token');

    fetch(`/api/professionalAppointments/${appointmentId}`, {
        headers: {
            'Authorization': 'Bearer ' + token,
            'Accept': 'application/json'
        }
    })
        .then(res => {
            if (!res.ok) throw new Error("Error en respuesta de API");
            return res.json();
        })
        .then(response => {
            const fullData = response.data || response;
            // USAMOS UN TRY/CATCH ACÁ PARA VER EL ERROR REAL
            try {
                showReservationModal(slotId, event.startStr, appointmentId, fullData);
            } catch (errorInterno) {
                console.error("ERROR DENTRO DE showReservationModal:", errorInterno);
                alert("Error en el modal: " + errorInterno.message);
            }
        })
        .catch(err => {
            console.error("Error de red/petición:", err);
            alert("Error al obtener datos actualizados");
        });
}

function showReservationModal(slotId, formattedTime, appointmentId = null, fullData = null, isExtra = false) {
    const form = document.getElementById('reservationForm');
    form.reset(); // Resetea inputs nativos (notas, costo, modalidad)

    const modalElement = document.getElementById('reservationModal');
    const selectedDateTime = document.getElementById('selectedDateTime');
    const patientSelect = document.getElementById('patient_id');
    const confirmBtn = document.getElementById('confirmReservationBtn');
    const statusWrapper = document.getElementById('status_wrapper');
    const label = document.getElementById('reservationModalLabel');
    const extraWrapper = document.getElementById('extra_datetime_wrapper');
    const extraStartTimeInput = document.getElementById('extra_start_time');

    document.getElementById('slotIdInput').value = '';
    document.getElementById('appointmentIdInput').value = '';

    selectedDateTime.textContent = formattedTime;
    isEditing = !!appointmentId;
    currentAppointmentId = appointmentId;

    if (isExtra) {
        extraWrapper.style.display = 'block';
        document.getElementById('reservationModalLabel').textContent = "Nuevo Turno Extra";
        document.getElementById('status').value = 'booked';
        // --- LÓGICA DE REDONDEO ---
        const now = new Date();
        const minutes = now.getMinutes();
        const roundedMinutes = Math.ceil(minutes / 15) * 15; // Redondea al bloque de 15' superior

        now.setMinutes(roundedMinutes);
        now.setSeconds(0);
        now.setMilliseconds(0);

        // Formateo manual para evitar problemas de zona horaria y formato ISO
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const mins = String(now.getMinutes()).padStart(2, '0');

        extraStartTimeInput.value = `${year}-${month}-${day}T${hours}:${mins}`;
    } else {
        extraWrapper.style.display = 'none';
        //extraStartTimeInput.value = '';
        document.getElementById('slotIdInput').value = slotId;
        document.getElementById('appointmentIdInput').value = appointmentId || '';
    }

    if (tomSelectInstance) {
        tomSelectInstance.destroy();
        tomSelectInstance = null;
    }

    tomSelectInstance = new TomSelect('#patient_id', {
        valueField: 'id',
        labelField: 'full_name',
        searchField: ['full_name', 'cuil'],
        placeholder: 'Escriba nombre o CUIL...',
        allowEmptyOption: true,
        load: function (query, callback) {
            if (!query.length || isEditing) return callback();

            const token = localStorage.getItem('auth_token');
            fetch(`/api/patients/listActivePatients?search=${encodeURIComponent(query)}`, {
                headers: { 'Authorization': 'Bearer ' + token }
            })
                .then(response => response.json())
                .then(json => {
                    loadedPatients = json.data;
                    callback(json.data);
                })
                .catch(() => callback());
        },
        onChange: function (value) {
            if (!value || isEditing) return;
            const selectedPatient = loadedPatients.find(p => p.id == value);
            if (selectedPatient) {
                if (selectedPatient.preferred_modality) document.getElementById('modality').value = selectedPatient.preferred_modality;
                if (selectedPatient.preferred_cost) document.getElementById('cost').value = selectedPatient.preferred_cost;
            }
        }
    });

    if (appointmentId && fullData) {
        tomSelectInstance.addOption({ id: fullData.patient_id, full_name: fullData.patient.full_name });
        tomSelectInstance.setValue(fullData.patient_id);
        tomSelectInstance.disable();
        confirmBtn.textContent = "Actualizar Cambios";
        document.getElementById('reservationModalLabel').textContent = "Gestionar Turno";
        document.getElementById('notes').value = fullData.notes;
        document.getElementById('cost').value = fullData.cost;
        document.getElementById('status').value = fullData.status;
        document.getElementById('duration').value = fullData.duration;
        document.getElementById('duration').disabled = true;
        //document.getElementById('repeat').value = fullData.duration; TODO: AUN NO IMPLEMENTADO, SI SE IMPLEMENTA, HAY QUE PENSAR BIEN CÓMO AFECTA A LA EDICIÓN
        document.getElementById('repeat').disabled = true;
        document.getElementById('status_wrapper').style.display = 'block';
        document.getElementById('payment_status').value = fullData.payment_status;
    } else {
        tomSelectInstance.clear();
        tomSelectInstance.enable();
        if (!isExtra) {
            document.getElementById('reservationModalLabel').textContent = "Nueva Reserva";
            statusWrapper.style.display = 'none';
        }
        confirmBtn.textContent = "Confirmar Reserva";
        document.getElementById('notes').value = '';
        document.getElementById('duration').disabled = false;
        //document.getElementById('repeat').value = fullData.duration; TODO: AUN NO IMPLEMENTADO, SI SE IMPLEMENTA, HAY QUE PENSAR BIEN CÓMO AFECTA A LA EDICIÓN
        document.getElementById('repeat').disabled = true; // TODO: Por ahora, no permitimos repetir ni al crear ni al editar, hasta que tengamos claro el flujo  
        document.getElementById('status_wrapper').style.display = 'none';
    }

    const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
    modal.show();
}

function submitReservation() {
    const appointmentId = document.getElementById('appointmentIdInput').value;
    const isUpdate = appointmentId !== "" && appointmentId !== null;
    const extraWrapper = document.getElementById('extra_datetime_wrapper');
    const isExtra = extraWrapper && extraWrapper.style.display === 'block';
    var url = '/api/professionalAppointments/book';
    var method = 'POST';
    const payload = {
        modality: document.getElementById('modality').value,
        cost: document.getElementById('cost').value,
        notes: document.getElementById('notes').value,
        status: document.getElementById('status').value,
        payment_status: document.getElementById('payment_status').value
    }
    if (!isUpdate) {
        payload.available_slot_id = document.getElementById('slotIdInput').value,
            payload.patient_id = document.getElementById('patient_id').value,
            payload.duration = document.getElementById('duration').value

        if (isExtra) {
            url = '/api/professionalAppointments/bookExtra';
            payload.start_time = document.getElementById('extra_start_time').value;

            if (!payload.start_time) return alert("Seleccione fecha y hora para el extra");
        } else {
            payload.available_slot_id = document.getElementById('slotIdInput').value;
        }

        if (!payload.patient_id) {
            alert("Por favor, seleccione un paciente de la lista de sugerencias.");
            return;
        }
    } else {
        url = `/api/professionalAppointments/${appointmentId}`
        method = 'PUT'
    }
    console.log("payload enviado:", payload);
    sendReservationRequest(url, method, payload);
}






