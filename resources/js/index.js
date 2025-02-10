
$(document).ready(async () => {
    let leftchannel = [];
    let rightchannel = [];
    let recording = false;
    let recordingLength = 0;
    let audioInput = null;
    let sampleRate = null;
    let AudioContext = window.AudioContext || window.webkitAudioContext;
    let context = null;
    let analyser = null;
    let canvas = document.querySelector('canvas');
    let canvasCtx = canvas.getContext("2d");
    let micSelect = document.querySelector('#micSelect');
    let stream = null;
    let tested = false;
    let finalFile = null;

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

    } catch (err) {
        $('#btn-aceptar').hide();
        $('#micSelect_').hide();

    }
    let deviceInfos = null;
    if (navigator?.mediaDevices?.enumerateDevices) {
        deviceInfos = await navigator.mediaDevices.enumerateDevices();
        micros();
        setUpRecording();
    }

    function micros() {
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
    }

    function getStream(constraints) {
        if (!constraints) {
            constraints = { audio: true, video: false };
        }
        return navigator.mediaDevices.getUserMedia(constraints);
    }



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
        document.querySelector('#audio').setAttribute('src', audioUrl);
        finalFile = blob;
        $('#audio').show();
        $('#btn-enviar').show();
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
        finalFile = null;
        document.querySelector('#audio').setAttribute('src', '');
        $('#feetBack').show();
        $('#btn-detener').show();
        $('#audio').hide();
        $('#btn-enviar').hide();
        start();
    }

    document.querySelector('#btn-detener').onclick = (e) => {
        $('#feetBack').hide();
        $('#btn-detener').hide();
        stop();
    }

    const getBuffer = (resolve) => {
        var reader = new FileReader();
        reader.onload = function () {
            var arrayBuffer = reader.result;
            resolve(arrayBuffer);
        }
        reader.readAsArrayBuffer(fileData);
    }

    function getWavBytes(buffer, options) {
        const type = options.isFloat ? Float32Array : Uint16Array
        const numFrames = buffer.byteLength / type.BYTES_PER_ELEMENT;

        const headerBytes = getWavHeader(Object.assign({}, options, { numFrames }));
        const wavBytes = new Uint8Array(headerBytes.length + buffer.byteLength);

        // prepend header, then add pcmBytes
        wavBytes.set(headerBytes, 0)
        wavBytes.set(new Uint8Array(buffer), headerBytes.length)

        return wavBytes
    }

    // adapted from https://gist.github.com/also/900023
    // returns Uint8Array of WAV header bytes
    function getWavHeader(options) {
        const numFrames = options.numFrames
        const numChannels = options.numChannels || 2
        const sampleRate = options.sampleRate || 44100
        const bytesPerSample = options.isFloat ? 4 : 2
        const format = options.isFloat ? 3 : 1

        const blockAlign = numChannels * bytesPerSample
        const byteRate = sampleRate * blockAlign
        const dataSize = numFrames * blockAlign

        const buffer = new ArrayBuffer(44)
        const dv = new DataView(buffer)

        let p = 0

        function writeString(s) {
            for (let i = 0; i < s.length; i++) {
                dv.setUint8(p + i, s.charCodeAt(i))
            }
            p += s.length
        }

        function writeUint32(d) {
            dv.setUint32(p, d, true)
            p += 4
        }

        function writeUint16(d) {
            dv.setUint16(p, d, true)
            p += 2
        }

        writeString('RIFF')              // ChunkID
        writeUint32(dataSize + 36)       // ChunkSize
        writeString('WAVE')              // Format
        writeString('fmt ')              // Subchunk1ID
        writeUint32(16)                  // Subchunk1Size
        writeUint16(format)              // AudioFormat https://i.stack.imgur.com/BuSmb.png
        writeUint16(numChannels)         // NumChannels
        writeUint32(sampleRate)          // SampleRate
        writeUint32(byteRate)            // ByteRate
        writeUint16(blockAlign)          // BlockAlign
        writeUint16(bytesPerSample * 8)  // BitsPerSample
        writeString('data')              // Subchunk2ID
        writeUint32(dataSize)            // Subchunk2Size

        return new Uint8Array(buffer)
    }

    function playBuffer(buffer) {
        source = audioCtx.createBufferSource();
        source.buffer = buffer;
        source.connect(audioCtx.destination);
        source.loop = true;
        source.start();
      }

      function decodeArrayBuffer(audioCtx, arrayBuffer) {
        return new Promise(audioCtx.decodeAudioData.bind(audioCtx, arrayBuffer));
      }

    document.getElementById("avatar").addEventListener("change", async (e) => {
        var audioContext = new (window.AudioContext || window.webkitAudioContext)();
        var analyser = audioContext.createAnalyser();
        
        fileData = new Blob([e.target?.files[0]]);
        var videoFileAsBuffer = await new Promise(getBuffer);
       /*  const aaa = await decodeArrayBuffer(audioContext,videoFileAsBuffer);
        alert('asdsad') */
        alert(0)
        console.log('videoFileAsBuffer',videoFileAsBuffer,aaa)
        const a = await window.DADF.getFileAudioBuffer(e.target?.files[0], audioContext,{ native: true });
        alert('cocha pacha')
        /* var formData = new FormData();
        formData.append("file", e.target?.files[0]);
        $.ajax({
            type: "POST",
            url: 'http://localhost/APM/public/api/returnAsItIs',
            data: formData,
            enctype: 'multipart/form-data',
            processData: false,
            contentType: false,
            cache: false,
            Headers: {
                "Content-Type": "multipart/form-data"
            },
            success: async function  (response) {
                console.log('response',response);

                var audioContext = new(window.AudioContext || window.webkitAudioContext)();
                var audioContext = new (window.AudioContext || window.webkitAudioContext)();
                var analyser = audioContext.createAnalyser();
                var data = new Uint8Array(analyser.frequencyBinCount);
        
                fileData = new Blob([e.target?.files[0]]);
                var videoFileAsBuffer = await new Promise(getBuffer);



                var audio = new Audio();
                audio.loop = true;
                audio.autoplay = false;
                audio.crossOrigin = "anonymous";
        
                audio.addEventListener('error', function (e) {
                    console.log(e);
                });
                audio.src = "https://greggman.github.io/doodles/sounds/DOCTOR VOX - Level Up.mp3";
                //audio.play();
                audio.controls = true;
        
                document.getElementById("wrapper").append(audio);
        
                audio.addEventListener('canplay', function () {
                    var audioSourceNode = audioContext.createMediaElementSource(audio);
        
                    audioSourceNode.connect(analyser);
                    analyser.connect(audioContext.destination);
                });
            },
            error: function (a, b, c) {
                
                alert(a?.responseJSON?.message || 'error al crear el registro')
            }
        }); */

        

         /* audioContext.decodeAudioData(videoFileAsBuffer, function(audioBuffer) {
            alert(0)
             const [left, right] =  [audioBuffer.getChannelData(0), audioBuffer.getChannelData(1)];
             const interleaved = new Float32Array(left.length + right.length)
             for (let src=0, dst=0; src < left.length; src++, dst+=2) {
               interleaved[dst] =   left[src]
               interleaved[dst+1] = right[src]
             }
             const wavBytes = getWavBytes(interleaved.buffer, {
               isFloat: true,
               numChannels: 2,
               sampleRate: 48000,
             })
             const wav = new Blob([wavBytes], { type: 'audio/wav' })
             const audioUrl = URL.createObjectURL(wav);
             document.querySelector('#audio').setAttribute('src', audioUrl);
             finalFile = wav;
             $('#audio').show();
             $('#btn-enviar').show();
         }); */


    }, true);

    //$("input[type=file]").on('change',);

    document.querySelector('#btn-enviar').onclick = (e) => {
        var formData = new FormData();
        formData.append("file", finalFile);
        formData.append("id_usuario", user);
        $('#btn-enviar').prop('disabled', true);
        $('#btn-aceptar').prop('disabled', true);
        $('#micSelect').prop('disabled', true);
        $.ajax({
            type: "POST",
            url: 'http://localhost/APM/public/api/reportSpeach',
            data: formData,
            enctype: 'multipart/form-data',
            processData: false,
            contentType: false,
            cache: false,
            Headers: {
                "Content-Type": "multipart/form-data"
            },
            success: function (response) {
                finalFile = null;
                document.querySelector('#audio').setAttribute('src', '');
                $('#avatar').val('')
                alert('Se ha registrado exitosamente');
                $('#btn-enviar').prop('disabled', false);
                $('#btn-aceptar').prop('disabled', false);
                $('#micSelect').prop('disabled', false);
            },
            error: function (a, b, c) {
                $('#btn-enviar').hide();
                $('#btn-enviar').prop('disabled', false);
                $('#btn-aceptar').prop('disabled', false);
                $('#micSelect').prop('disabled', false);
                alert(a?.responseJSON?.message || 'error al crear el registro')
            }
        });
    }
});