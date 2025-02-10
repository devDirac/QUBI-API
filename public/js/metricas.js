let palabras = [];
let elementosParaGenerarReporte = [];
const urlApp = "https://diracapm.qubi.com.mx/";
const urlApp_file = "https://diracapm.qubi.com.mx/";
//const urlApp = 'http://localhost/APM/public/';
//const urlApp_file = 'http://localhost/APM/';
$(document).ready(async () => {
    var myModal = new bootstrap.Modal(document.getElementById('modalLoader'), {
        backdrop: "static", keyboard: false
    });

    $('#fechaInicio').val((new Date()).toISOString().split('T')[0]);
    $('#fechaFin').val((new Date()).toISOString().split('T')[0]);
    $('#fechaInicio_periodo').val((new Date()).toISOString().split('T')[0]);
    $('#fechaFin_periodo').val((new Date()).toISOString().split('T')[0]);
    $('#fechaInicio_periodo_consulta').val((new Date()).toISOString().split('T')[0]);
    $('#fechaFin_periodo_consulta').val((new Date()).toISOString().split('T')[0]);
    /* Obtiene los parametros del url y los divide en segmentos */
    const getParameterValues = (param) => {
        var url = window.location.href
            .slice(window.location.href.indexOf("?") + 1)
            .split("&");
        for (var i = 0; i < url.length; i++) {
            var urlparam = url[i].split("=");
            if (urlparam[0] == param) {
                return urlparam[1];
            }
        }
    };
    /* Obtiene y asigna el id de la solicitud en idSolicitud */
    const user = getParameterValues("user");
    document.querySelector("#keyWords").addEventListener("keypress", function (e) {
        const valor = $("#keyWords").val();
        let html = "";
        const existe = palabras.find((e) => e === valor);
        if (e.key === "Enter" && valor !== "" && !existe) {
            $("#listaPalabrasClave").html("");
            palabras.push(valor);
            palabras.forEach((element, key) => {
                html += `<p id="palabra_${key}" style="margin:5px; cursor:default;" class="btn btn-primary">${element} <span class="badge text-bg-danger" style="cursor:pointer;" onclick="remuevePalabra('${element}')">X</span></p>`;
            });
            $("#listaPalabrasClave").html(html);
            $("#keyWords").val("");
        }
    });

    document.querySelector("#btn-buscar").onclick = (e) => {
        myModal.toggle();
        const tipoReporte = $("#tipo_reporte").val();
        const fechaInicio = $("#fechaInicio").val();
        const fechaFin = $("#fechaFin").val();
        let queryPalabras_original = "texto_original REGEXP '";
        let queryPalabras_resumen = "resumen REGEXP '";
        palabras.forEach((element, key) => {
            queryPalabras_original += key === 0 ? `${element}` : `|${element}`;
            queryPalabras_resumen += key === 0 ? `${element}` : `|${element}`;
        });
        queryPalabras_original += "'";
        queryPalabras_resumen += "'";
        $.ajax({
            type: "GET",
            url: urlApp + "api/getDataQueryVoz",
            data: {
                ...{ user: user },
                ...(tipoReporte !== "" ? { tipoReporte } : {}),
                ...(fechaInicio !== "" ? { fechaInicio } : {}),
                ...(fechaFin !== "" ? { fechaFin } : {}),
                ...(palabras.length
                    ? { queryPalabras_original, queryPalabras_resumen }
                    : {}),
            },
            contentType: JSON,
            cache: false,
            Headers: {
                Accept: "application/json",
            },
            success: function (response) {
                myModal.hide();
                let bodyTable = "";
                if (!response.length) {
                    bodyTable += `<div class"row" style="margin:14px"><div class="card text-center"><h1>Sin resultados</h1></div></div>`
                }
                response.forEach((element) => {
                    bodyTable += `<div class"row" style="margin:14px" id="eliminaRegistro_${element?.id}_content"><div class="card text-center"> <div class="card-header" style="background-color:white">
                    <ul class="nav nav-tabs card-header-tabs" role="tablist" id="${element?.id}" style="background-color:white">
                    
                    ${element?.tipo !== 'IMAGEN' ? `<li class="nav-item" role="presentation">
                    <button class="nav-link active" id="home-tab${element?.id}" data-bs-toggle="tab" data-bs-target="#home-tab-pane${element?.id}" type="button" role="tab" aria-controls="home-tab-pane${element?.id}" aria-selected="true">Texto original</button>
                  </li>` : ''}

                  <li class="nav-item" role="presentation">
                    <button class="nav-link ${element?.tipo === 'IMAGEN' ? 'active' : ''}" id="profile-tab${element?.id}" data-bs-toggle="tab" data-bs-target="#profile-tab-pane${element?.id}" type="button" role="tab" aria-controls="profile-tab-pane${element?.id}" aria-selected="false">Resumen</button>
                  </li>

                  <li class="nav-item" role="presentation">
                    <button class="nav-link" id="contact-tab${element?.id}" data-bs-toggle="tab" data-bs-target="#contact-tab-pane${element?.id}" type="button" role="tab" aria-controls="contact-tab-pane${element?.id}" aria-selected="false"> ${element?.tipo === 'IMAGEN' ? 'Imagen' : 'Audio'}</button>
                  </li>
                  
                  <li class="nav-item btn-warning" role="presentation">
                    <button class="nav-link btn-warning" type="button" role="tab" style="background-color: #ffc107;" id="eliminaRegistro_${element?.id}" onclick="return eliminaRegistro(${element?.id})" aria-selected="false">Eliminar registro</button>
                  </li>



                  </ul>
                </div>
                <div class="card-body">
                <div class="tab-content" id="myTabContent">
                   
                ${element?.tipo !== 'IMAGEN' ? 
                    
                    `<div class="tab-pane fade show active" id="home-tab-pane${element?.id}" role="tabpanel" aria-labelledby="home-tab${element?.id}" tabindex="0" style="text-align:left;">
                    
                        <div class="row">
                            <div class="col-md-11 historico_grabaciones_word_resumen${element?.id}" id="historico_grabaciones_word${element?.id}">
                                ${element?.texto_original || ''}
                            </div>
                            <div class="col-md-1" style="cursor:pointer;">
                                <img src="${url_sitio}/img/14783314.png?v=1" onclick="Export2WordContent_word_resumen(${element?.id})" alt="Bootstrap" width="55" height="55">
                            </div>
                        </div>
                
            </div>` : ''}
                    
                   <div class="tab-pane fade ${element?.tipo === 'IMAGEN' ? 'show active' : ''}" id="profile-tab-pane${element?.id}" role="tabpanel" aria-labelledby="profile-tab${element?.id}" tabindex="0" style="text-align:left;">
                        <div class="row">
                            <div class="col-md-11 historico_grabaciones_word${element?.id}" id="historico_grabaciones_word${element?.id}">
                                ${element?.resumen || ''}
                            </div>
                            <div class="col-md-1" style="cursor:pointer;">
                                <img src="${url_sitio}/img/14783314.png?v=1" onclick="Export2WordContent_word(${element?.id})" alt="Bootstrap" width="55" height="55">
                            </div>
                        </div>                   
                   </div>



                    <div class="tab-pane fade" id="contact-tab-pane${element?.id}" role="tabpanel" aria-labelledby="contact-tab${element?.id}" tabindex="0">${element?.tipo !== 'IMAGEN' ? `<audio  class="btn btn-default" controls src="${urlApp_file}${element?.ruta || ''}"></audio>` : `<img src="${element?.img || ''}" class="img-fluid" alt="Responsive image">`}</div>
                </div>
                </div>
              </div></div>`;
                });
                $("#content").html(bodyTable);
            },
            error: function (a, b, c) {
                myModal.hide();
                alert(a?.responseJSON?.message || "error al buscar");
            },
        });
    };


    document.querySelector("#btn-generar-reporte").onclick = (e) => {
        myModal.toggle();
        const fechaInicio = $("#fechaInicio_periodo").val();
        const fechaFin = $("#fechaFin_periodo").val();
        if (fechaInicio === '' || fechaFin === '') {
            alert('Aun no selecciona un rango de fechas para la generación de resultados')
            return;
        }
        elementosParaGenerarReporte = []
        let queryPalabras_original = "texto_original REGEXP '";
        let queryPalabras_resumen = "resumen REGEXP '";
        palabras.forEach((element, key) => {
            queryPalabras_original += key === 0 ? `${element}` : `|${element}`;
            queryPalabras_resumen += key === 0 ? `${element}` : `|${element}`;
        });
        queryPalabras_original += "'";
        queryPalabras_resumen += "'";
        $.ajax({
            type: "GET",
            url: urlApp + "api/getDataQueryVoz",
            data: {
                ...{ user: user },
                ...(fechaInicio !== "" ? { fechaInicio } : {}),
                ...(fechaFin !== "" ? { fechaFin } : {}),
                ...(palabras.length
                    ? { queryPalabras_original, queryPalabras_resumen }
                    : {}),
            },
            contentType: JSON,
            cache: false,
            Headers: {
                Accept: "application/json",
            },
            success: function (response) {
                myModal.hide();
                let bodyTable = "";
                if (!response.length) {
                    bodyTable += `<div class"row" style="margin:14px"><div class="card text-center"><h1>Sin resultados</h1></div></div>`
                }
                bodyTable += `<ol class="list-group">`;
                if (response.length) {
                    bodyTable += '<li class="list-group-item" style="text-align:left"><p>Seleccione los elementos que formaran parte del resumen global </p> </li>';
                }
                response.forEach((element) => {
                    bodyTable += `<li class="list-group-item align-items-start"> 
                            <div class="row">
                           
                            <div class="col-md-2 col-sm-12" style="text-align:left;">
                                 <div class="fw-bold " style="text-align:left;">Seleccionar</div>
                            <input class="form-check-input me-1" type="checkbox" value="" onclick="myFunction(${element?.id || ''})" id="firstCheckbox_${element?.id || ''}">
                            </div>

                             <div class="col-md-2 col-sm-12" style="text-align:left;">
                                 <div class="fw-bold " style="text-align:left;">Tipo</div>
                            ${element?.tipo || ''}
                            </div>

                            <div class="col-md-3 col-sm-12" style="text-align:left;">
                                 <div class="fw-bold " style="text-align:left;">Fecha de creación</div>
                            ${element?.fecha_registro || ''}
                            </div>
                          
                            <div class="col-md-5 col-sm-12" style="text-align:left;">
                             <div class="fw-bold " style="text-align:left;">Resumen</div>
                            ${element?.resumen || ''}
                            </div>
                            </div>
                            </li>`
                });
                bodyTable += `</ol>`;
                $("#content_resultado_consulta_periodo").html(bodyTable);
                if (response.length) {
                    $('#generador-reporte-boton').show()
                }

            },
            error: function (a, b, c) {
                myModal.hide();
                alert(a?.responseJSON?.message || "error al buscar");
            },
        });
    };

    document.querySelector("#btn-consultar-reporte").onclick = (e) => {
        myModal.toggle();
        const fechaInicio = $("#fechaInicio_periodo_consulta").val();
        const fechaFin = $("#fechaFin_periodo_consulta").val();
        if (fechaInicio === '' || fechaFin === '') {
            alert('Aun no selecciona un periodo para la generación de resultados')
            return;
        }
        elementosParaGenerarReporte = []
        $.ajax({
            type: "GET",
            url: urlApp + "api/getDataQueryReportesCreados",
            data: {
                ...{ user: user },
                ...(fechaInicio !== "" ? { fechaInicio } : {}),
                ...(fechaFin !== "" ? { fechaFin } : {}),
            },
            contentType: JSON,
            cache: false,
            Headers: {
                Accept: "application/json",
            },
            success: function (response) {
                myModal.hide();
                let bodyTable = "";
                if (!response.length) {
                    bodyTable += `<div class"row" style="margin:14px"><div class="card text-center"><h1>Sin resultados</h1></div></div>`
                }
                if (response.length) {
                    $('#export_word').show()
                }
                bodyTable += `<ol class="list-group">`
                response.forEach((element) => {
                    bodyTable +=

                        `<li class="list-group-item align-items-start historico_grabaciones_word_resumen_resume${element?.id}" id="eliminaRegistro_${element?.id}_content"> 
                    <div class="row">
                    <div class="col-md-2 col-sm-12" style="text-align:left;">
                         <div class="fw-bold " style="text-align:left;">Periodo seleccionado</div>
                    ${(new Date(element?.fecha_inicio_reporte_generado || '')).toISOString().split('T')[0]} al ${(new Date(element?.fecha_fin_reporte_generado || '')).toISOString().split('T')[0]}
                    </div>
                   
                    <div class="col-md-2 col-sm-12" style="text-align:left;">
                         <div class="fw-bold " style="text-align:left;">Fecha creación</div>
                    ${element?.fecha_registro || ''}
                    </div>
                  
                    
                    <div class="col-md-8 col-sm-12 " style="text-align:left;" >
                     <div class="fw-bold " style="text-align:left;">Resumen</div>
                    ${element?.resumen || ''}
                    </div>


                    <div class="col-md-12 col-sm-12" style="text-align:left;">
                     <div class="fw-bold " style="text-align:left;"></div>
                        <button class="btn btn-warning" type="button" role="tab" style="background-color: #ffc107;" id="eliminaRegistro_${element?.id}" onclick="return eliminaRegistro(${element?.id})" aria-selected="false">Eliminar registro</button>
                        <button class="btn btn-default" type="button" role="tab" aria-selected="false">
                            <img src="${url_sitio}/img/14783314.png?v=1" onclick="return Export2WordContent_word_resumen_resume(${element?.id})" alt="Bootstrap" width="55" height="55">
                        </button>
                    </div>

                    


                    </div>
                    </li>`

                });
                bodyTable += `</ol>`;
                $("#content_resultado_consulta_periodo_consulta").html(bodyTable);
                $('#generador-reporte-boton').show()
            },
            error: function (a, b, c) {
                myModal.hide();
                $('#export_word').hide()
                alert(a?.responseJSON?.message || "error al buscar");
            },
        });
    };

    document.querySelector("#generador-reporte-boton").onclick = (e) => {

        if (!elementosParaGenerarReporte?.length) {
            alert('No se han seleccionado elementos para la creación del reporte')
            return;
        }
        if (elementosParaGenerarReporte?.length === 1) {
            alert('Seleccione al menos dos registros')
            return;
        }
        myModal.toggle();
        document.querySelector("#generador-reporte-boton").setAttribute("disabled", "disabled");
        document.querySelector("#generador-reporte-boton").textContent = 'Cargando...';;
        const fechaInicio = $("#fechaInicio_periodo").val();
        const fechaFin = $("#fechaFin_periodo").val();
        const user = getParameterValues("user");
        const proyecto = getParameterValues('p');
        const contrato = getParameterValues('idc');
        const concepto = getParameterValues('c');
        const avance = getParameterValues('a');
        $.ajax({
            type: "POST",
            url: urlApp + "api/generaReportePeriodosVarios",
            data: {
                ...{ user: user, ids: elementosParaGenerarReporte.toString(),proyecto,contrato,concepto,avance },
                ...(fechaInicio !== "" ? { fechaInicio } : {}),
                ...(fechaFin !== "" ? { fechaFin } : {}),
            },
            Headers: {
                Accept: "application/json",
            },
            success: function (response) {
                //elementosParaGenerarReporte = [];
                myModal.hide();
                document.querySelector("#generador-reporte-boton").removeAttribute("disabled");
                document.querySelector("#generador-reporte-boton").textContent = 'Crear reporte';
                alert('Exito al generar su reporte, lo puede consultar en la pestaña: Consulta de resúmenes globales');
                $('#btn-generar-reporte').trigger("click");
            },
            error: function (a, b, c) {
                myModal.hide();
                document.querySelector("#generador-reporte-boton").removeAttribute("disabled");
                document.querySelector("#generador-reporte-boton").textContent = 'Crear reporte';
                alert(a?.responseJSON?.message || "error al buscar");
            },
        });
    };

});

