import Chart from 'chart.js/auto';


const REDIRECT_LOGIN_URL = '/login'; // Ruta a la página de inicio de sesión (web)

function getAuthToken() {
    return localStorage.getItem('auth_token');
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
}

// Marcamos la función como ASYNC para poder usar AWAIT
window.initDashboard = async function () {
    const token = getAuthToken();

    if (!token) {
        // Asumiendo que redirectToLogin está definida en otro lado
        redirectToLogin('Por favor, inicia sesión para acceder al dashboard.');
        return;
    }

    try {
        const response = await fetch('/api/dashboard/summary', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            }
        });
        if (response.status === 401) {
            redirectToLogin('Tu sesión ha expirado. Por favor, ingresa nuevamente.');
            return;
        }

        if (!response.ok) {
            console.error('Error en la respuesta del servidor');
            return;
        }

        const data = await response.json();
        console.log(data);
        // Ejecutamos los renders con la data limpia
        renderWidgets(data.stats.financial);
        renderAttendanceChart(data.stats.attendance);
        renderDebtors(data.stats.debtors);
        renderNextAppointment(data.next_appointment);
        renderQuote(data.quote);
        renderAnnouncements(data.announcements);

    } catch (error) {
        console.error('Error cargando el Dashboard:', error);
    }
}
function renderQuote(quote) {
    const container = document.getElementById('quote-container');
    if (container && quote) {
        container.innerHTML = `<blockquote class="blockquote">
            <p>"${quote.text}"</p>
            <footer class="blockquote-footer">${quote.author}</footer>
        </blockquote>`;
    }
}

function renderAnnouncements(announcements) {
    const container = document.getElementById('announcements-container');
    if (container) {
        container.innerHTML = announcements.map(a => `
            <div class="alert alert-${a.type} mb-2">
                <strong>${a.title}</strong>: ${a.message}
                <br>
                <br>
                <small>Por: ${a.author}</small>
            </div>
        `).join('');
    }
}
// Las funciones de renderizado quedan igual (están perfectas)
function renderWidgets(financial) {
    document.getElementById('widget-facturacion').innerText = `$${financial.total_facturado.toLocaleString()}`;
    document.getElementById('widget-deuda').innerText = `$${financial.total_adeudado.toLocaleString()}`;
    document.getElementById('widget-ratio').innerText = `${financial.ratio_deuda}%`;
}

function renderAttendanceChart(attendance) {
    const ctx = document.getElementById('attendanceChart').getContext('2d');

    // Obtenemos los estados únicos (Labels: PRESENT, ABSENT, etc.)
    const states = [...new Set(attendance.map(a => a.status))];
    // 1. Definís el diccionario de traducciones
    const statusTranslations = {
        'booked': 'Reservado',
        'attended': 'Atendido',
        'absent': 'Ausente',
        'cancelled': 'Cancelado'
    };
    const states2 = [...new Set(attendance.map(a => a.status))]
        .map(status => statusTranslations[status] || status);


    // Dataset 1: Turnos Normales (is_extra = 0)
    const normales = states.map(state => {
        const registro = attendance.find(a => a.status === state && (a.is_extra == 0 || a.is_extra == false));
        return registro ? parseInt(registro.total) : 0;
    });

    // Dataset 2: Turnos Extras (is_extra = 1)
    const extras = states.map(state => {
        const registro = attendance.find(a => a.status === state && (a.is_extra == 1 || a.is_extra == true));
        return registro ? parseInt(registro.total) : 0;
    });

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: states2.map(s => s.toUpperCase()),
            datasets: [
                {
                    label: 'Normales',
                    data: normales,
                    backgroundColor: '#2883f1' // Azul
                },
                {
                    label: 'Extras',
                    data: extras,
                    backgroundColor: '#fb6124' // Amarillo/Naranja
                }
            ]
        },
        options: {
            scales: {
                x: { stacked: true }, // <--- Esto los pone uno encima del otro
                y: { stacked: true }
            }
        }
    });
}

function renderDebtors(debtors) {
    const list = document.getElementById('debtors-list');
    if (!list) return;
    list.innerHTML = debtors.map(d => `
        <li class="list-group-item d-flex justify-content-between align-items-center small">
            ${d.patient.last_name}, ${d.patient.name}
            
        </li>
    `).join('');
}

function renderNextAppointment(next) {
    const box = document.getElementById('next-appointment-box');
    const title = document.querySelector('#next-appointment-card-title');
    const dateDisplay = formatAppointmentDate(next ? next.start_time : null);
    if (!box) return;
    if (next && next.remaining_time) {
        // Actualizamos el título con la cuenta regresiva
        if (next.remaining_time.length > 5) { // Si el formato es HH:MM
            title.innerText = `Próximo turno en: ${next.remaining_time}`;
        } else {
            title.innerText = `Próximo turno en: ${next.remaining_time}hs`;
        }

        box.innerHTML = `
            <div class="d-flex align-items-center">
                <div class="flex-grow-1 ">
                    <h4 class="mb-0 text-dark p-2">${next.patient.full_name} , ${dateDisplay}</h4>
                    <span class="text-muted"><i class="far fa-clock"></i> </span>
                </div>
            </div>
        `;
    } else {
        if (title) title.innerText = "Próximo turno";
        box.innerHTML = '<p class="text-muted mb-0">No hay más turnos agendados para hoy.</p>';
    }
}

function formatAppointmentDate(dateString) {
    // dateString llega como "2026-05-11 21:00"
    // Reemplazamos el espacio por 'T' para que sea un formato ISO válido si es necesario
    const appointmentDate = new Date(dateString.replace(' ', 'T'));
    const now = new Date();

    // Formateamos la hora HH:mm
    const hours = String(appointmentDate.getHours()).padStart(2, '0');
    const minutes = String(appointmentDate.getMinutes()).padStart(2, '0');
    const timeStr = `${hours}:${minutes}`;

    // Comparamos si es el mismo día, mes y año
    const isToday = appointmentDate.getDate() === now.getDate() &&
        appointmentDate.getMonth() === now.getMonth() &&
        appointmentDate.getFullYear() === now.getFullYear();

    if (isToday) {
        return `Comienza a las ${timeStr}hs`;
    } else {
        const day = String(appointmentDate.getDate()).padStart(2, '0');
        const month = String(appointmentDate.getMonth() + 1).padStart(2, '0'); // Enero es 0
        const year = appointmentDate.getFullYear();
        return `Comienza a las ${timeStr}hs del ${day}-${month}-${year}`;
    }
}