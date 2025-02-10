const urlApp_ = 'https://diracapm.qubi.com.mx/';
//const urlApp_ = 'http://localhost/APM/public/';
let maxNumAudios = 0;
let finalsAudioFile = [];
const getParameterValues = (param) => {
    var url = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for (var i = 0; i < url.length; i++) {
        var urlparam = url[i].split('=');
        if (urlparam[0] == param) {
            return urlparam[1];
        }
    }
}
/* Obtiene y asigna el id de la solicitud en idSolicitud */
const user = getParameterValues('user');
const proyecto = getParameterValues('p');
const contrato = getParameterValues('idc');
const concepto = getParameterValues('c');
const avance = getParameterValues('a');

$(document).ready(async () => {
    let myInterval = null;
    let mytimeoutID = null;
    let leftchannel = [];
    let rightchannel = [];
    let recording = false;
    let recordingLength = 0;
    let audioInput = null;
    let sampleRate = null;
    let AudioContext = window.AudioContext || window.webkitAudioContext;
    let context = null;
    let analyser = null;
    //let canvas = document.querySelector('canvas');
    var canvas = document.getElementById('canvas');

    let canvasCtx = canvas.getContext("2d");
    let micSelect = document.querySelector('#micSelect');
    let stream = null;
    let tested = false;
    let finalFile = null;
    //let finalsAudioFile = [];
    /* Obtiene los parametros del url y los divide en segmentos */
    const getParameterValues = (param) => {
        var url = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
        for (var i = 0; i < url.length; i++) {
            var urlparam = url[i].split('=');
            if (urlparam[0] == param) {
                return urlparam[1];
            }
        }
    }
    /* Obtiene y asigna el id de la solicitud en idSolicitud */
    const user = getParameterValues('user');
    if (!user) {
        $('#procesando').hide();
        $('#controls').hide();
        $('#feetBack').hide();
        $('#btn-detener').hide();
        $('#btn-enviar').hide();
        $('#audio').hide();
        $('#error').show();
        return false;
    }
    try {
        window.stream = stream = await getStream();
        $('#error2').hide();
        $('#controls').show();
    } catch (err) {
        $('#error2').show();
        $('#controls').hide();
    }
    const deviceInfos = await navigator.mediaDevices.enumerateDevices();
    var mics = [];
    for (let i = 0; i !== deviceInfos.length; ++i) {
        let deviceInfo = deviceInfos[i];
        if (deviceInfo.kind === 'audioinput') {
            mics.push(deviceInfo);
            let label = deviceInfo.label ||
                'Microphone ' + mics.length;
            const option = document.createElement('option')
            option.value = deviceInfo.deviceId;
            option.text = label;
            micSelect.appendChild(option);
        }
    }
    function getStream(constraints) {
        if (!constraints) {
            constraints = { audio: true, video: false };
        }
        return navigator.mediaDevices.getUserMedia(constraints);
    }
    setUpRecording();
    function setUpRecording() {
        context = new AudioContext();
        sampleRate = context.sampleRate;
        // crea un nodo de ganancia
        volume = context.createGain();
        // crea un nodo de audio desde el flujo entrante del micrófono
        audioInput = context.createMediaStreamSource(stream);
        // Crear analizador
        analyser = context.createAnalyser();
        // conectar la entrada de audio al analizador
        audioInput.connect(analyser);
        // conectar analizador al control de volumen
        analyser.connect(volume);
        let bufferSize = 2048;
        let recorder = context.createScriptProcessor(bufferSize, 2, 2);
        // conectamos el control de volumen al procesador
        volume.connect(recorder);
        analyser.connect(recorder);
        // finally connect the processor to the output
        recorder.connect(context.destination);
        recorder.onaudioprocess = function (e) {
            if (!recording) {
                return
            };
            let left = e.inputBuffer.getChannelData(0);
            let right = e.inputBuffer.getChannelData(1);
            if (!tested) {
                tested = true;
                // si esto se reduce a 0 no estamos recibiendo ningún sonido
                if (!left.reduce((a, b) => a + b)) {
                    alert("Al parecer hay un error con su micrófono");
                    stop();
                    stream.getTracks().forEach(function (track) {
                        track.stop();
                    });
                    context.close();
                }
            }
            // clonamos las muestras
            leftchannel.push(new Float32Array(left));
            rightchannel.push(new Float32Array(right));
            recordingLength += bufferSize;
        };
        visualize();
    };
    function mergeBuffers(channelBuffer, recordingLength) {
        let result = new Float32Array(recordingLength);
        let offset = 0;
        let lng = channelBuffer.length;
        for (let i = 0; i < lng; i++) {
            let buffer = channelBuffer[i];
            result.set(buffer, offset);
            offset += buffer.length;
        }
        return result;
    }
    function interleave(leftChannel, rightChannel) {
        let length = leftChannel.length + rightChannel.length;
        let result = new Float32Array(length);
        let inputIndex = 0;
        for (let index = 0; index < length;) {
            result[index++] = leftChannel[inputIndex];
            result[index++] = rightChannel[inputIndex];
            inputIndex++;
        }
        return result;
    }
    function writeUTFBytes(view, offset, string) {
        let lng = string.length;
        for (let i = 0; i < lng; i++) {
            view.setUint8(offset + i, string.charCodeAt(i));
        }
    }
    function start() {
        recording = true;
        document.querySelector('#msg').style.visibility = 'visible'
        leftchannel.length = rightchannel.length = 0;
        recordingLength = 0;
        if (!context) setUpRecording();
    }
    function stop() {
        recording = false;
        document.querySelector('#msg').style.visibility = 'hidden'
        // bajamos los canales izquierdo y derecho
        let leftBuffer = mergeBuffers(leftchannel, recordingLength);
        let rightBuffer = mergeBuffers(rightchannel, recordingLength);
        // we interleave both channels together
        let interleaved = interleave(leftBuffer, rightBuffer);
        // creamos nuestro archivo wav
        let buffer = new ArrayBuffer(44 + interleaved.length * 2);
        let view = new DataView(buffer);
        // RIFF chunk descriptor
        writeUTFBytes(view, 0, 'RIFF');
        view.setUint32(4, 44 + interleaved.length * 2, true);
        writeUTFBytes(view, 8, 'WAVE');
        // FMT sub-chunk
        writeUTFBytes(view, 12, 'fmt ');
        view.setUint32(16, 16, true);
        view.setUint16(20, 1, true);
        // stereo (2 channels)
        view.setUint16(22, 2, true);
        view.setUint32(24, sampleRate, true);
        view.setUint32(28, sampleRate * 4, true);
        view.setUint16(32, 4, true);
        view.setUint16(34, 16, true);
        // data sub-chunk
        writeUTFBytes(view, 36, 'data');
        view.setUint32(40, interleaved.length * 2, true);
        // escribe las muestras de PCM "La modulación de código de pulso"
        let lng = interleaved.length;
        let index = 44;
        let volume = 1;
        for (let i = 0; i < lng; i++) {
            view.setInt16(index, interleaved[i] * (0x7FFF * volume), true);
            index += 2;
        }
        // GENERACIÓN DEL AUDIO 
        const blob = new Blob([view], { type: 'audio/wav' });
        const audioUrl = URL.createObjectURL(blob);
        finalsAudioFile.push({ id: maxNumAudios, file: blob })
        let audioHTML = '<div class="row" id="content_records' + maxNumAudios + '" style="width: 100%; border: solid 1px grey; padding: 12px;"><div class="col-md-4 col-sm-12" style="text-align:center;"><audio src="' + audioUrl + '"  style="width: 100%;" id="audio' + maxNumAudios + '" controls></audio></div>';
        audioHTML += '<div class="col-md-4 col-sm-12" style="text-align:center;"> <button style=" width: 100%;" type="button" class="btn btn-success" onclick="return enviar(' + maxNumAudios + ')" id="btn-enviar' + maxNumAudios + '""><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-send-fill" viewBox="0 0 16 16"> <path d="M15.964.686a.5.5 0 0 0-.65-.65L.767 5.855H.766l-.452.18a.5.5 0 0 0-.082.887l.41.26.001.002 4.995 3.178 3.178 4.995.002.002.26.41a.5.5 0 0 0 .886-.083zm-1.833 1.89L6.637 10.07l-.215-.338a.5.5 0 0 0-.154-.154l-.338-.215 7.494-7.494 1.178-.471z" /></svg>Enviar grabación</button></div>';
        audioHTML += '<div class="col-md-4 col-sm-12" style="text-align:center;"> <button style=" width: 100%;" type="button" class="btn btn-danger" onclick="return elimina(' + maxNumAudios + ')" id="btn-eliminar' + maxNumAudios + '""><svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="16" fill="currentColor" height="16" viewBox="0 0 128 128"><path d="M 49 1 C 47.34 1 46 2.34 46 4 C 46 5.66 47.34 7 49 7 L 79 7 C 80.66 7 82 5.66 82 4 C 82 2.34 80.66 1 79 1 L 49 1 z M 24 15 C 16.83 15 11 20.83 11 28 C 11 35.17 16.83 41 24 41 L 101 41 L 101 104 C 101 113.37 93.37 121 84 121 L 44 121 C 34.63 121 27 113.37 27 104 L 27 52 C 27 50.34 25.66 49 24 49 C 22.34 49 21 50.34 21 52 L 21 104 C 21 116.68 31.32 127 44 127 L 84 127 C 96.68 127 107 116.68 107 104 L 107 40.640625 C 112.72 39.280625 117 34.14 117 28 C 117 20.83 111.17 15 104 15 L 24 15 z M 24 21 L 104 21 C 107.86 21 111 24.14 111 28 C 111 31.86 107.86 35 104 35 L 24 35 C 20.14 35 17 31.86 17 28 C 17 24.14 20.14 21 24 21 z M 50 55 C 48.34 55 47 56.34 47 58 L 47 104 C 47 105.66 48.34 107 50 107 C 51.66 107 53 105.66 53 104 L 53 58 C 53 56.34 51.66 55 50 55 z M 78 55 C 76.34 55 75 56.34 75 58 L 75 104 C 75 105.66 76.34 107 78 107 C 79.66 107 81 105.66 81 104 L 81 58 C 81 56.34 79.66 55 78 55 z"></path></svg>Descartar</button></div></div>';
        $('#results').append(audioHTML)
        maxNumAudios++;
    }



    function visualize() {
        WIDTH = canvas.width;
        HEIGHT = canvas.height;
        CENTERX = canvas.width / 2;
        CENTERY = canvas.height / 2;
        if (!analyser) return;
        analyser.fftSize = 2048;
        var bufferLength = analyser.fftSize;
        var dataArray = new Uint8Array(bufferLength);
        canvasCtx.clearRect(0, 0, WIDTH, HEIGHT);
        var draw = function () {
            drawVisual = requestAnimationFrame(draw);
            analyser.getByteTimeDomainData(dataArray);
            canvasCtx.fillStyle = 'white';
            canvasCtx.fillRect(0, 0, WIDTH, HEIGHT);
            canvasCtx.lineWidth = 2;
            canvasCtx.strokeStyle = 'rgb(0, 0, 0)';
            canvasCtx.beginPath();
            var sliceWidth = WIDTH * 1.0 / bufferLength;
            var x = 0;
            for (var i = 0; i < bufferLength; i++) {
                var v = dataArray[i] / 128.0;
                var y = v * HEIGHT / 2;
                if (i === 0) {
                    canvasCtx.moveTo(x, y);
                } else {
                    canvasCtx.lineTo(x, y);
                }
                x += sliceWidth;
            }
            canvasCtx.lineTo(canvas.width, canvas.height / 2);
            canvasCtx.stroke();
        };
        draw();
    }

    micSelect.onchange = async e => {
        stream.getTracks().forEach(function (track) {
            track.stop();
        });
        context.close();
        stream = await getStream({
            audio: {
                deviceId: { exact: micSelect.value }
            }, video: false
        });
        setUpRecording();
    }

    document.querySelector('#btn-aceptar').onclick = (e) => {
        let counter = 120;
        if (maxNumAudios >= 9) {
            alert('Ha llegado al maximo de grabaciones, envie las grabaciones en lista o eliminelas para poder agregar mas audios a la lista');
            return;
        }
        document.querySelector("#btn-aceptar").setAttribute("disabled", "disabled");

        myInterval = setInterval(() => {
            $('#counter').html('Restan: ' + counter + ' Segundos');
            counter = counter - 1;
        }, 1000);
        mytimeoutID = setTimeout(() => {
            $('#btn-detener').trigger("click");
        }, 120000);
        $('#feetBack').show();
        $('#btn-detener').show();
        $('#audio').hide();
        $('#btn-enviar').hide();
        start();
    }

    document.querySelector('#btn-detener').onclick = (e) => {
        document.querySelector("#btn-aceptar").removeAttribute("disabled");
        $('#feetBack').hide();
        $('#btn-detener').hide();
        clearInterval(myInterval);
        clearTimeout(mytimeoutID);
        stop();
    }
});


