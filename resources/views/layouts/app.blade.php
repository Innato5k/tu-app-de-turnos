<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PsicoTurnos')</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite([ //cargar los archivos CSS y JS necesarios
    'resources/css/app.css',
    'resources/css/style.css',
    'resources/js/app.js',
    ])

</head>

<body class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow">
        <div class="container-fluid">
            <a class="navbar-brand" href="/dashboard">
                <i class="bi bi-calendar-check text-primary"></i> <strong>PsicoTurnos</strong>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <!-- Todos inician con d-none, el JS los activa -->
                    <li class="nav-item">
                        <a id="linkUsuarios" class="nav-link text-info d-none" href="/users">
                            <i class="bi bi-person-gear"></i> Usuarios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a id="linkMisTurnos" class="nav-link d-none" href="/schedules"><i class="bi bi-calendar3"></i> Calendario</a>
                    </li>
                    <li class="nav-item">
                        <a id="linkHorariosProfesional" class="nav-link  d-none" href="/professionalSchedules">
                            <i class="bi bi-clock-history"></i> Horarios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a id="linkPacientes" class="nav-link d-none" href="/patients"><i class="bi bi-people"></i> Pacientes</a>
                    </li>
                </ul>

                <ul class="navbar-nav align-items-center">
                    <!-- Link de Login: visible solo si no hay token -->
                    <li class="nav-item">
                        <a id="loginLink" href="/login" class="nav-link text-info d-none">Iniciar Sesión</a>
                    </li>

                    <!-- Dropdown de Usuario: visible solo con sesión -->
                    <li class="nav-item dropdown d-none" id="userDropdownItem">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userMenuDropdown" role="button" data-bs-toggle="dropdown">
                            <span id="userNameDisplay" class="me-2 text-white text-capitalize"></span>
                            <img src="/img/user-icon.png" alt="Perfil" width="32" height="32" class="rounded-circle border border-2 border-primary">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li><a class="dropdown-item" href="/perfil/datos"><i class="bi bi-person-gear"></i> Mis datos</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <!-- Botón de logout ahora sin d-none interno, lo controla el padre -->
                                <button id="logoutButton" class="dropdown-item text-danger border-0 bg-transparent w-100 text-start">
                                    <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                                </button>
                            </li>
                        </ul>
                    </li>
                </ul>
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

    @yield('scripts')



</body>

</html>