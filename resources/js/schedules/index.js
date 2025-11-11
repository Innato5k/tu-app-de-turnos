// Importaciones correctas de FullCalendar y sus plugins desde node_modules
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import bootstrap5Plugin from '@fullcalendar/bootstrap5';
import esLocale from '@fullcalendar/core/locales/es'; // Importa el locale de español
var calendar

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
        slotMinTime: '08:00:00', // Horario de inicio del calendario
        slotMaxTime: '24:00:00', // Horario de fin del calendario
        slotDuration: '00:30:00', // Duración de cada slot (ej. 30 minutos)
        editable: true, // Permite arrastrar y redimensionar eventos (útil para gestionar turnos)
        selectable: true, // Permite seleccionar rangos de tiempo
        nowIndicator: true, // Muestra la línea de la hora actual
        allDaySlot: false, // Oculta la sección "todo el día"
        eventClassNames: 'fc-event-custom', // Clase CSS personalizada para eventos
        timeZone: 'America/Argentina/Buenos_Aires',

        eventClick: function(info) {
            // El objeto 'info' contiene toda la información del evento clickeado
            handleSlotClick(info.event);
        },

        // Opciones de estilo para Bootstrap 5
        themeSystem: 'bootstrap5',

        datesSet: function (info) {
            // 'info.start' y 'info.end' son los objetos de fecha para el rango de la vista actual
            const startDate = info.start;
            const endDate = info.end;

            // FullCalendar.formatDate es útil para dar formato
            const formattedStartDate = calendar.formatDate(startDate, {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit'
            });
            const formattedEndDate = calendar.formatDate(endDate, {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit'
            });
            fetchCalendarData(formattedStartDate, formattedEndDate);
        },
        /* EVENTOS DE PRUEBA - COMENTAR O ELIMINAR EN PRODUCCIÓN
        events: [
            // --- Lunes, 21 de julio de 2025 (Pasado) ---
            { id: '1', title: 'Paciente Presente Pagado - P1', start: '2025-08-21T08:00:00', end: '2025-08-21T09:00:00', extendedProps: { status: 'paid' } },
            { id: '2', title: 'Paciente Ausente - P2', start: '2025-08-21T09:00:00', end: '2025-08-21T10:00:00', extendedProps: { status: 'absent' } },
            { id: '3', title: 'Agendado - P3', start: '2025-08-21T10:00:00', end: '2025-08-21T11:00:00', extendedProps: { status: 'booked' } },
            { id: '4', title: 'Bloqueado por Profesional', start: '2025-08-21T11:00:00', end: '2025-08-21T12:00:00', extendedProps: { status: 'blocked' } },
            { id: '5', title: 'Cancelado - P4', start: '2025-08-21T12:00:00', end: '2025-08-21T13:00:00', extendedProps: { status: 'canceled' } },
            { id: '6', title: 'Disponible', start: '2025-08-21T14:00:00', end: '2025-08-21T15:00:00', extendedProps: { status: 'available' } },
            { id: '7', title: 'Paciente Presente Deuda - P5', start: '2025-08-21T15:00:00', end: '2025-08-21T16:00:00', extendedProps: { status: 'debt' } },
            { id: '8', title: 'No Tomado', start: '2025-08-21T16:00:00', end: '2025-08-21T17:00:00', extendedProps: { status: 'event-notTaken' } },
            { id: '9', title: 'Agendado - P6', start: '2025-08-21T17:00:00', end: '2025-08-21T18:00:00', extendedProps: { status: 'booked' } },
 
            // --- Martes, 22 de julio de 2025 (Pasado) ---
            { id: '10', title: 'Turno Extra - P7', start: '2025-08-22T07:00:00', end: '2025-08-22T08:00:00', extendedProps: { status: 'extra' } },
            { id: '11', title: 'Paciente Presente Pagado - P8', start: '2025-08-22T08:00:00', end: '2025-08-22T09:00:00', extendedProps: { status: 'paid' } },
            { id: '12', title: 'Disponible', start: '2025-08-22T09:00:00', end: '2025-08-22T10:00:00', extendedProps: { status: 'available' } },
            { id: '13', title: 'Agendado - P9', start: '2025-08-22T10:00:00', end: '2025-08-22T11:00:00', extendedProps: { status: 'booked' } },
            { id: '14', title: 'Cancelado - P10', start: '2025-08-22T11:00:00', end: '2025-08-22T12:00:00', extendedProps: { status: 'canceled' } },
            { id: '15', title: 'Paciente Ausente - P11', start: '2025-08-22T14:00:00', end: '2025-08-22T15:00:00', extendedProps: { status: 'absent' } },
            { id: '16', title: 'No Tomado', start: '2025-08-22T15:00:00', end: '2025-08-22T16:00:00', extendedProps: { status: 'event-notTaken' } },
            { id: '17', title: 'Bloqueado por Profesional', start: '2025-08-22T16:00:00', end: '2025-08-22T17:00:00', extendedProps: { status: 'blocked' } },
            { id: '18', title: 'Agendado - P12', start: '2025-08-22T17:00:00', end: '2025-08-22T18:00:00', extendedProps: { status: 'booked' } },
            { id: '19', title: 'Turno Extra - P13', start: '2025-08-22T18:00:00', end: '2025-08-22T19:00:00', extendedProps: { status: 'extra' } },
 
            // --- Miércoles, 23 de julio de 2025 (Pasado) ---
            { id: '20', title: 'Paciente Presente Deuda - P14', start: '2025-08-23T08:00:00', end: '2025-08-23T09:00:00', extendedProps: { status: 'debt' } },
            { id: '21', title: 'Disponible', start: '2025-08-23T09:00:00', end: '2025-08-23T10:00:00', extendedProps: { status: 'available' } },
            { id: '22', title: 'Agendado - P15', start: '2025-08-23T10:00:00', end: '2025-08-23T11:00:00', extendedProps: { status: 'booked' } },
            { id: '23', title: 'Bloqueado por Profesional', start: '2025-08-23T11:00:00', end: '2025-08-23T12:00:00', extendedProps: { status: 'blocked' } },
            { id: '24', title: 'No Tomado', start: '2025-08-23T14:00:00', end: '2025-08-23T15:00:00', extendedProps: { status: 'event-notTaken' } },
            { id: '25', title: 'Paciente Presente Pagado - P16', start: '2025-08-23T15:00:00', end: '2025-08-23T16:00:00', extendedProps: { status: 'paid' } },
            { id: '26', title: 'Agendado - P17', start: '2025-08-23T16:00:00', end: '2025-08-23T17:00:00', extendedProps: { status: 'booked' } },
            { id: '27', title: 'Disponible', start: '2025-08-23T17:00:00', end: '2025-08-23T18:00:00', extendedProps: { status: 'available' } },
 
            // --- Jueves, 24 de julio de 2025 (Pasado) ---
            { id: '28', title: 'Agendado - P18', start: '2025-08-24T08:00:00', end: '2025-08-24T09:00:00', extendedProps: { status: 'booked' } },
            { id: '29', title: 'Disponible', start: '2025-08-24T09:00:00', end: '2025-08-24T10:00:00', extendedProps: { status: 'available' } },
            { id: '30', title: 'Paciente Ausente - P19', start: '2025-08-24T10:00:00', end: '2025-08-24T11:00:00', extendedProps: { status: 'absent' } },
            { id: '31', title: 'Bloqueado por Profesional', start: '2025-08-24T11:00:00', end: '2025-08-24T12:00:00', extendedProps: { status: 'blocked' } },
            { id: '32', title: 'Cancelado - P20', start: '2025-08-24T14:00:00', end: '2025-08-24T15:00:00', extendedProps: { status: 'canceled' } },
            { id: '33', title: 'Paciente Presente Deuda - P21', start: '2025-08-24T15:00:00', end: '2025-08-24T16:00:00', extendedProps: { status: 'debt' } },
            { id: '34', title: 'Agendado - P22', start: '2025-08-24T16:00:00', end: '2025-08-24T17:00:00', extendedProps: { status: 'booked' } },
            { id: '35', title: 'No Tomado', start: '2025-08-24T17:00:00', end: '2025-08-24T18:00:00', extendedProps: { status: 'event-notTaken' } },
 
            // --- Viernes, 25 de julio de 2025 (HOY) ---
            { id: '36', title: 'Turno Extra - P23', start: '2025-08-25T07:00:00', end: '2025-08-25T08:00:00', extendedProps: { status: 'extra' } },
            { id: '37', title: 'Agendado - P24', start: '2025-08-25T08:00:00', end: '2025-08-25T09:00:00', extendedProps: { status: 'booked' } },
            { id: '38', title: 'Disponible', start: '2025-08-25T09:00:00', end: '2025-08-25T10:00:00', extendedProps: { status: 'available' } },
            { id: '39', title: 'Bloqueado por Profesional', start: '2025-08-25T10:00:00', end: '2025-08-25T11:00:00', extendedProps: { status: 'blocked' } },
            { id: '40', title: 'Agendado - P25', start: '2025-08-25T11:00:00', end: '2025-08-25T12:00:00', extendedProps: { status: 'booked' } },
            { id: '41', title: 'Disponible', start: '2025-08-25T14:00:00', end: '2025-08-25T15:00:00', extendedProps: { status: 'available' } },
            { id: '42', title: 'Turno Extra - P26', start: '2025-08-25T18:30:00', end: '2025-08-25T19:30:00', extendedProps: { status: 'extra' } },
 
 
            // --- Lunes, 28 de julio de 2025 (Futuro) ---
            { id: '43', title: 'Disponible', start: '2025-08-28T08:00:00', end: '2025-08-28T09:00:00', extendedProps: { status: 'available' } },
            { id: '44', title: 'Agendado - P27', start: '2025-08-28T09:00:00', end: '2025-08-28T10:00:00', extendedProps: { status: 'booked' } },
            { id: '45', title: 'Bloqueado por Profesional', start: '2025-08-28T10:00:00', end: '2025-08-28T11:00:00', extendedProps: { status: 'blocked' } },
            { id: '46', title: 'Disponible', start: '2025-08-28T14:00:00', end: '2025-08-28T15:00:00', extendedProps: { status: 'available' } },
 
            // --- Martes, 29 de julio de 2025 (Futuro) ---
            { id: '47', title: 'Agendado - P28', start: '2025-08-29T08:00:00', end: '2025-08-29T09:00:00', extendedProps: { status: 'booked' } },
            { id: '48', title: 'Disponible', start: '2025-08-29T09:00:00', end: '2025-08-29T10:00:00', extendedProps: { status: 'available' } },
 
            // --- Miércoles, 30 de julio de 2025 (Futuro) ---
            { id: '49', title: 'Bloqueado por Profesional', start: '2025-08-30T09:00:00', end: '2025-08-30T10:00:00', extendedProps: { status: 'blocked' } },
            { id: '50', title: 'Agendado - P29', start: '2025-08-30T10:00:00', end: '2025-08-30T11:00:00', extendedProps: { status: 'booked' } },
        ],
        // --- AQUÍ TERMINA EL ARRAY DE EVENTOS ---
        */


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
            }else if (info.event.extendedProps.status === 'extra') { // not taken
                info.el.classList.add('fc-event-extra');
            }
        },
        select: function (info) {
            alert('Has seleccionado desde ' + info.startStr + ' hasta ' + info.endStr);
        },
        /*eventClick: function (info) {
            alert('Has hecho clic en: ' + info.event.title + ' (ID: ' + info.event.id + ')');
        }*/
    });

    calendar.render();
});

