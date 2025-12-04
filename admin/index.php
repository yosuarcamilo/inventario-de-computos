<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario - Panel de Administración</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
        }

        .sidebar {
            width: 280px;
            height: 100vh;
            background-color: #2c3e50;
            color: white;
            position: fixed;
            left: 0;
            top: 0;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid #34495e;
            position: relative;
        }

        .sidebar-header h1 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .sidebar-header p {
            color: #bdc3c7;
            font-size: 14px;
        }

        .logout-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #e74c3c;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            transition: background-color 0.3s;
        }

        .logout-btn:hover {
            background: #c0392b;
        }

        .sidebar-section {
            padding: 20px;
            border-bottom: 1px solid #34495e;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            color: #95a5a6;
            margin-bottom: 15px;
            letter-spacing: 1px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 12px 0;
            cursor: pointer;
            transition: background-color 0.3s;
            border-radius: 5px;
            padding-left: 10px;
        }

        .menu-item:hover {
            background-color: #34495e;
        }

        .menu-item.active {
            background-color: #3498db;
        }

        .menu-item i {
            margin-right: 15px;
            width: 20px;
            text-align: center;
        }

        .dropdown {
            position: relative;
            margin-bottom: 15px;
        }

        .dropdown-select {
            width: 100%;
            padding: 10px;
            background-color: #34495e;
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        .dropdown-select:focus {
            outline: none;
        }

        .sede-info {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }

        .sede-info i {
            margin-right: 10px;
            color: #3498db;
        }

        .main-content {
            margin-left: 280px;
            padding: 20px;
            min-height: 100vh;
        }

        .device-options {
            background-color: #34495e;
            border-radius: 5px;
            padding: 15px;
            margin-top: 10px;
        }

        .device-option {
            display: flex;
            align-items: center;
            padding: 12px 0;
            cursor: pointer;
            transition: background-color 0.3s;
            border-radius: 5px;
            padding-left: 10px;
        }

        .device-option:hover {
            background-color: #2c3e50;
        }

        .add-device-btn {
            background-color: transparent;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 15px;
            transition: background-color 0.3s;
            width: 100%;
            text-align: left;
        }

        .add-device-btn:hover {
            background-color: #34495e;
        }

        .loading {
            text-align: center;
            padding: 50px;
            color: #7f8c8d;
        }

        .loading i {
            font-size: 48px;
            margin-bottom: 20px;
            color: #3498db;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <h1><i class="fas fa-boxes"></i> Inventario</h1>
            <p>Panel de Administración</p>
            <button class="logout-btn" onclick="cerrarSesion()" style="position: absolute; top: 20px; right: -10px; scale: 0.8;">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </button>
        </div>

        <div class="sidebar-section">
            <div class="section-title">SEDE ACTUAL</div>
            <div class="dropdown">
                <select class="dropdown-select" id="sedeSelect">
                    <option value="1">SEDE PRINCIPAL</option>
                    <option value="2">SEDE MINERCOL</option>
                </select>
            </div>
            <div class="sede-info">
                <i class="fas fa-building"></i>
                <span id="sedeNombre">SEDE PRINCIPAL</span>
            </div>
        </div>

        <div class="sidebar-section">
            <div class="section-title">DASHBOARD</div>
            <div class="menu-item" data-page="dashboard">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </div>
        </div>

        <div class="sidebar-section">
            <div class="section-title">GESTIÓN DE SALAS</div>
            <div class="menu-item" data-page="todas_las_salas">
                <i class="fas fa-building"></i>
                <span>Todas las Salas</span>
            </div>
        </div>

        <div class="sidebar-section">
            <div class="section-title">DISPOSITIVOS</div>
            <div class="menu-item" data-page="lista_dispositivos">
                <i class="fas fa-desktop"></i>
                <span>Lista Dispositivos</span>
            </div>
            
            <button class="add-device-btn" data-page="agregar_dispositivo">
                <i class="fas fa-plus"></i> Agregar Dispositivo
            </button>
            
            <div class="device-options">
                <div class="device-option" data-page="puntos_datos">
                    <i class="fas fa-plug"></i>
                    <span>Puntos de datos</span>
                </div>
                <div class="device-option" data-page="puntos_regulares">
                    <i class="fas fa-plug"></i>
                    <span>Puntos regulados</span>
                </div>
                <div class="device-option" data-page="puntos_ap">
                    <i class="fas fa-plug"></i>
                    <span>Puntos AP</span>
                </div>
                <div class="device-option" data-page="aires_acondicionados">
                    <i class="fas fa-snowflake"></i>
                    <span>Aires Acondicionados</span>
                </div>
                <div class="device-option" data-page="puntos_comerciales">
                    <i class="fas fa-store"></i>
                    <span>Puntos Comerciales</span>
                </div>
                <div class="device-option" data-page="puntos_switch">
                    <i class="fas fa-network-wired"></i>
                    <span>Puntos Switch</span>
                </div>
            </div>
        </div>
    </div>

    <div class="main-content" id="mainContent">
        <div class="loading">
            <i class="fas fa-home"></i>
            <h2>Bienvenido al Panel de Administración</h2>
            <p>Selecciona una opción del menú lateral para comenzar.</p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Función para cargar contenido
            function loadContent(page, sede_id = null, mensaje = null, tipo = null) {
                $('#mainContent').html('<div class="loading"><i class="fas fa-spinner fa-spin"></i><p>Cargando...</p></div>');
                
                let url = '/inventario%20de%20computos/include/' + page + '.php';
                let params = [];
                
                if (sede_id) {
                    params.push('sede_id=' + sede_id);
                }
                if (mensaje) {
                    params.push('mensaje=' + encodeURIComponent(mensaje));
                }
                if (tipo) {
                    params.push('tipo=' + tipo);
                }
                
                if (params.length > 0) {
                    url += '?' + params.join('&');
                }
                
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#mainContent').html(response);
                    },
                    error: function() {
                        $('#mainContent').html('<div class="loading"><i class="fas fa-exclamation-triangle"></i><p>Error al cargar la página</p></div>');
                    }
                });
            }

            // Event listener para el cambio de sede
            $('#sedeSelect').change(function() {
                const sede_id = $(this).val();
                const sede_nombre = $(this).find('option:selected').text();
                
                // Actualizar el nombre de la sede mostrado
                $('#sedeNombre').text(sede_nombre);
                
                // Obtener la página actual activa
                const activeElement = $('.menu-item.active, .device-option.active, .add-device-btn.active');
                const currentPage = activeElement.data('page');
                
                // Recargar la página actual con la nueva sede
                if (currentPage) {
                    loadContent(currentPage, sede_id);
                } else {
                    // Si no hay página activa, cargar dashboard
                    loadContent('dashboard', sede_id);
                }
            });

            // Event listeners para todos los elementos del menú
            $('.menu-item, .device-option, .add-device-btn').click(function() {
                // Remover clase active de todos los elementos
                $('.menu-item, .device-option, .add-device-btn').removeClass('active');
                // Agregar clase active al elemento clickeado
                $(this).addClass('active');
                
                // Obtener la página a cargar
                const page = $(this).data('page');
                if (page) {
                    const sede_id = $('#sedeSelect').val();
                    loadContent(page, sede_id);
                }
            });

            // Verificar si hay parámetros en la URL
            const urlParams = new URLSearchParams(window.location.search);
            const pageParam = urlParams.get('page');
            const mensaje = urlParams.get('mensaje');
            const tipo = urlParams.get('tipo');
            
            // Verificar mensajes de sesión
            <?php
            if (isset($_SESSION['success_message'])) {
                echo "alert('" . addslashes($_SESSION['success_message']) . "');";
                unset($_SESSION['success_message']);
            }
            if (isset($_SESSION['error_message'])) {
                echo "alert('Error: " . addslashes($_SESSION['error_message']) . "');";
                unset($_SESSION['error_message']);
            }
            ?>
            
            // Mostrar mensaje si existe
            if (mensaje) {
                alert(mensaje);
            }
            
            // Cargar página específica o dashboard por defecto
            if (pageParam) {
                // Usar sede_id de la URL si existe, sino usar el valor del select
                const sedeIdFromUrl = urlParams.get('sede_id');
                const sede_id = sedeIdFromUrl || $('#sedeSelect').val();
                
                // Actualizar el select si viene de la URL
                if (sedeIdFromUrl) {
                    $('#sedeSelect').val(sedeIdFromUrl);
                    $('#sedeNombre').text($('#sedeSelect option:selected').text());
                }
                
                loadContent(pageParam, sede_id, mensaje, tipo);
                
                // Marcar como activo el elemento correspondiente
                $('.menu-item, .device-option, .add-device-btn').removeClass('active');
                $(`[data-page="${pageParam}"]`).addClass('active');
            } else {
                // Usar la sede seleccionada en el dropdown, no siempre sede_id = 1
                const sede_id = $('#sedeSelect').val();
                loadContent('dashboard', sede_id);
            }
        });

        // Función para cerrar sesión
        function cerrarSesion() {
            if (confirm('¿Estás seguro de que quieres cerrar sesión?')) {
                // Aquí puedes hacer una llamada AJAX para cerrar la sesión en el servidor
                // Por ahora, redirigimos a la página de login
                window.location.href = '../login/logout.php';
            }
        }
    </script>
</body>
</html>
