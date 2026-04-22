<!DOCTYPE html>
<html lang="es-mx">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Seguimiento programas</title>

    <!-- Framework oficial gob.mx -->
    <link href="https://framework-gb.cdn.gob.mx/assets/styles/main.css" rel="stylesheet">

    <!-- Estilos de Llave MX -->
    <link href="{{ asset('css/styleA.css') }}" rel="stylesheet">
</head>

<body class="login-page">

<main class="page">

    <div class="login-wrapper">

        <!-- COLUMNA IZQUIERDA -->
        <div class="login-left">
            <p class="text-center">
                <img src="{{ asset('images/seguimientos.png') }}"
                     alt="Seguimientos"
                     width="430"
                     class="mb-4"/><br>

                <img src="{{ asset('images/logos/mujeres_logo.png') }}"
                     alt="Logo Mujeres"
                     width="500">
            </p>
        </div>

        <!-- COLUMNA DERECHA -->
        <div class="login-right">
            <div class="container-llaveMX">
                <div class="boxLogin">
                    <div   div class="right-section">
                        <img src="{{ asset('images/logos/llaveMX.png') }}" alt="Llave MX">
                        <div class="login-buttons-row">
                        <a href="{{ route('llave.login') }}" class="login-button">
                            Iniciar sesión
                        </a>
                        
                        <a href="https://val-llave.infotec.mx/RegistroCiudadano.xhtml"
                           class="login-button cuenta">
                            Crear cuenta
                        </a>  
                        </div>
                        <div class="acciones">
                            <p class="terminos">
                                Al iniciar sesión declaro que he leído los
                                <a href="https://www.archivos.atdt.gob.mx/storage/app/media/Transparencia/TyC/TerminosLlaveMX.pdf" target="_blank">
                                    Términos y Condiciones
                                </a>
                                y nuestro
                                <a href="https://www.archivos.atdt.gob.mx/storage/app/media/Transparencia/PORTAL%20ATDT/AVISOS%20DE%20PRIVACIDAD/ATDT_Aviso%20de%20Privacidad%20Integral%20Llave%20MX.pdf" target="_blank">
                                    Aviso de Privacidad
                                </a>.
                            </p>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
</main>


<script src="https://framework-gb.cdn.gob.mx/gobmx.js"></script>
</body>
</html>