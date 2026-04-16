
document.addEventListener('DOMContentLoaded', async () => {
    // Obtener el ID del usuario de la URL
    // Asume que la URL es algo como /users/{id}/edit
    const pathSegments = window.location.pathname.split('/');
    const userId = pathSegments[pathSegments.length - 2];

    // URLs de la API
    const API_userS_BASE_URL = '/api/users'; // Base para GET y PUT
    const REDIRECT_LOGIN_URL = '/login'; // Ruta de login para redireccionar en caso de token inválido
    const REDIRECT_userS_LIST_URL = '/users'; // Ruta para volver al listado después de guardar

    // Elementos del DOM
    const createuserForm = document.getElementById('createuserForm');
    const userIdInput = document.getElementById('userId');
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
    const is_activeInput = document.getElementById('is_active');
    const roleInput = document.getElementById('role');

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

    // Función para cargar los datos del usuario
    async function loaduserData() {
        const token = getAuthToken();
        if (!token) {
            alert('No autenticado. Por favor, inicia sesión.'); // Reemplazar con modal personalizado
            window.location.href = REDIRECT_LOGIN_URL;
            return;
        }


        showMessage('Cargando datos del usuario...', 'info');
        saveuserButton.disabled = true;

        try {
            const response = await fetch(`${API_userS_BASE_URL}/${userId}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
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
                throw new Error(errorData.message || 'Error al cargar los datos del usuario.');
            }

            const user = await response.json();

            // Llenar el formulario con los datos del usuario
            userIdInput.value = user.data.id;
            nameInput.value = user.data.name || '';
            lastNameInput.value = user.data.last_name || '';
            cuilInput.value = user.data.cuil ? formatCuil(user.data.cuil) : '';
            emailInput.value = user.data.email || '';
            phoneInput.value = user.data.phone || '';
            phoneOptInput.value = user.data.phone_opt || '';
            birthDateInput.value = user.data.birth_date ? new Date(user.data.birth_date).toISOString().split('T')[0] : '';
            genderInput.value = user.data.gender || '';
            nationalMdLicInput.value = user.data.national_md_lic || '';
            provincialMdLicInput.value = user.data.provincial_md_lic || '';
            specialityInput.value = user.data.speciality || '';
            is_activeInput.checked = (user.data.is_active);   
            roleInput.value = user.data.role || '';

            showMessage(''); // Limpiar mensaje de carga
            saveuserButton.disabled = false;

        } catch (error) {
            console.error('Error al cargar usuario:', error);
            showMessage(`Error: ${error.message}`, 'danger');
            saveuserButton.disabled = true; // Mantener deshabilitado si no se pudieron cargar los datos
        }
    }

    function calculateAge(birthDate) {
        const today = new Date();
        const birth = new Date(birthDate);
        let age = today.getFullYear() - birth.getFullYear();
        const m = today.getMonth() - birth.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) {
            age--;
        }
        return age;
    }

    // Manejador de envío del formulario de edición
    edituserForm.addEventListener('submit', async (e) => {
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
        if (fechNac > new Date().toISOString().split('T')[0]) {
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
            gender: genderInput.value,
            national_md_lic: nationalMdLicInput.value,
            provincial_md_lic: provincialMdLicInput.value,
            speciality: specialityInput.value,
            is_active: is_activeInput.checked,
            role: roleInput.value
        };

        try {
            const response = await fetch(`${API_userS_BASE_URL}/${userId}`, {
                method: 'PUT', // Usar PUT para actualización
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify(updateduserData)
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

            // Si la respuesta es 200 OK (sin contenido) o devuelve el usuario actualizado
            showMessage('usuario actualizado exitosamente.', 'success');

        } catch (error) {
            console.error('Error al guardar usuario:', error);
            showMessage(`Error: ${error.message}`, 'danger');
        } finally {
            saveuserButton.disabled = false;
            saveuserButton.textContent = 'Guardar Cambios';
        }
    });

    // Cargar los datos del usuario al iniciar la página
    if (userId) {
        loaduserData();
    } else {
        showMessage('Error: ID de usuario no encontrado en la URL.', 'danger');
        saveuserButton.disabled = true;
    }
});