const remuevePalabra = (key) => {
    const nuevas = palabras.filter((e) => e !== key);
    palabras = nuevas;
    let html = "";
    $("#listaPalabrasClave").html("");
    palabras.forEach((element, key) => {
        html += `<p id="palabra_${key}" style="margin:5px; cursor:default;" class="btn btn-primary">${element} <span class="badge text-bg-danger" style="cursor:pointer;" onclick="remuevePalabra('${element}')">X</span></p>`;
    });
    $("#listaPalabrasClave").html(html);
};

const myFunction = (id) => {
    if ($('#firstCheckbox_' + id).is(':checked')) {
        elementosParaGenerarReporte.push(id);
    } else {
        elementosParaGenerarReporte = elementosParaGenerarReporte.filter(e => e !== id);
    }
}

const Export2Word = () => {
    const fechaInicio = $("#fechaInicio_periodo_consulta").val();
    const fechaFin = $("#fechaFin_periodo_consulta").val();
    var element = 'content_resultado_consulta_periodo_consulta'
    var filename = 'Resumen del periodo ' + fechaInicio + ' ' + fechaFin;
    var preHtml = "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'><head><meta charset='utf-8'><title>Export HTML To Doc</title></head><body>";
    var postHtml = "</body></html>";
    var html = preHtml + document.getElementById(element).innerHTML + postHtml;
    var blob = new Blob(['\ufeff', html], {
        type: 'application/msword'
    });
    var url = 'data:application/vnd.ms-word;charset=utf-8,' + encodeURIComponent(html);
    filename = filename ? filename + '.doc' : 'document.doc';
    var downloadLink = document.createElement("a");
    document.body.appendChild(downloadLink);
    if (navigator.msSaveOrOpenBlob) {
        navigator.msSaveOrOpenBlob(blob, filename);
    } else {
        downloadLink.href = url;
        downloadLink.download = filename;
        downloadLink.click();
    }
    document.body.removeChild(downloadLink);
}