const enviar = (id) => {
    var myModal = new bootstrap.Modal(document.getElementById('modalLoader'), {
        backdrop: "static", keyboard: false
    });
    myModal.toggle();
    $('#procesando').show();
    document.querySelector("#btn-aceptar").setAttribute("disabled", "disabled");
    document.querySelector("#btn-enviar" + id).setAttribute("disabled", "disabled");
    document.querySelector("#btn-eliminar" + id).setAttribute("disabled", "disabled");
    var formData = new FormData();
    formData.append("file", finalsAudioFile.find(e => e?.id === id)?.file);
    formData.append("id_usuario", user);
    formData.append("proyecto", proyecto);
    formData.append("contrato", contrato);
    formData.append("concepto", concepto);
    formData.append("avance", avance);
    $.ajax({
        type: "POST",
        url: urlApp_ + 'api/reportSpeach',
        data: formData,
        enctype: 'multipart/form-data',
        processData: false,
        contentType: false,
        cache: false,
        Headers: {
            "Content-Type": "multipart/form-data"
        },
        success: function (response) {
            myModal.hide();
            $('#procesando').hide();
            document.querySelector("#btn-aceptar").removeAttribute("disabled");
            finalsAudioFile = finalsAudioFile.filter(e => e?.id !== id)
            maxNumAudios--;
            $('#content_records' + id).remove()
            alert('Se ha registrado exitosamente');
        },
        error: function (a, b, c) {
            myModal.hide();
            $('#procesando').hide();
            document.querySelector("#btn-enviar" + id).removeAttribute("disabled");
            document.querySelector("#btn-eliminar" + id).removeAttribute("disabled");
            document.querySelector("#btn-aceptar").removeAttribute("disabled");
            alert(a?.responseJSON?.message || 'error al crear el registro')
        }
    });

}

const elimina = (id) => {
    if (window.confirm('¿Desea eliminar esta grabación de la lista?')) {
        $('#content_records' + id).remove();
        finalsAudioFile = finalsAudioFile.filter(e => e?.id !== id);       
        maxNumAudios--;
    }
}