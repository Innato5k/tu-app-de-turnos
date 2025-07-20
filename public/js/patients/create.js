document.addEventListener('DOMContentLoaded', async () => {
     // URLs de la API
    const API_PATIENTS_BASE_URL = '/api/patients'; 
    const REDIRECT_LOGIN_URL = '/login'; // Ruta de login para redireccionar en caso de token inválido
    const REDIRECT_PATIENTS_LIST_URL = '/patients'; // Ruta para volver al listado después de guardar

    // Elementos del DOM
    const createPatientForm = document.getElementById('createPatientForm');
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

    // Manejador de envío del formulario de edición
    createPatientForm.addEventListener('submit', async (e) => {
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
            const response = await fetch(`${API_PATIENTS_BASE_URL}`, {
                method: 'POST',
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
});