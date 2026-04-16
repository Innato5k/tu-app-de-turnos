document.addEventListener('DOMContentLoaded', async () => {
     // URLs de la API
    const API_userS_BASE_URL = '/api/users'; 
    const REDIRECT_LOGIN_URL = '/login'; // Ruta de login para redireccionar en caso de token inválido
    const REDIRECT_userS_LIST_URL = '/users'; // Ruta para volver al listado después de guardar

    // Elementos del DOM
    const createuserForm = document.getElementById('createuserForm');
    const nameInput = document.getElementById('name');
    const lastNameInput = document.getElementById('last_name');
    const cuilInput = document.getElementById('cuil');    
    const birthDateInput = document.getElementById('birth_date');    
    const genderInput = document.getElementById('gender');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');
    const phoneOptInput = document.getElementById('phone_opt');
    const nationalMdLicInput = document.getElementById('national_md_lic');
    const provincialMdLicInput = document.getElementById('provincial_md_lic');
    const specialityInput = document.getElementById('speciality');
    

    // Función para obtener el token JWT del localStorage
    function getAuthToken() {
        return localStorage.getItem('auth_token');
    }

    // Función para mostrar mensajes al usuario
    function showMessage(message, type = 'info') {
        userMessage.textContent = message;
        userMessage.classList.remove('text-success', 'text-danger', 'text-info');
        userMessage.classList.add(`text-${type}`);
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
    createuserForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const token = getAuthToken();
        if (!token) {
            alert('No autenticado. Por favor, inicia sesión.'); // Reemplazar con modal personalizado
            window.location.href = REDIRECT_LOGIN_URL;
            return;
        }
        if (!nameInput.value || !lastNameInput.value || !cuilInput.value) {
            showMessage('Por favor, completa todos los campos obligatorios.', 'danger');
            return;
        }
        const fechNac = new Date(birthDateInput.value).toISOString().split('T')[0]
        if ( fechNac > new Date().toISOString().split('T')[0]) {
            showMessage('La fecha de nacimiento no puede ser futura.', 'danger');
            return;
        }

        saveuserButton.disabled = true;
        saveuserButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...';
        showMessage('', 'info'); // Limpiar mensajes anteriores

        // Nuevo: Limpiar el CUIL antes de enviarlo al backend (quitar guiones)
        const cleanCuil = cuilInput.value.replace(/\D/g, '');

        const updateduserData = {
            name: nameInput.value,
            last_name: lastNameInput.value,
            cuil: cleanCuil,
            email: emailInput.value,
            phone: phoneInput.value,
            phone_opt: phoneOptInput.value,
            birth_date: birthDateInput.value ? new Date(birthDateInput.value).toISOString() : null,
            national_md_lic: nationalMdLicInput.value,
            provincial_md_lic: provincialMdLicInput.value,
            speciality: specialityInput.value,  
            gender: genderInput.value
        };

        try {
            const response = await fetch(`${API_userS_BASE_URL}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify(updateduserData)
            });

            if (response.status === 401 || response.status === 403) {
                alert('Sesión expirada o no autorizada. Por favor, inicia sesión de nuevo.'); //TODO: Reemplazar con modal personalizado
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

            // Si la respuesta es 200 OK (sin contenido) o devuelve el usuario actualizado
            showMessage('usuario actualizado exitosamente.', 'success');
            // Opcional: Recargar los datos del usuario o redirigir
            setTimeout(() => {
                window.location.href = REDIRECT_userS_LIST_URL; // Redirige al listado
            }, 1500);

        } catch (error) {
            console.error('Error al guardar usuario:', error);
            showMessage(`Error: ${error.message}`, 'danger');
        } finally {
            saveuserButton.disabled = false;
            saveuserButton.textContent = 'Guardar Cambios';
        }
    });    
});