const Export2WordContent_word = (id) => {
    let textFromDiv = ''
    var priceEls = document.getElementsByClassName("historico_grabaciones_word"+id);
    for (var i = 0; i < priceEls.length; i++) {
        var price = priceEls[i].innerText;
        textFromDiv += i + 1 + ': ' + price + '   <br>';
    }
    console.log('*',textFromDiv)
    $('#content_toExport').html(textFromDiv)
    const fechaInicio = $("#fechaInicio").val();
    const fechaFin = $("#fechaFin").val();
    var filename = 'Reporte de texto resumido: ' + fechaInicio + ' ' + fechaFin+' ID:'+id;
    var preHtml = "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'><head><meta charset='utf-8'><title>Export HTML To Doc</title></head><body>";
    var postHtml = "</body></html>";
    var html = preHtml + document.getElementById('content_toExport').innerHTML + postHtml;
    var blob = new Blob(['\ufeff', html], {
        type: 'application/msword'
    });
    var url = 'data:application/vnd.ms-word;charset=utf-8,' + encodeURIComponent(html);
    filename = filename ? filename + '.doc' : 'document.doc';
    var downloadLink = document.createElement("a");
    document.body.appendChild(downloadLink);
    if (navigator.msSaveOrOpenBlob) {
        navigator.msSaveOrOpenBlob(blob, filename);
    } else {
        downloadLink.href = url;
        downloadLink.download = filename;
        downloadLink.click();
    }
    document.body.removeChild(downloadLink);
    $('#content_toExport').html('')
}

