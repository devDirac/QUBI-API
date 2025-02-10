<!DOCTYPE html>
<html lang="en">
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
    <h3 style='color:#F89E44;'>{!! $titulo !!}<strong style='color:#38425d;'>{!! $titulo2 !!}</strong></h3>
    <br>
    <p style='color:#38425d;'>{!! $html !!}</p>
    <br>
    <br>
    <div style="display: flex; ">
        <table style="width: 100%; border: 1px solid #eee; padding:5px" >
            <thead style='color:#38425d; font-size:14px; font-weight:100;'>
                <tr style="border: 1px solid #000; border-collapse: collapse; padding: 0.3em; caption-side: bottom; text-align:left;">
                    <th style="" scope="col">
                        <p style='color:#38425d; font-size:14px; font-weight:100;  text-align:left;'>
                            ESTIMACIÓN DEFINITIVA NUMERO:
                            {!! $numEstimacionDefinitiva !!}
                        </p>
                    </th>
                    <th style="" scope="col">
                        <p style='color:#38425d; font-size:14px; font-weight:100; text-align:left;'>
                            FECHA DE ESTIMACIÓN
                            DEFINITIVA: {!! $fechaEstDefinitiva !!} </p>
                    </th>
                    <th style="" scope="col">
                        <p style='color:#38425d; font-size:14px; font-weight:100; text-align:left;'>
                            CLIENTE: {!! $cliente !!}</p>
                    </th>
                </tr>
                <tr style="border: 1px solid #000; border-collapse: collapse; padding: 0.3em; caption-side: bottom;  text-align:left;">
                    <th style="" scope="col">
                        <p style='color:#38425d;  font-size:14px; font-weight:100; text-align:left;'>
                            CONTRATISTA:
                            {!! $contratista !!}
                        </p>
                    </th>
                    <th style="" scope="col">
                        <p style='color:#38425d; font-size:14px; font-weight:100; text-align:left;'>
                            ORIGEN DE LOS
                            RECURSOS:{!! $origenRecursos !!}</p>
                    </th>
                    <th style="" scope="col">
                        <p style='color:#38425d;  font-size:14px; font-weight:100; text-align:left;'>
                            PERIODO: {!! $periodo !!}</p>
                    </th>
                </tr>
                <tr style="border: 1px solid #000; border-collapse: collapse; padding: 0.3em; caption-side: bottom; text-align:left;">
                    <th style="" scope="col">
                        <p style='color:#38425d;  font-size:14px; font-weight:100; text-align:left;'>
                            CONTRATO NUMERO:
                            {!! $contratoNumero !!}
                        </p>
                    </th>
                    <th style="" scope="col">
                        <p style='color:#38425d; font-size:14px; font-weight:100; text-align:left;'>
                            DESCRIPCIÓN DEL CONTRATO:
                            {!! $descripcionContrato !!}
                        </p>
                    </th>
                </tr>
            </thead>
        </table>
    </div>
    <div class="divider">&nbsp;</div>
    <br>
    <br>
    <div style="text-align:center; width: 100%;">
        <table style=" width: 100%; border: 1px solid #eee; padding:5px" >
            <thead style='color:#38425d; font-size:14px; font-weight:100;'>
                <tr>
                    <th>
                        <div>
                            <p style='color:#38425d; font-size:14px;  font-weight:100;  text-align:left;'>
                                IMPORTE
                                CONTRATADO:<strong>{!! $importeContratado !!}</strong>
                            </p>
                            <p style='color:#38425d; font-size:14px; font-weight:100; text-align:left;'>
                                ACUMULADO
                                ANTERIOR:<strong>{!! $acumuladoAnterior !!}</strong>
                            </p>
                            <p style='color:#38425d; font-size:14px; font-weight:100; text-align:left;'>
                                ESTA
                                ESTIMACIÓN:<strong>{!! $estaEstimacion !!}</strong></p>
                            <p style='color:#38425d; font-size:14px; font-weight:100; text-align:left;'>
                                ACUMULADO
                                ACTUAL:<strong>{!! $acumuladoActual !!}</strong></p>
                            <p style='color:#38425d; font-size:14px; font-weight:100; text-align:left;'>
                                SALDO:<strong>{!! $saldo !!}</strong>
                            </p>
                            <p style='color:#38425d; font-size:14px; font-weight:100; text-align:left;'>
                                DEDUCCION ESTA
                                ESTIMACIÓN:<strong>{!! $deduccionEstimacion !!}</strong></p>
                            <p style='color:#38425d; font-size:14px; font-weight:100; text-align:left;'>
                                AMORTIZACIÓN <small>[Esta estimación]</small>:<strong>{!! $amortizacion !!}</strong></p>
                            <p style='color:#38425d; font-size:14px; font-weight:100; text-align:left;'>
                                SUBTOTAL:<strong>{!! $subtotal !!}</strong></p>
                            <p style='color:#38425d; font-size:14px; font-weight:100; text-align:left;'>
                                IMP 16% IVA:<strong>{!! $impuestoIva !!}</strong></p>
                            <p style='color:#38425d; font-size:14px; font-weight:100; text-align:left;'>
                                IMP TOTAL:<strong>{!! $subTotal2 !!}</strong></p>
                            <p style='color:#38425d; font-size:14px; font-weight:100; text-align:left;'>
                                DEVOLUCION POR RETENCIÓN:<strong>{!! $devolucionRetencion !!}</strong></p>
                            <p style='color:#38425d; font-size:14px; font-weight:100; text-align:left;'>
                                RETENCIÓN:<strong>{!! $retencion !!}</strong></p>
                            <p style='color:#38425d; font-size:14px; font-weight:100; text-align:left;'>
                                CARGOS ADICIONALES:<strong>{!! $cargosAdicionales !!}</strong>
                            </p>
                            <p style='color:#38425d; font-size:14px; font-weight:100; text-align:left;'>
                                SALDO A PAGAR DE ESTA
                                ESTIMACIÓN:<strong>{!! $saldoPagarEstimacionActual !!}</strong></p>
                            <p style='color:#38425d; font-size:14px; font-weight:100; text-align:left;'>
                                IMPORTE A
                                PAGAR:<strong>{!! $importePagar !!}</strong></p>
                            <p style='color:#38425d; font-size:14px; font-weight:100;'>
                            </p>
                        </div>
                    </th>
                    <th>
                        <div>
                            <p style='color:#38425d; font-size:14px; font-weight:100;  text-align:left; text-decoration: underline;'>
                                AMORTIZACIÓN [10.00 %]</p>
                            <p style='color:#38425d; font-size:14px; font-weight:100; text-align:left;'>
                                ANTICIPO:<strong>{!! $anticipo !!}</strong></p>
                            <p style='color:#38425d; font-size:14px; font-weight:100; text-align:left;'>
                                AMORTIZACION [ACUMULADO
                                ANTERIOR]:<strong>{!! $amortizacionAcumuladoAnterior !!}</strong></p>
                            <p style='color:#38425d; font-size:14px; font-weight:100; text-align:left;'>
                                ESTA ESTIMACIÓN[0.00
                                %]:<strong>{!! $amortizacionActual !!}</strong></p>
                            <p style='color:#38425d; font-size:14px; font-weight:100; text-align:left;'>
                                ANTICIPO [ACUMULADO
                                ACTUAL]:<strong>{!! $anticipoAcumulado !!}</strong></p>
                            <p style='color:#38425d; font-size:14px; font-weight:100; text-align:left;'>
                                SALDO POR
                                AMORTIZAR:<strong>{!! $saldoPorAmortizar !!}</strong>
                            </p>
                            <p
                                style='color:#38425d; font-size:14px; font-weight:100; text-decoration: underline; text-align:left;'>
                                RETENCIÓN</p>
                            <p style='color:#38425d; font-size:14px; font-weight:100; text-align:left;'>
                                FONDO DE GARANTÍA
                                [{!! $fondoGarantiaPorcentaje !!}%]:<strong>{{$fondoGarantia}}</strong></p>
                            <p style='color:#38425d; font-size:14px; font-weight:100; text-align:left;'>
                                F.G. [ACUMULADO
                                ANTERIOR]:<strong>{!! $fondoGarantiaAnterior !!}</strong></p>
                            <p style='color:#38425d; font-size:14px; font-weight:100; text-align:left;'>
                                F.G. [ACUMULADO
                                ACTUAL]:<strong>{!! $fondoGarantiaAcumulado !!}</strong></p>
                            <p style='color:#38425d; font-size:14px; font-weight:100; text-align:left;'>
                                IVA
                                RETENCIÓN:<strong>{!! $ivaRetencion !!}</strong></p>
                            <p
                                style='color:#38425d; font-size:14px; font-weight:100; text-align:left; text-decoration: underline;'>
                                CARGOS
                                ADICIONALES</strong>
                            </p>
                            <p style='color:#38425d; font-size:14px; font-weight:100; text-align:left;'>
                                IVA CARGOS
                                ADICIONALES:<strong>{!! $ivaCargosAdicionales !!}</strong></p>
                        </div>
                    </th>
                </tr>
            </thead>
        </table>
    </div>
    <p style='color:#38425d; font-size:14px; font-weight:unset; text-align:left;'>
        <strong>({!! $cifraTexto !!} {!! $centavos !!})</strong>
    </p>
    <br>
    <div class="text-center">
        <h3 style='color:#38425d; font-size:14px; font-weight:unset;'>Conceptos
            estimados</h3>
    </div>
    <div class="col-md-12">
        <table style=" width: 100%; border: 1px solid #000;" id="tabla">
            <thead>
                <tr>
                    <th style="text-align:center; background: #eee; text-align: left; vertical-align: top; border: 1px solid #000; border-collapse: collapse; padding: 0.3em; caption-side: bottom;"
                        scope="col">Id concepto</th>
                    <th style="text-align:center; background: #eee; text-align: left; vertical-align: top; border: 1px solid #000; border-collapse: collapse; padding: 0.3em; caption-side: bottom;"
                        scope="col">Descripción</th>
                    <th style="text-align:center; background: #eee; text-align: left; vertical-align: top; border: 1px solid #000; border-collapse: collapse; padding: 0.3em; caption-side: bottom;"
                        scope="col">Volumen estimado</th>
                    <th style="text-align:center; background: #eee;  text-align: left; vertical-align: top; border: 1px solid #000; border-collapse: collapse; padding: 0.3em; caption-side: bottom;"
                        scope="col">Volumen acumulado anterior</th>
                    <th style="text-align:center; background: #eee; text-align: left; vertical-align: top; border: 1px solid #000; border-collapse: collapse; padding: 0.3em; caption-side: bottom;"
                        scope="col">Volumen acumulado actual</th>
                    <th style="text-align:center; background: #eee;  text-align: left; vertical-align: top; border: 1px solid #000; border-collapse: collapse; padding: 0.3em; caption-side: bottom;"
                        scope="col">Precio</th>
                    <th style="text-align:center; background: #eee; text-align: left; vertical-align: top; border: 1px solid #000; border-collapse: collapse; padding: 0.3em; caption-side: bottom;"
                        scope="col">Importe</th>
                    <th style="text-align:center; background: #eee; text-align: left; vertical-align: top; border: 1px solid #000; border-collapse: collapse; padding: 0.3em; caption-side: bottom;"
                        scope="col">Importe acumulado anterior</th>
                    <th style="text-align:center; background: #eee; text-align: left; vertical-align: top; border: 1px solid #000; border-collapse: collapse; padding: 0.3em; caption-side: bottom;"
                        scope="col">Importe acumulado actual</th>
                    <th style="text-align:center; background: #eee;  text-align: left; vertical-align: top; border: 1px solid #000; border-collapse: collapse; padding: 0.3em; caption-side: bottom;"
                        scope="col">Comentarios</th>
                </tr>
            </thead>
            <tbody id="content_table">
                <?php
