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
    <div style="position: relative; height: 95vh; background-color: rgb(32, 47, 80); width:100%;">
        <div id="carousel"
            style="display: flex; align-items: center; justify-content: center; height: 80vh; width: 100%;">
            @foreach ($elements as $index => $element)
                @if ($element->texto === null)
                    <div class="carousel-item" data-type="{{ $element->type }}"
                        data-duration="{{ $element->type === 'image' ? 30000 : 0 }}"
                        style="display: {{ $index === 0 ? 'block' : 'none' }}; width: 100%; height: 100%; position: absolute; top: 0; left: 0; text-align: center;">

                        @if ($element->type === 'image')
                            <img src="{{ $apiUrlDocumentos . str_replace('storage/app/', '', $element->ruta_media) }}" alt="Image {{ $index }}"
                                style="width: 100%; height: 100%; object-fit: contain;">
                        @elseif ($element->type === 'video')
                            <video src="{{ $apiUrlDocumentos . str_replace('storage/app/', '', $element->ruta_media) }}" muted
                                class="carousel-video" style="width: 100%; height: 100%; object-fit: contain;">
                            </video>
                        @endif
                    </div>
                @endif
            @endforeach

        </div>

        <div style="background-color: rgb(32, 47, 80); overflow: hidden;  height: 5vh; width: 100%; margin-top:5px;">
            <div id="marquee"
                style="white-space: nowrap; display: inline-block; animation: marquee 25s linear infinite; padding:5px">
                @foreach ($banner as $index => $element)
                    @if ($element->ruta_media === null)
                        <span
                            style="margin-right: 50px; font-size: 24px; background-color: #f4f4f4; padding: 5px; font-weight: bold; border-radius:15px;">
                            {{$element->texto}} </span>

                    @endif
                @endforeach
            </div>
        </div>
        <!-- Footer con logo -->
        <footer style="display: flex; align-items: center; justify-content: right; height: 10vh; width: 100%;">
            <img src="{{ URL::to('/') }}/img/logo_dirac_2025.png?v=1" alt="Logo" style="width: 300px; height: auto;">
        </footer>
    </div>


    <script>
        
        window.onload  = function () {
            const items = document.querySelectorAll('.carousel-item');
            let currentIndex = 0;
            const goToNext = function () {
                items[currentIndex].style.display = 'none'; // Oculta el elemento actual
                currentIndex = (currentIndex + 1) % items.length; // Incrementa el índice
                const nextItem = items[currentIndex];
                nextItem.style.display = 'block'; // Muestra el siguiente elemento
                if (nextItem.dataset.type === 'image') {
                    // Cambia después de 30 segundos si es una imagen
                    setTimeout(goToNext, nextItem.dataset.duration);
                } else if (nextItem.dataset.type === 'video') {
                    // Cambia al final del video si es un video
                    const video = nextItem.querySelector('.carousel-video');
                    video.play();
                    video.onended = goToNext;
                }
            };
            // Inicia el carrusel
            const firstItem = items[currentIndex];
            if (firstItem.dataset.type === 'image') {
                setTimeout(goToNext, firstItem.dataset.duration);
            } else if (firstItem.dataset.type === 'video') {
                const video = firstItem.querySelector('.carousel-video');
                video.play();
                video.onended = goToNext;
            }
        };
    </script>
    <style>
        #carousel {
            position: relative;
            overflow: hidden;
        }

        .carousel-item {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: none;
        }
    </style>
</body>

</html>