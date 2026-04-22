
import TomSelect from 'tom-select';
import 'tom-select/dist/css/tom-select.bootstrap5.css'

// Importaciones correctas de FullCalendar y sus plugins desde node_modules
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import bootstrap5Plugin from '@fullcalendar/bootstrap5';
import esLocale from '@fullcalendar/core/locales/es'; // Importa el locale de español

//TODO: armar BE para recibir pacientes activos '/api/patients/listActivePatients'
const patientsApiUrl = '/api/patients/listActivePatients';

let calendar
var tomSelectInstance = null;
// Arriba, con tus otras variables globales
let reservationModalInstance = null;

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

        events: function(info, successCallback, failureCallback) {
            const token = localStorage.getItem('auth_token');
        
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
            // El objeto 'info' contiene toda la información del evento clickeado
            handleSlotClick(info.event);
        },

        // Opciones de estilo para Bootstrap 5
        themeSystem: 'bootstrap5',

        eventDidMount: function (info) {
            if (info.event.extendedProps.status === 'available') { // available
                info.el.classList.add('fc-event-available');
            } else if (info.event.extendedProps.status === 'booked') { // booked
                info.el.classList.add('fc-event-booked');
            } else if (info.event.extendedProps.status === 'blocked') { // Bloqueado por profesional
                info.el.classList.add('fc-event-blocked');
            } else if (info.event.extendedProps.status === 'absent') { // absent
                info.el.classList.add('fc-event-absent');
            } else if (info.event.extendedProps.status === 'paid') { // Presente, Pagado
                info.el.classList.add('fc-event-paid');
            } else if (info.event.extendedProps.status === 'debt') { // Presente , Debe el pago
                info.el.classList.add('fc-event-debt');
            } else if (info.event.extendedProps.status === 'cancelled') { // cancelled
                info.el.classList.add('fc-event-cancelled');
            } else if (info.event.extendedProps.status === 'extra') { // not taken
                info.el.classList.add('fc-event-extra');
            }
        },

               
        
        select: function (info) {
            alert('Has seleccionado desde ' + info.startStr + ' hasta ' + info.endStr);
        },
    });





    const confirmBtn = document.getElementById('confirmReservationBtn');
    confirmBtn.addEventListener('click', function () {
        submitReservation();
    });
    calendar.render();
});



//TODO: Corregir , esta ok para post pero no para put       
function sendReservationRequest(payload) {
    const token = localStorage.getItem('auth_token');
    fetch('/api/professionalAppointments/book', {
        method: 'POST',
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
                throw new Error(data.message || 'Error al reservar el turno');
            }
            return data;
        })
        .then(data => {
            //alert('Reserva exitosa');                         
            alert('Turno reservado con éxito');
            const modalElement = document.getElementById('reservationModal');
            const modal = bootstrap.Modal.getInstance(modalElement);
            modal.hide();

            // Refrescamos el calendario para que el slot cambie de color (verde -> rojo)
            calendar.refetchEvents();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error: ' + error.message);
        });
}

// Función que manejará la lógica del clic
function handleSlotClick(event) {
    const status = event.extendedProps.status;
    const slotId = event.id;
    const appointmentId = event.extendedProps.appointment_id;
    const startTimeStr = event.startStr;

    const formattedTime = startTimeStr ? startTimeStr.slice(11, 16) : 'Hora desconocida';

    if (status === 'available') {
        console.log(`Slot disponible clicado. ID: ${slotId}, Hora: ${formattedTime}`);
        showReservationModal(slotId, formattedTime);
    } else if (status === 'booked' || status === 'paid') {
        showReservationModal(slotId, formattedTime, appointmentId);
    } else {
        alert(`Este horario (${formattedTime}) está ${status} y no puede ser reservado.`);
    }
}

//---------------------------------------------------------
// Variable para almacenar los pacientes cargados y comparar nombres con IDs
let loadedPatients = [];

// 1. Escuchar cuando el usuario escribe en el buscador

