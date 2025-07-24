// Importaciones correctas de FullCalendar y sus plugins desde node_modules
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import bootstrap5Plugin from '@fullcalendar/bootstrap5';
import esLocale from '@fullcalendar/core/locales/es'; // Importa el locale de español


document.addEventListener('DOMContentLoaded', function () {
    console.log('Script de horarios cargado. Inicializando FullCalendar.');

    var calendarEl = document.getElementById('Calendar');

    var calendar = new Calendar(calendarEl, {
        // Añadir los plugins que instalamos
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
        slotMaxTime: '20:00:00', // Horario de fin del calendario
        slotDuration: '00:30    :00', // Duración de cada slot (ej. 30 minutos)
        editable: true, // Permite arrastrar y redimensionar eventos (útil para gestionar turnos)
        selectable: true, // Permite seleccionar rangos de tiempo
        nowIndicator: true, // Muestra la línea de la hora actual
        allDaySlot: false, // Oculta la sección "todo el día"
        eventClassNames: 'fc-event-custom', // Clase CSS personalizada para eventos

        // Opciones de estilo para Bootstrap 5
        themeSystem: 'bootstrap5',

        // Aquí irían tus eventos/horarios reales.
        // Por ahora, solo algunos ejemplos para que veas cómo se visualiza.
        events: [
            {
                id: '1',
                title: 'Bloque Disponible',
                start: '2025-07-23T09:00:00',
                end: '2025-07-23T10:00:00',
                extendedProps: {
                    status: 'present'
                }
            },
            {
                id: '2',
                title: 'Sesión con Paciente X',
                start: '2025-07-24T11:00:00',
                end: '2025-07-24T12:30:00',
                extendedProps: {
                    status: 'booked'
                }
            },
            {
                id: '3',
                title: '-Bloqueo para Almuerzo',
                start: '2025-07-25T13:00:00',
                end: '2025-07-25T14:00:00',
                extendedProps: {
                    status: 'debt'
                }
            },
            // Asegúrate de que las fechas aquí correspondan con tu "semana actual" para verlos
            // Los Toldos, Argentina - la semana actual es del 21 al 27 de julio de 2025
            {
                id: '4',
                title: '--Disponible',
                start: '2025-07-21T15:00:00',
                end: '2025-07-21T16:00:00',
                extendedProps: {
                    status: 'paid'
                }
            },
            {
                id: '5',
                title: 'Ocupado',
                start: '2025-07-26T10:30:00',
                end: '2025-07-26T11:30:00',
                extendedProps: {
                    status: 'absent'
                }
            },
        ],

        eventDidMount: function (info) {
            if (info.event.extendedProps.status === 'available') {
                info.el.classList.add('fc-event-available');
            } else if (info.event.extendedProps.status === 'booked') {
                info.el.classList.add('fc-event-booked');
            } else if (info.event.extendedProps.status === 'blocked') {
                info.el.classList.add('fc-event-blocked');
            } else if (info.event.extendedProps.status === 'present') { // Nuevo
                info.el.classList.add('fc-event-present');
            } else if (info.event.extendedProps.status === 'absent') { // Nuevo
                info.el.classList.add('fc-event-absent');
            } else if (info.event.extendedProps.status === 'paid') { // Nuevo
                info.el.classList.add('fc-event-paid');
            } else if (info.event.extendedProps.status === 'debt') { // Nuevo
                info.el.classList.add('fc-event-debt');
            }
        },
        select: function (info) {
            alert('Has seleccionado desde ' + info.startStr + ' hasta ' + info.endStr);
        },
        eventClick: function (info) {
            alert('Has hecho clic en: ' + info.event.title + ' (ID: ' + info.event.id + ')');
        }
    });

    calendar.render();
});