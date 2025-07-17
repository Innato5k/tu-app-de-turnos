
document.addEventListener('DOMContentLoaded', async () => {
    // Obtener el ID del paciente de la URL
    // Asume que la URL es algo como /patients/{id}/edit
    const pathSegments = window.location.pathname.split('/');
    const patientId = pathSegments[pathSegments.length - 2]; 

    // URLs de la API
    const API_PATIENTS_BASE_URL = '/api/patients'; // Base para GET y PUT
    const REDIRECT_LOGIN_URL = '/login'; // Ruta de login para redireccionar en caso de token inválido
    const REDIRECT_PATIENTS_LIST_URL = '/patients'; // Ruta para volver al listado después de guardar

    // Elementos del DOM
    const editPatientForm = document.getElementById('editPatientForm');
    const patientIdInput = document.getElementById('patientId');
    const nameInput = document.getElementById('name');
    const lastNameInput = document.getElementById('last_name');
    const cuilInput = document.getElementById('cuil');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');
    const phoneOptInput = document.getElementById('phone_opt');
    const observationsInput = document.getElementById('observations');
    const birthDateInput = document.getElementById('birth_date');
    const genderInput = document.getElementById('gender');
    const addressInput = document.getElementById('address');
    const cityInput = document.getElementById('city');
    const provinceInput = document.getElementById('province');
    const postalCodeInput = document.getElementById('postal_code');
    const medicalCoverageInput = document.getElementById('medical_coverage');
    const savePatientButton = document.getElementById('savePatientButton');
    const patientMessage = document.getElementById('patientMessage');

    // Función para obtener el token JWT del localStorage
    function getAuthToken() {
        return localStorage.getItem('auth_token');
    }

    // Función para mostrar mensajes al usuario
    function showMessage(message, type = 'info') {
        patientMessage.textContent = message;
        patientMessage.classList.remove('text-success', 'text-danger', 'text-info');
        patientMessage.classList.add(`text-${type}`);
    }

    // Función para formatear el CUIL (XX-XXXXXXXX-X)
    function formatCuil(value) {
        // Limpia el valor, dejando solo dígitos
        const cleanValue = value.replace(/\D/g, '');
        let formattedValue = '';

        if (cleanValue.length > 0) {
            formattedValue = cleanValue.substring(0, 2); // Primeros 2 dígitos
            if (cleanValue.length > 2) {
                formattedValue += '-' + cleanValue.substring(2, 10); // Siguientes 8 dígitos
            }
            if (cleanValue.length > 10) {
                formattedValue += '-' + cleanValue.substring(10, 11); // Último dígito
            }
        }
        return formattedValue;
    }

    // Event listener para formatear el CUIL mientras se escribe
    cuilInput.addEventListener('input', (e) => {
        const cursorPosition = e.target.selectionStart; // Guarda la posición del cursor
        const originalLength = e.target.value.length;

        e.target.value = formatCuil(e.target.value);

        // Ajusta la posición del cursor después del formato
        const newLength = e.target.value.length;
        const lengthDifference = newLength - originalLength;
        e.target.setSelectionRange(cursorPosition + lengthDifference, cursorPosition + lengthDifference);
    });

    // Función para cargar los datos del paciente
    async function loadPatientData() {
        const token = getAuthToken();
        if (!token) {
            alert('No autenticado. Por favor, inicia sesión.'); // Reemplazar con modal personalizado
            window.location.href = REDIRECT_LOGIN_URL;
            return;
        }

        showMessage('Cargando datos del paciente...', 'info');
        savePatientButton.disabled = true;

        try {
            const response = await fetch(`${API_PATIENTS_BASE_URL}/${patientId}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            if (response.status === 401 || response.status === 403) {
                alert('Sesión expirada o no autorizada. Por favor, inicia sesión de nuevo.'); // Reemplazar con modal personalizado
                localStorage.removeItem('auth_token');
                localStorage.removeItem('token_type');
                localStorage.removeItem('user_info');
                window.location.href = REDIRECT_LOGIN_URL;
                return;
            }

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Error al cargar los datos del paciente.');
            }

            const patient = await response.json();

            // Llenar el formulario con los datos del paciente
            patientIdInput.value = patient.id;
            nameInput.value = patient.name || '';
            lastNameInput.value = patient.last_name || '';
            cuilInput.value = patient.cuil ? formatCuil(patient.cuil) : '';
            emailInput.value = patient.email || '';
            phoneInput.value = patient.phone || '';
            phoneOptInput.value = patient.phone_opt || '';
            observationsInput.value = patient.observations || '';
            birthDateInput.value = patient.birth_date ? new Date(patient.birth_date).toISOString().split('T')[0] : '';
            genderInput.value = patient.gender || '';
            addressInput.value = patient.address || '';
            cityInput.value = patient.city || '';
            provinceInput.value = patient.province || '';
            postalCodeInput.value = patient.postal_code || '';
            medicalCoverageInput.value = patient.medical_coverage || '';


            showMessage(''); // Limpiar mensaje de carga
            savePatientButton.disabled = false;

        } catch (error) {
            console.error('Error al cargar paciente:', error);
            showMessage(`Error: ${error.message}`, 'danger');
            savePatientButton.disabled = true; // Mantener deshabilitado si no se pudieron cargar los datos
        }
    }

    // Manejador de envío del formulario de edición
    editPatientForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const token = getAuthToken();
        if (!token) {
            alert('No autenticado. Por favor, inicia sesión.'); // Reemplazar con modal personalizado
            window.location.href = REDIRECT_LOGIN_URL;
            return;
        }

        savePatientButton.disabled = true;
        savePatientButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...';
        showMessage('', 'info'); // Limpiar mensajes anteriores

        // Nuevo: Limpiar el CUIL antes de enviarlo al backend (quitar guiones)
        const cleanCuil = cuilInput.value.replace(/\D/g, '');

        const updatedPatientData = {
            name: nameInput.value,
            last_name: lastNameInput.value,
            cuil: cleanCuil,
            email: emailInput.value,
            phone: phoneInput.value,
            phone_opt: phoneOptInput.value,
            observations: observationsInput.value,
            birth_date: birthDateInput.value ? new Date(birthDateInput.value).toISOString() : null,
            gender: genderInput.value,
            address: addressInput.value,
            city: cityInput.value,
            province: provinceInput.value,
            postal_code: postalCodeInput.value,
            medical_coverage: medicalCoverageInput.value,
            // Si hay otros campos, agrégalos aquí
        };

        try {
            const response = await fetch(`${API_PATIENTS_BASE_URL}/${patientId}`, {
                method: 'PUT', // Usar PUT para actualización
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify(updatedPatientData)
            });

            if (response.status === 401 || response.status === 403) {
                alert('Sesión expirada o no autorizada. Por favor, inicia sesión de nuevo.'); // Reemplazar con modal personalizado
                localStorage.removeItem('auth_token');
                localStorage.removeItem('token_type');
                localStorage.removeItem('user_info');
                window.location.href = REDIRECT_LOGIN_URL;
                return;
            }

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Error al guardar los cambios.');
            }

            // Si la respuesta es 200 OK (sin contenido) o devuelve el paciente actualizado
            showMessage('Paciente actualizado exitosamente.', 'success');
            // Opcional: Recargar los datos del paciente o redirigir
            setTimeout(() => {
                window.location.href = REDIRECT_PATIENTS_LIST_URL; // Redirige al listado
            }, 1500);

        } catch (error) {
            console.error('Error al guardar paciente:', error);
            showMessage(`Error: ${error.message}`, 'danger');
        } finally {
            savePatientButton.disabled = false;
            savePatientButton.textContent = 'Guardar Cambios';
        }
    });

    // Cargar los datos del paciente al iniciar la página
    if (patientId) {
        loadPatientData();
    } else {
        showMessage('Error: ID de paciente no encontrado en la URL.', 'danger');
        savePatientButton.disabled = true;
    }
});