foreach ($conceptos as $key => $value) {
    echo "<tr>";
    echo "<td style=' text-align: left; vertical-align: top; border: 1px solid #000; border-collapse: collapse; padding: 0.3em; caption-side: bottom;'> {$value['concepto']} </td>";
    echo "<td style=' text-align: left; vertical-align: top; border: 1px solid #000; border-collapse: collapse; padding: 0.3em; caption-side: bottom;'> {$value['descripcion']} </td>";
    echo "<td style=' text-align: left; vertical-align: top; border: 1px solid #000; border-collapse: collapse; padding: 0.3em; caption-side: bottom;'> {$value['volumen_estimar']} {$value['unidad']} </td>";
    echo "<td style=' text-align: left; vertical-align: top; border: 1px solid #000; border-collapse: collapse; padding: 0.3em; caption-side: bottom;'>{$value['volumenAcumuladoAnterior']} </td>";
    echo "<td style=' text-align: left; vertical-align: top; border: 1px solid #000; border-collapse: collapse; padding: 0.3em; caption-side: bottom;'>{$value['volumenAcumuladoActual']} </td>";
    echo "<td style=' text-align: left; vertical-align: top; border: 1px solid #000; border-collapse: collapse; padding: 0.3em; caption-side: bottom;'> $ {$value['precio_unitario']} </td>";
    echo "<td style=' text-align: left; vertical-align: top; border: 1px solid #000; border-collapse: collapse; padding: 0.3em; caption-side: bottom;'> $ {$value['importe']} </td>";
    echo "<td style=' text-align: left; vertical-align: top; border: 1px solid #000; border-collapse: collapse; padding: 0.3em; caption-side: bottom;'>$ {$value['importeAcumuladoAnterior']}</td>";
    echo "<td style=' text-align: left; vertical-align: top; border: 1px solid #000; border-collapse: collapse; padding: 0.3em; caption-side: bottom;'>$ {$value['importeAcumuladoActual']}</td>";
    echo "<td style=' text-align: left; vertical-align: top; border: 1px solid #000; border-collapse: collapse; padding: 0.3em; caption-side: bottom;'> {$value['comentarios_estimacion']} </td>";
    echo "</tr>";
}
                ?>
            </tbody>
        </table>
        <br>
    </div>
    <br>
    <h5 style='color:#38425d;'>{!! $html2 !!}</h5>
    <br>
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