const Export2WordContent_word_resumen = (id) => {
    let textFromDiv = ''
    var priceEls = document.getElementsByClassName("historico_grabaciones_word_resumen"+id);
    for (var i = 0; i < priceEls.length; i++) {
        var price = priceEls[i].innerText;
        textFromDiv += i + 1 + ': ' + price + '   <br>';
    }
    console.log('*',textFromDiv)
    $('#content_toExport').html(textFromDiv)
    const fechaInicio = $("#fechaInicio").val();
    const fechaFin = $("#fechaFin").val();
    var filename = 'Reporte de texto original: ' + fechaInicio + ' ' + fechaFin+' ID:'+id;
    var preHtml = "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'><head><meta charset='utf-8'><title>Export HTML To Doc</title></head><body>";
    var postHtml = "</body></html>";
    var html = preHtml + document.getElementById('content_toExport').innerHTML + postHtml;
    var blob = new Blob(['\ufeff', html], {
        type: 'application/msword'
    });
    var url = 'data:application/vnd.ms-word;charset=utf-8,' + encodeURIComponent(html);
    filename = filename ? filename + '.doc' : 'document.doc';
    var downloadLink = document.createElement("a");
    document.body.appendChild(downloadLink);
    if (navigator.msSaveOrOpenBlob) {
        navigator.msSaveOrOpenBlob(blob, filename);
    } else {
        downloadLink.href = url;
        downloadLink.download = filename;
        downloadLink.click();
    }
    document.body.removeChild(downloadLink);
    $('#content_toExport').html('')
}


