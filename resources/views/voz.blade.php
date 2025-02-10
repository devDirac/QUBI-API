<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ URL::to('/') }}/css/bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ URL::to('/') }}/css/style.css" rel="stylesheet" type="text/css" />
    <title>DIRAC | Ingenieros Consultores</title>
    <link rel="icon" type="image/x-icon" href="{{ URL::to('/') }}/img/arjion1.png?v=1">
    <script src="{{ URL::to('/') }}/js/jquery.min.js"></script>
    <script src="{{ URL::to('/') }}/js/bootstrap5.js"></script>
    <script src="{{ URL::to('/') }}/js/index.js?v=sssssss"></script>
</head>
<body>
    <nav class="navbar bg-body-tertiary content-nav">
        <div class="container">
            <p class="navbar-brand">
                <img src="{{ URL::to('/') }}/img/arjion1.png?v=1" alt="Bootstrap" width="55" height="55">
            </p>
            <a class="navbar-brand" href="{{ URL::to('/api/metricas?user=') }}{{request()->user;}}">
                Consultas
            </a>
        </div>
    </nav>
    <br />
    <div class="container">
        <div class="row">
            <div class="col-md-12" style="text-align: center;">
                <h1>Reporte de voz</h1>
            </div>
            <div class="col-md-12" id="controls" style="text-align: center;">
                <label for="micSelect">
                    Micrófonos disponibles
                    <select name="micSelect" id="micSelect" class="form-control"></select>
                </label>
                <br>
                <br>
                <div class="btn-group" id="content-controls">
                    <button type="button" class="btn btn-danger" id="btn-aceptar">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-record-circle-fill" viewBox="0 0 16 16">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-8 3a3 3 0 1 0 0-6 3 3 0 0 0 0 6" />
                        </svg>
                        Iniciar grabación
                    </button>
                    <button style="display: none;" type="button" class="btn btn-warning" id="btn-detener">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-stop-btn-fill" viewBox="0 0 16 16">
                            <path
                                d="M0 12V4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2m6.5-7A1.5 1.5 0 0 0 5 6.5v3A1.5 1.5 0 0 0 6.5 11h3A1.5 1.5 0 0 0 11 9.5v-3A1.5 1.5 0 0 0 9.5 5z" />
                        </svg>
                        Detener grabación
                    </button>
                </div>
                <br>
                <br>
                <div class="row">
                    <div class="col-md-12" style="display: none; text-align: center;" id="procesando">
                        <h5>Procesando solicitud, por favor espere...</h5>
                    </div>
                    <div class="col-md-12" style="display: none; text-align: center;" id="feetBack">
                        <br>
                        <div id="counter_content">
                            <h5 id="counter"></h5>
                        </div>
                        <div id="msg">
                            <h5>Grabando</h5>
                        </div>
                        <canvas width="500" height="120"></canvas>
                    </div>
                </div>
                <div class="row" id="results" style="padding: 14px; text-align: center; justify-content: center;">
                </div>
            </div>
            
            <div class="col-md-12" id="error" style="display: none;">
                <div class="alert alert-warning" role="alert" id="alertaEstatusNotificacion">
                    El usuario no es valido y es requerido
                </div>
            </div>
            <div class="col-md-12" id="error2" style="display: none;">
                <div class="alert alert-warning" role="alert" id="alertaEstatusNotificacion2">
                    Proporcione permisos de acceso al micrófono y audio despues refresque su navegador y vuelva a
                    intentarlo
                </div>
            </div>
        </div>
    </div>
</body>
</html>