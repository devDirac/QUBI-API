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

    let table = null;
    table = $('#tabla').DataTable();

    document.querySelector("#btn-buscar").onclick = (e) => {
        myModal.toggle();

        const fechaInicio = $("#fechaInicio").val();
        const fechaFin = $("#fechaFin").val();
        $.ajax({
            type: "GET",
            url: urlApp + "api/getDataQueryGlobal",
            data: {
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
                table.destroy();
                $('#content_table').html('');
                (response || []).forEach(element => {
                    let html = `<tr>`;
                    html += `<th scope="row">${element?.id}</th>`;
                    if(element?.tipo === 'VOZ'){
                        html += `<td>${element?.tipo}</td>`;
                    }
                    if(element?.tipo === 'IMAGEN'){
                        html += `<td>${element?.tipo}</td>`;
                    }
                    if(element?.tipo === null){
                        html += `<td>Resumen global</td>`;
                    }
                    html += `<td style="width: 363px;">${element?.texto_original|| ''}</td>`;
                    html += `<td style="width: 363px;">${element?.resumen|| ''}</td>`;
                    
                    if(element?.tipo === 'VOZ'){
                        html += `<td><audio  class="btn btn-default" controls src="${urlApp_file}${element?.ruta || ''}"></audio></td>`;
                    }else{
                        html += `<td></td>`;
                    }
                    html += `<td>${element?.fecha_inicio_reporte_generado || ''}</td>`;
                    html += `<td>${element?.fecha_fin_reporte_generado|| ''}</td>`;
                    if(element?.tipo === 'VOZ'){
                        html += `<td></td>`;
                    }
                    if(element?.tipo === 'IMAGEN'){
                        html += `<td><a href="javascript:void();" onclick="return openDataUrl('${element?.img}')" target="_blank"><img src="${element?.img}" class="img-fluid" alt="Responsive image"></a></td>`;
                    }    
                    if(element?.tipo === null){
                        html += `<td></td>`;
                    }
                    html += `<td>${element?.fecha_registro}</td>`;
                    html += `<td>${element?.id_usuario}</td>`;
                    html += `</tr>`;
                    $("#content_table").append(html);
                    
                    
                });
                table = $('#tabla').DataTable();
                
            },
            error: function (a, b, c) {
                myModal.hide();
                alert(a?.responseJSON?.message || "error al buscar");
            },
        });
    };
});
const  openDataUrl = (base64URL)=>{
    let aWindow = window.open();

    aWindow.document.write('<iframe src="' + base64URL  + '" frameborder="0" style="border:0; top:0px; left:0px; bottom:0px; right:0px; width:100%; height:100%;" allowfullscreen></iframe>');
}
