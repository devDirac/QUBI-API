<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel</title>
    <link rel="stylesheet" href="https://getbootstrap.com/2.3.2/assets/css/bootstrap.css">
    <style type="text/css">
        html,
        body {
            height: 100%;
        }

        #wrap {
            min-height: 100%;
            height: auto !important;
            height: 100%;
            margin: 0 auto -60px;
        }

        #push,
        #footer {
            height: 60px;
        }

        #footer {
            background-color: #f5f5f5;
        }

        .container {
            width: auto;
            max-width: 680px;
        }

        .container .credit {
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <div style="text-align:center">
        <img src="{{ URL::to('/') }}/img/logo_correo.png" alt="" width="200">
    </div>
    <br>
    <h3 style='color:#F89E44;'>{!! $titulo !!}</h3>
    <br>
    <p style='color:#38425d;'>{!! $html !!}</p>
    <br>
    <br>

    <div class="col-md-12">
        <table style=" width: 100%; border: 1px solid #000;" id="tabla">
            <thead>
                <tr>
                    <th style="text-align:center; background: #eee; text-align: left; vertical-align: top; border: 1px solid #000; border-collapse: collapse; padding: 0.3em; caption-side: bottom;"
                        scope="col">Contrato</th>
                    <th style="text-align:center; background: #eee; text-align: left; vertical-align: top; border: 1px solid #000; border-collapse: collapse; padding: 0.3em; caption-side: bottom;"
                        scope="col">importe</th>
                    <th style="text-align:center; background: #eee; text-align: left; vertical-align: top; border: 1px solid #000; border-collapse: collapse; padding: 0.3em; caption-side: bottom;"
                        scope="col">Avance capturado</th>
                    <th style="text-align:center; background: #eee;  text-align: left; vertical-align: top; border: 1px solid #000; border-collapse: collapse; padding: 0.3em; caption-side: bottom;"
                        scope="col">Porcentaje de avance</th>
                </tr>
            </thead>
            <tbody id="content_table">
                <?php
                    foreach ($contratosAvances as $key => $value) {
                        echo "<tr>";
                        echo "<td style=' text-align: left; vertical-align: top; border: 1px solid #000; border-collapse: collapse; padding: 0.3em; caption-side: bottom;'> {$value->contrato}</td>";
                        echo "<td style=' text-align: left; vertical-align: top; border: 1px solid #000; border-collapse: collapse; padding: 0.3em; caption-side: bottom;'> {$value->importe}</td>";
                        echo "<td style=' text-align: left; vertical-align: top; border: 1px solid #000; border-collapse: collapse; padding: 0.3em; caption-side: bottom;'> {$value->avance}</td>";
                        echo "<td style=' text-align: left; vertical-align: top; border: 1px solid #000; border-collapse: collapse; padding: 0.3em; caption-side: bottom;'> {$value->porcentaje_de_avance}</td>";
                        echo "</tr>";
                    }
                ?>
            </tbody>
        </table>
        <br>
    </div>
    <div>
        <p style='color:#38425d; font-weight: bold;'> ¡Excelente día! </p>
    </div>
    <div style="height: 250px; background-color: #3E3E3E; margin-top: auto;">
        <div
            style="position: relative; top: 25px; text-align: center; width: 100%; color: white; background-color: #3E3E3E;">
            ® DIRAC | Ingenieros Consultores<br />
        </div>
    </div>
    <!-- importar component vue -->
    <script src="{{ asset('js/app.js') }}"></script>
</body>

</html>