document.getElementById('patient_search')?.addEventListener('input', function(e) {
    const query = e.target.value;
    const datalist = document.getElementById('patientsList');
    const hiddenInput = document.getElementById('patient_id');

    // Si el usuario seleccionó una opción del datalist
    const selectedPatient = loadedPatients.find(p => p.full_name === query);
    if (selectedPatient) {
        hiddenInput.value = selectedPatient.id;
        // Opcional: auto-completar costo y modalidad si vienen en el objeto
        if(selectedPatient.modality) document.getElementById('modality').value = selectedPatient.modality;
        if(selectedPatient.cost) document.getElementById('cost').value = selectedPatient.cost;
        return;
    }

    // Si está escribiendo (mínimo 3 caracteres), buscamos en el BE
    if (query.length >= 3) {
        const token = localStorage.getItem('auth_token');
        fetch(`/api/patients/listActivePatients?search=${encodeURIComponent(query)}`, {
            headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
        })
        .then(response => response.json())
        .then(res => {
            const patients = res.data; // Entramos a .data por tu estructura de Resource
            loadedPatients = patients; // Guardamos para la comparación
            
            datalist.innerHTML = ''; // Limpiamos opciones viejas
            patients.forEach(patient => {
                const option = document.createElement('option');
                option.value = patient.full_name; // Lo que ve el usuario
                datalist.appendChild(option);
            });
        });
    }
});

function showReservationModal(slotId, formattedTime, appointmentId = null) {
    const modalElement = document.getElementById('reservationModal');
    
    // 1. Limpiar instancia previa si existe
    if (tomSelectInstance) {
        tomSelectInstance.destroy();
    }

    // 2. Seteos básicos
    document.getElementById('slotIdInput').value = slotId;

    // 3. Inicializar TomSelect
    tomSelectInstance = new TomSelect('#patient_id', {
        valueField: 'id',
        labelField: 'full_name',
        searchField: ['full_name', 'cuil'],
        load: function(query, callback) {
            if (!query.length) return callback();
            const token = localStorage.getItem('auth_token');
            fetch(`/api/patients/listActivePatients?search=${encodeURIComponent(query)}`, {
                headers: { 'Authorization': 'Bearer ' + token }
            })
            .then(response => response.json())
            .then(json => callback(json.data))
            .catch(() => callback());
        }
    });

    const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
    modal.show();
}

// 3. Función para enviar la reserva
function submitReservation() {
    const payload = {
        available_slot_id: document.getElementById('slotIdInput').value,
        patient_id: document.getElementById('patient_id').value,
        modality: document.getElementById('modality').value,
        cost: document.getElementById('cost').value,
        notes: document.getElementById('notes').value,
    };

    if (!payload.patient_id) {
        alert("Por favor, seleccione un paciente de la lista de sugerencias.");
        return;
    }

    sendReservationRequest(payload); 
}
//---------------------------------------------------------

// Función para obtener datos del calendario desde el backend
function fetchCalendarData(startDate, endDate) {
    const apiUrl = `/api/professionalAppointments/?start_date=${startDate}&end_date=${endDate}`;
    const token = localStorage.getItem('auth_token');
    if (!token) {
        console.error("Token de autenticación no encontrado.");
    }

    fetch(apiUrl, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + token
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            const rawEvents = data.data;

            // Aquí debes procesar los datos para FullCalendar
            // La API debe devolver un formato que FullCalendar entienda
            const events = rawEvents.map(item => ({
                id: item.id,
                title: item.title,
                start: item.start,
                end: item.end,
                extendedProps: {
                    status: item.extendedProps.status,
                    appointment_id: item.extendedProps.appointment_id,
                    patient_id: item.extendedProps.patient_id
                }
            }));

            // Limpia los eventos existentes y añade los nuevos
            calendar.removeAllEvents();
            calendar.addEventSource(events);
        })
        .catch(error => {
            console.error('Error fetching calendar data:', error);
        });
}