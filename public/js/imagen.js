const urlApp_imagen = "https://diracapm.qubi.com.mx/";
const urlApp_file_imagen = "https://diracapm.qubi.com.mx/";
//const urlApp_imagen = 'http://localhost/APM/public/';
//const urlApp_file_imagen = 'http://localhost/APM/';
let imagenesEnviar = [];
let parametros = null;
let esEdicionParametro = 0;
var myModal = new bootstrap.Modal(document.getElementById('modalLoader'), {
    backdrop: "static", keyboard: false
});

$(document).ready(async () => {
    var videoWidth = 320;
    var videoHeight = 240;
    var videoTag = document.getElementById('theVideo');
    var btnCapture = document.getElementById("btnCapture");
    var btnAddParametro = document.getElementById("btn-add-parametro");

    let table = null;
    table = $('#tabla_parametros').DataTable();

    const getParametros = () => {
        $.ajax({
            type: "GET",
            url: urlApp_imagen + "api/getParametros",
            data: { user },
            Headers: {
                Accept: "application/json",
            },
            success: function (response) {
                esEdicionParametro = 0;
                $('#nombre_del_parametro').val('');
                $('#descripcion_del_parametro').val('');
                $('#btn-add-parametro').text('Guardar');
                parametros = response;
                $('#tabla_parametros').DataTable().destroy();
                $('#content_table').html('');
                (response || []).forEach(element => {
                    let html = `<tr>`;
                    html += `<th scope="row">${element?.id}</th>`;
                    html += `<td >${element?.nombre || ''}</td>`;
                    html += `<td >${element?.descripcion || ''}</td>`;
                    html += `<td>
                            <button class="btn btn-danger" type="button"  id="elimina_parametro_${element?.id}" onclick="return eliminaParametro(${element?.id})" aria-selected="false"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
  <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
</svg></button>
                            <button class="btn btn-warning" type="button"  id="edita_parametro_${element?.id}" onclick="return editaParametro(${element?.id})" aria-selected="false"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-fill" viewBox="0 0 16 16">
  <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z"/>
</svg></button>
                        </td>`;
                    html += `</tr>`;
                    $("#content_table").append(html);


                });
                table = $('#tabla_parametros').DataTable();
            },
            error: function (a, b, c) {
                myModal.hide();
                alert(a?.responseJSON?.message || "error al eliminar el registro");
            },
        });
    }



    const inicializaServicio = () => {
        videoTag.setAttribute('autoplay', '');
        videoTag.setAttribute('muted', '');
        videoTag.setAttribute('playsinline', '')

        videoTag.setAttribute('width', videoWidth);
        videoTag.setAttribute('height', videoHeight);
        navigator.mediaDevices.getUserMedia({
            audio: false,
            video: {
                width: videoWidth,
                height: videoHeight,
                facingMode: { exact: "environment" },
            }
        }).then(stream => {
            videoTag.srcObject = stream;
            getParametros();
        }).catch(e => {
            $('#theVideo').hide();
            $('#btnCapture').hide();
            document.getElementById('errorTxt').innerHTML = 'Esta funcionalidad solo se permite en dispositivos móviles con cámara trasera'/*  + e.toString() */;
        });
    }

    inicializaServicio();
    getParametros();

    btnAddParametro.addEventListener("click", () => {
        myModal.toggle()
        const nombre = $('#nombre_del_parametro').val();
        const parametros = $('#descripcion_del_parametro').val();
        $.ajax({
            type: "POST",
            url: urlApp_imagen + (esEdicionParametro === 0 ? "api/creaParametro" : "api/editaParametro"),
            data: { ...{ nombre, user }, ...(parametros === '' ? {} : { parametros }), ... (esEdicionParametro === 0 ? {} : { id: esEdicionParametro }) },
            Headers: {
                Accept: "application/json",
            },
            success: function (response) {
                alert('Exito al guardar');
                esEdicionParametro = 0;
                myModal.hide();
                getParametros();
            },
            error: function (a, b, c) {
                myModal.hide();
                alert(a?.responseJSON?.message || "error en la operación");
            },
        });

    })

    btnCapture.addEventListener("click", () => {
        if (imagenesEnviar?.length >= 9) {
            alert('Ha llegado al maximo de imagenes, envie las grabaciones en lista o eliminelas para poder agregar mas imagenes a la lista');
            return;
        }
        let html = '';
        html += `<li class="list-group-item align-items-start" id="listaCanvas_${imagenesEnviar?.length}_content"> 
            <div class="row">
                <div class="col-md-12 col-sm-12" style="text-align:center;">
                        <canvas id="CursorLayer_${imagenesEnviar?.length}" width="320" height="240"></canvas>
                </div>
                <div class="col-md-12 col-sm-12" style="text-align:center;">
                <select class="form-select" aria-label="Default select example" id="select_tipo_parametros_${imagenesEnviar?.length}">
                        <option selected value="">Selecciona el parametro de analisis</option>`;
        (parametros || []).forEach(element => {
            html += `<option value=${element?.id}>${element?.nombre}</option>`;
        });
        html += ` </select>
                </div>
                <div class="col-md-12 col-sm-12" style="text-align:center;">
                    <button class="btn btn-success" type="button"  id="enviar_foto_${imagenesEnviar?.length}" onclick="return enviarFoto(${imagenesEnviar?.length})" aria-selected="false">Enviar foto</button>
                    <button class="btn btn-danger" type="button"  id="descartar_foto_${imagenesEnviar?.length}" onclick="return descartarFoto(${imagenesEnviar?.length})" aria-selected="false">Descartar foto</button>
                </div>
            </div>
        </li>`;
        $("#content_canvas_imagen_content").append(html);
        var canvasTag = document.getElementById(`CursorLayer_${imagenesEnviar?.length}`);
        const canvasContext_ = canvasTag.getContext('2d');
        canvasContext_.drawImage(videoTag, 0, 0, videoWidth, videoHeight);
        var dataURL = canvasTag.toDataURL();
        var blob = dataURLtoBlob(dataURL);
        imagenesEnviar.push({ id: imagenesEnviar?.length, imagen: blob });
        $('#content_canvas_imagen').show();
    });

    function dataURLtoBlob(dataURL) {
        var arr = dataURL.split(','),
            mime = arr[0].match(/:(.*?);/)[1],
            bstr = atob(arr[1]),
            n = bstr.length,
            u8arr = new Uint8Array(n);
        while (n--) {
            u8arr[n] = bstr.charCodeAt(n);
        }
        return new Blob([u8arr], {
            type: mime
        });
    }
});

