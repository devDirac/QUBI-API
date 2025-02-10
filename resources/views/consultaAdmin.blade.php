<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ URL::to('/') }}/css/bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ URL::to('/') }}/css/style.css" rel="stylesheet" type="text/css" />
    <link href="{{ URL::to('/') }}/css/dataTables.css" rel="stylesheet" type="text/css" />
    <title>DIRAC | Ingenieros Consultores</title>
    <link rel="icon" type="image/x-icon" href="{{ URL::to('/') }}/img/arjion1.png?v=1">
    <script src="{{ URL::to('/') }}/js/jquery.min.js"></script>
    <script src="{{ URL::to('/') }}/js/bootstrap5.js"></script>
    <script src="{{ URL::to('/') }}/js/dataTables.js?v=0.1"></script>
</head>

<body>
    <nav class="navbar bg-body-tertiary content-nav">
        <div class="container">
            <p class="navbar-brand">
                <img src="{{ URL::to('/') }}/img/arjion1.png?v=1" alt="Bootstrap" width="55" height="55">
            </p>
            <p class="navbar-brand">Bienvenido a la consulta global del sistema de reporte de voz e imagen</p>
        </div>
    </nav>
    <br />
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 text-center">
                <br>
                <h5>Consulta global </h5>
                <br>
            </div>
            <div class="col-md-6 col-sm-12 " style="text-align: left;">
                <label for="fechaInicio" class="form-label">Fecha inicio</label>
                <input type="date" id="fechaInicio" class="form-control">
            </div>
            <div class="col-md-6 col-sm-12" style="text-align: left;">
                <label for="fechaFin" class="form-label">Fecha termino</label>
                <input type="date" id="fechaFin" class="form-control">
            </div>
            <div class="col-md-12 text-center">
                <br>
                <button type="button" id="btn-buscar" class="btn btn-primary">Buscar</button>
                <br>
            </div>
            <div class="col-md-12">
                <table class="table" id="tabla">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Tipo</th>
                            <th scope="col">Texto original</th>
                            <th scope="col">Resumen</th>
                            <th scope="col">Ruta</th>
                            <th scope="col">Fecha inicio resumen generado</th>
                            <th scope="col">Fecha fin resumen generado</th>
                            <th scope="col">Imagen</th>
                            <th scope="col">Fecha registro</th>
                            <th scope="col">Usuario</th>
                        </tr>
                    </thead>
                    <tbody id="content_table">
                    </tbody>
                </table>
                <br>
            </div>
        </div>
    </div>
    <div class="modal" tabindex="-1" id="modalLoader">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background-color: transparent; border: none;">
                <div class="modal-body" style="text-align: center;">
                    <div class="spinner-grow spinner-grow-sm" style="width: 3rem; height: 3rem;" role="status">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ URL::to('/') }}/js/general.js?v=0.2a"></script>
</body>

</html>