const Export2WordContent_word_resumen_resume = (id) => {
    let textFromDiv = ''
    var priceEls = document.getElementsByClassName("historico_grabaciones_word_resumen_resume"+id);
    for (var i = 0; i < priceEls.length; i++) {
        var price = priceEls[i].innerText;
        textFromDiv += i + 1 + ': ' + price + '   <br>';
    }
    console.log('*',textFromDiv)
    $('#content_toExport').html(textFromDiv)
    const fechaInicio = $("#fechaInicio").val();
    const fechaFin = $("#fechaFin").val();
    var filename = 'Reporte de texto original: ' + fechaInicio + ' ' + fechaFin+' ID:'+id;
    var preHtml = "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'><head><meta charset='utf-8'><title>Export HTML To Doc</title></head><body>";
    var postHtml = "</body></html>";
    var html = preHtml + document.getElementById('content_toExport').innerHTML + postHtml;
    var blob = new Blob(['\ufeff', html], {
        type: 'application/msword'
    });
    var url = 'data:application/vnd.ms-word;charset=utf-8,' + encodeURIComponent(html);
    filename = filename ? filename + '.doc' : 'document.doc';
    var downloadLink = document.createElement("a");
    document.body.appendChild(downloadLink);
    if (navigator.msSaveOrOpenBlob) {
        navigator.msSaveOrOpenBlob(blob, filename);
    } else {
        downloadLink.href = url;
        downloadLink.download = filename;
        downloadLink.click();
    }
    document.body.removeChild(downloadLink);
    $('#content_toExport').html('')
}