const getParametros = () => {
    let table = null;
    table = $('#tabla_parametros').DataTable();
    $.ajax({
        type: "GET",
        url: urlApp_imagen + "api/getParametros",
        data: { user },
        Headers: {
            Accept: "application/json",
        },
        success: function (response) {
            parametros = response;
            $('#tabla_parametros').DataTable().destroy();
            $('#content_table').html('');
            (response || []).forEach(element => {
                let html = `<tr>`;
                html += `<th scope="row">${element?.id}</th>`;
                html += `<td>${element?.nombre || ''}</td>`;
                html += `<td>${element?.descripcion || ''}</td>`;
                html += `<td>
                    <button class="btn btn-danger" type="button"  id="elimina_parametro_${element?.id}" onclick="return eliminaParametro(${element?.id})" aria-selected="false"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
  <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
</svg></button>
                    <button class="btn btn-warning" type="button"  id="edita_parametro_${element?.id}" onclick="return editaParametro(${element?.id})" aria-selected="false"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-fill" viewBox="0 0 16 16">
  <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z"/>
</svg></button>
                </td>`;
                html += `</tr>`;
                $("#content_table").append(html);
            });
            table = $('#tabla_parametros').DataTable();
        },
        error: function (a, b, c) {
            myModal.hide();
            alert(a?.responseJSON?.message || "error al eliminar el registro");
        },
    });
}

