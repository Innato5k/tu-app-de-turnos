<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PsicoTurnos')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body class="d-flex flex-column min-vh-100">

    <body class="d-flex flex-column min-vh-100">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-md navbar-light bg-light shadow-sm py-2">
            <div class="container-fluid">
                <!-- Botón de menú hamburguesa (visible en pantallas pequeñas) -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <!-- Logo o nombre de la app -->
                <a class="navbar-brand ms-2 text-info fw-bold fs-5" href="#">PsicoTurnos</a>


                <!-- Contenido de la navbar colapsable -->
                <div  class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a  id="linkInicio" class="d-none nav-link text-secondary fw-medium px-2 py-1" href="#">Inicio</a>
                        </li>
                        <li class="nav-item">
                            <a  id="linkMisTurnos" class="d-none nav-link text-secondary fw-medium px-2 py-1" href="#">Mis Turnos</a>
                        </li>
                        <li class="nav-item">
                            <a id="linkPacientes" class="d-none nav-link text-secondary fw-medium px-2 py-1" href="#">Mis Pacientes</a>
                        </li>
                        <li class="nav-item">
                            <a  id="linkContactos" class="d-none nav-link text-secondary fw-medium px-2 py-1" href="#">Contacto</a>
                        </li>
                    </ul>
                    <!-- Parte derecha: Usuario y opciones de logout (visible en pantallas grandes) -->
                    <div class="d-flex align-items-center">
                        {{-- Elementos que se mostrarán/ocultarán con JS --}}
                        <span id="userNameDisplay" class="text-dark fw-medium me-2 d-none d-md-block small"></span>
                        <button id="logoutButton" class="btn btn-danger btn-sm fw-semibold py-1 px-3 d-none">
                            Cerrar Sesión
                        </button>
                        <a id="loginLink" href="{{ route('login') }}" class="btn btn-info btn-sm fw-semibold py-1 px-3 d-none text-white">
                            Iniciar Sesión
                        </a>
                    </div>
                </div>


            </div>
        </nav>


        <!-- Contenido principal de la página -->
        <main class="flex-grow-1 d-flex align-items-center justify-content-center py-4 px-2">
            @yield('content') {{-- Aquí se inyectará el contenido de las vistas específicas --}}
        </main>

        <!-- Footer 
    <footer class="bg-light text-center text-secondary py-3">
        <div class="container">
            <p class="mb-0">© 2023 PsicoTurnos. Todos los derechos reservados.</p>
            <p class="mb-0">Desarrollado por Tu Nombre</p>
        </div>-->

        <!-- Enlace a Bootstrap JS (Popper.js y Bootstrap bundle) -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            crossorigin="anonymous"></script>
        @yield('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const authToken = localStorage.getItem('auth_token');
                const userInfo = localStorage.getItem('user_info');
                const userNameDisplay = document.getElementById('userNameDisplay');
                const logoutButton = document.getElementById('logoutButton');
                const loginLink = document.getElementById('loginLink');
                const linkInicio = document.getElementById('linkInicio');
                const linkMisTurnos = document.getElementById('linkMisTurnos');
                const linkPacientes = document.getElementById('linkPacientes');
                const linkContactos = document.getElementById('linkContactos');

                if (authToken && userInfo) {
                    // Si hay un token y info de usuario, el usuario está "logueado" en el front-end
                    try {
                        const user = JSON.parse(userInfo);
                        userNameDisplay.textContent = `Hola, ${user.name}!`;
                        userNameDisplay.classList.remove('d-none'); // Muestra el nombre
                        logoutButton.classList.remove('d-none'); // Muestra el botón de logout
                        loginLink.classList.add('d-none'); // Oculta el enlace de login
                        linkInicio.classList.remove('d-none'); // Oculta el menú de navegación no autenticado
                        linkMisTurnos.classList.remove('d-none');
                        linkPacientes.classList.remove('d-none');
                        linkContactos.classList.remove('d-none');
                    } catch (e) {
                        console.error("Error al parsear user_info:", e);
                        userNameDisplay.classList.add('d-none');
                        logoutButton.classList.add('d-none');
                        loginLink.classList.remove('d-none');
                        linkInicio.classList.add('d-none');
                        linkMisTurnos.classList.add('d-none');
                        linkPacientes.classList.add('d-none');
                        linkContactos.classList.add('d-none');
                    }
                } else {
                    // Si no hay token, el usuario no está logueado
                    userNameDisplay.classList.add('d-none');
                    logoutButton.classList.add('d-none');
                    loginLink.classList.remove('d-none'); 
                    linkInicio.classList.add('d-none');
                    linkMisTurnos.classList.add('d-none');
                    linkPacientes.classList.add('d-none');
                    linkContactos.classList.add('d-none');
                }

                // Manejador del botón de Cerrar Sesión
                logoutButton.addEventListener('click', () => {
                    // Elimina el token y la info del usuario del localStorage
                    localStorage.removeItem('auth_token');
                    localStorage.removeItem('token_type');
                    localStorage.removeItem('user_info');

                    // Redirige a la página de login (o a la raíz si el login es la raíz)
                    window.location.href = "{{ route('login') }}";
                });
            });
        </script>

    </body>

</html>