const Export2WordContent = () => {
    let textFromDiv = ''
    var priceEls = document.getElementsByClassName("resumen");
    for (var i = 0; i < priceEls.length; i++) {
        var price = priceEls[i].innerText;
        textFromDiv += i + 1 + ': ' + price + '   <br>';
    }
    $('#content_toExport').html(textFromDiv)
    const fechaInicio = $("#fechaInicio").val();
    const fechaFin = $("#fechaFin").val();
    var filename = 'Historico de grabaciones del periodo: ' + fechaInicio + ' ' + fechaFin;
    var preHtml = "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'><head><meta charset='utf-8'><title>Export HTML To Doc</title></head><body>";
    var postHtml = "</body></html>";
    var html = preHtml + document.getElementById('content_toExport').innerHTML + postHtml;
    var blob = new Blob(['\ufeff', html], {
        type: 'application/msword'
    });
    var url = 'data:application/vnd.ms-word;charset=utf-8,' + encodeURIComponent(html);
    filename = filename ? filename + '.doc' : 'document.doc';
    var downloadLink = document.createElement("a");
    document.body.appendChild(downloadLink);
    if (navigator.msSaveOrOpenBlob) {
        navigator.msSaveOrOpenBlob(blob, filename);
    } else {
        downloadLink.href = url;
        downloadLink.download = filename;
        downloadLink.click();
    }
    document.body.removeChild(downloadLink);
    $('#content_toExport').html('')
}

const eliminaRegistro = (id) => {
    var myModal = new bootstrap.Modal(document.getElementById('modalLoader'), {
        backdrop: "static", keyboard: false
    });

    if (window.confirm('¿Esta seguro que desea eliminar esta elemento?')) {
        myModal.toggle();
        document.querySelector("#eliminaRegistro_" + id).setAttribute("disabled", "disabled");
        document.querySelector("#eliminaRegistro_" + id).textContent = 'Procesando solicitud...';
        $.ajax({
            type: "POST",
            url: urlApp + "api/eliminaGrabacion",
            data: { id },
            Headers: {
                Accept: "application/json",
            },
            success: function (response) {
                myModal.hide();
                alert(response);
                $('#eliminaRegistro_' + id + '_content').remove();
                document.querySelector("#eliminaRegistro_" + id).removeAttribute("disabled");
            },
            error: function (a, b, c) {
                myModal.hide();
                alert(a?.responseJSON?.message || "error al eliminar el registro");
            },
        });
    }
}