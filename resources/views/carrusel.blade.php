<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrusel</title>
    <style>
        @keyframes marquee {
            0% {
                transform: translateX(100%);
            }

            100% {
                transform: translateX(-100%);
            }
        }

        #marquee span {
            color: #333;
            font-weight: bold;
        }
    </style>
</head>

<body style="background-color: rgb(32, 47, 80);">
    <div style="position: relative; height: 95vh; background-color: rgb(32, 47, 80); width:95vw; text-align: center;">
        <h1>Selecciona la lista de reproducción</h1>
        <div id="carousel"
            style="align-items: center; justify-content: center; height: 80vh; width: 100%;">
            @foreach ($elements as $index => $element)
                <div style="background-color: rgb(32, 47, 80); width:100%; margin-top:15px">
                    <span
                        style="margin-right: 50px; font-size: 24px; background-color: #f4f4f4; padding: 5px; font-weight: bold; border-radius:15px;">
                        lista:{{$element->piso}}
                        <a href="{{ URL::to('/carrusel?id=') . $element->id }}"> Reproducir</a>
                    </span>
                </div>
            @endforeach
        </div>
        <!-- Footer con logo -->
        <footer style="display: flex; align-items: center; justify-content: right; height: 10vh; width: 100%;">
            <img src="{{ URL::to('/') }}/img/logo_dirac_2025.png?v=1" alt="Logo" style="width: 300px; height: auto;">
        </footer>

    </div>
</body>

</html>