const inicializaServicio = () => {
    var videoWidth = 320;
    var videoHeight = 240;
    var videoTag = document.getElementById('theVideo');

    videoTag.setAttribute('autoplay', '');
    videoTag.setAttribute('muted', '');
    videoTag.setAttribute('playsinline', '')

    videoTag.setAttribute('width', videoWidth);
    videoTag.setAttribute('height', videoHeight);
    getParametros();
    navigator.mediaDevices.getUserMedia({
        audio: false,
        video: {
            width: videoWidth,
            height: videoHeight,
            facingMode: { exact: "environment" },
        }
    }).then(stream => {
        videoTag.srcObject = stream;

    }).catch(e => {
        $('#theVideo').hide();
        $('#btnCapture').hide();
        document.getElementById('errorTxt').innerHTML = 'Esta funcionalidad solo se permite en dispositivos móviles con cámara trasera'/*  + e.toString() */;
    });
}

const dataURLtoBlob = (dataURL) => {
    var arr = dataURL.split(','),
        mime = arr[0].match(/:(.*?);/)[1],
        bstr = atob(arr[1]),
        n = bstr.length,
        u8arr = new Uint8Array(n);
    while (n--) {
        u8arr[n] = bstr.charCodeAt(n);
    }
    return new Blob([u8arr], {
        type: mime
    });
}

const enviarFoto = (id) => {
    myModal.toggle();
    const canvasSelected = document.getElementById('CursorLayer_' + id);
    var dataURL = canvasSelected.toDataURL();
    var blob = dataURLtoBlob(dataURL);
    var data = new FormData();
    data.append("capturedImage", blob, "capturedImage.png");
    var reader = new FileReader();
    reader.readAsDataURL(blob);
    reader.onloadend = function () {
        const parametros = $('#select_tipo_parametros_' + id).val();
        var base64data = reader.result;
        $.ajax({
            type: "POST",
            url: urlApp_imagen + "api/analizaImagen",
            data: parametros !== "" ? { image: base64data, user, proyecto, contrato, concepto, avance, parametros } : { image: base64data, user, proyecto, contrato, concepto, avance },
            Headers: {
                Accept: "application/json",
            },
            success: function (response) {
                alert('Exito al guardar');
                $('#listaCanvas_' + id + '_content').remove();
                imagenesEnviar = imagenesEnviar.filter(e => e?.id !== id);
                myModal.hide();
                inicializaServicio();
                getParametros();
            },
            error: function (a, b, c) {
                myModal.hide();
                alert(a?.responseJSON?.message || "error al eliminar el registro");
            },
        });
    }
}

const descartarFoto = (id) => {
    if (window.confirm('¿Desea eliminar esta captura de imagen de la lista?')) {
        $('#listaCanvas_' + id + '_content').remove();
        imagenesEnviar = imagenesEnviar.filter(e => e?.id !== id);
        inicializaServicio();
        getParametros();
    }
}

const eliminaParametro = (id) => {
    if (window.confirm('¿Desea eliminar esta parametro de analisis de imagen?')) {
        myModal.toggle();
        $.ajax({
            type: "POST",
            url: urlApp_imagen + "api/eliminaParametro",
            data: { id },
            Headers: {
                Accept: "application/json",
            },
            success: function (response) {
                alert('Exito al guardar');

                myModal.hide();
                getParametros();
            },
            error: function (a, b, c) {
                myModal.hide();
                alert(a?.responseJSON?.message || "error al eliminar el registro");
            },
        });
        //
    }
}

const editaParametro = (id) => {
    esEdicionParametro = id;
    const parametroSeleccionado = parametros.find(e => e?.id === id)
    $('#nombre_del_parametro').val(parametroSeleccionado?.nombre);
    $('#descripcion_del_parametro').val(parametroSeleccionado?.descripcion);
    $('#btn-add-parametro').text('Guardar cambios');
}