// Función que manejará la lógica del clic
function handleSlotClick(event) {
    const status = event.extendedProps.status; 
    const slotId = event.id; 
    const startTimeStr = event.startStr;

    const formattedTime = startTimeStr ? startTimeStr.slice(11, 16) : 'Hora desconocida';

    if (status === 'available') {        
        console.log(`Slot disponible clicado. ID: ${slotId}, Hora: ${formattedTime}`);   
        showReservationModal(slotId, formattedTime);        
    } else {
        alert(`Este horario (${formattedTime}) está ${status} y no puede ser reservado.`);
    }
}

function showReservationModal(slotId, formattedTime) {
    // Asume que tienes un modal con el ID 'reservationModal' en tu HTML
    const modalElement = document.getElementById('reservationModal');
    
    // 1. Mostrar la hora en el título o cuerpo del modal
    document.getElementById('modalTitle').textContent = `Reservar turno a las ${formattedTime}`;
    
    // 2. Almacenar el slotId en un campo oculto o en un atributo del botón de guardar
    // Esto es CLAVE para enviarlo en la petición POST
    document.getElementById('slotIdInput').value = slotId;

    // 3. Abrir el modal (ejemplo con Bootstrap)
    const reservationModal = new bootstrap.Modal(modalElement);
    reservationModal.show();
}

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
            const rawEvents =  data.original;
            console.log('Datos recibidos del backend:', rawEvents);

            // Aquí debes procesar los datos para FullCalendar
            // La API debe devolver un formato que FullCalendar entienda
            const events = rawEvents.map(item => ({
                id: item.id,
                title: item.title,
                status: item.extendedProps?.status,
                start: item.start_time,
                end: item.end_time,
            }));

            // Limpia los eventos existentes y añade los nuevos
            calendar.removeAllEvents();
            calendar.addEventSource(events);
        })
        .catch(error => {
            console.error('Error fetching calendar data:', error);
        });
}