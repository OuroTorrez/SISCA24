<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Include common resources -->
    <?php include 'commonResources.php'; ?>
    <title>Response - DIF Michoacán</title>
    <!-- Styles -->
    <link rel="stylesheet" href="Styles/ventanaResponseStyles.css">
</head>

<body>
    <div id="ResponseDocCont">
        <div id="ResponseDoc">
            <div class="ResponseTitle">
                <h2 style="color: var(--Background);">Registro generado exitosamente</h2>
                <button id="ResponseDocClose" class="ResponseDocCloseCustom" onclick="CloseResponse()">
                    <i class="bi bi-x-circle"></i>
                </button>
            </div>
            <object class="ResponseObject" data="" download=""></object>
        </div>
        <div id="WaitDoc">
            <div class="ResponseTitle">
                <h2 style="color: var(--Background);">Generando registro...</h2>
                <button id="ResponseDocClose" onclick="CloseResponse()">
                    <i class="bi bi-x-circle"></i>
                </button>
            </div>
            <h4>Por favor espere un momento</h4>
        </div>
        <div id="UploadDoc">
            <div class="ResponseTitle">
                <h2 style="color: var(--Background);">Sube tus documentos</h2>
                <button id="ResponseDocClose" onclick="CloseResponse()">
                    <i class="bi bi-x-circle"></i>
                </button>
            </div>
            <label class="inputFileLabel" for="SubirDocs">
                <span class="inputFileSpan">
                    Suelta tus archivos aquí<br>
                    O<br>
                </span>
                <input class="inputFile" type="file" name="SubirDocs" id="SubirDocs" accept=".pdf">
            </label>
            <input type="text" id="folio" value="" hidden>
            <button type="submit" onclick="subirDocumentos()">
                <i class="bi bi-cloud-upload"></i>
                <span>Subir</span>
            </button>
        </div>
        <div id="ResponseDocEditable">
            <div class="ResponseTitle">
                <h2 style="color: var(--Background);">Documentos subidos</h2>
                <button id="ResponseDocClose" class="ResponseDocCloseCustom" onclick="CloseResponse()">
                    <i class="bi bi-x-circle"></i>
                </button>
            </div>
            <object class="ResponseObject" data="" download=""></object>
            <button class="ResponseDocReplaceButton" onclick="">
                <i class="bi bi-cloud-upload"></i>
                <span>Reemplazar</span>
            </button>
        </div>
    </div>
</body>

</html>
<script>

    function WaitDoc(title, message) {
        $('#ResponseDocCont').css('display', 'flex');
        $('#ResponseDoc').css('display', 'none');
        $('#WaitDoc').css('display', 'flex');
        $('#UploadDoc').css('display', 'none');
        $('#ResponseDocEditable').css('display', 'none');
        $('.ResponseTitle h2').text(title);
        $('#WaitDoc h4').text(message);
        data = null;
        blob = null;
    }

    function ResponseDoc(title, objectData, downloadName, closeFunction) {
        $('#ResponseDocCont').css('display', 'flex');
        $('#ResponseDoc').css('display', 'flex');
        $('#WaitDoc').css('display', 'none');
        $('#UploadDoc').css('display', 'none');
        $('#ResponseDocEditable').css('display', 'none');
        $('.ResponseTitle h2').text(title);
        $('.ResponseObject').attr('data', objectData);
        $('.ResponseObject').attr('download', downloadName);
        $('#ResponseObjectFail').attr('href', objectData);
        $('#ResponseObjectFail').attr('download', downloadName);
        $('#ResponseDocClose').attr('onclick', closeFunction);
        data = null;
        blob = null;
    }

    function CloseResponse() {
        $('#ResponseDocCont').css('display', 'none');
        $('#ResponseDoc').css('display', 'none');
        $('#WaitDoc').css('display', 'none');
        $('#UploadDoc').css('display', 'none');
        $('#ResponseDocEditable').css('display', 'none');
        data = null;
        blob = null;
    }

    function UploadDoc(title, folio) {
        $('#ResponseDocCont').css('display', 'flex');
        $('#ResponseDoc').css('display', 'none');
        $('#WaitDoc').css('display', 'none');
        $('#UploadDoc').css('display', 'flex');
        $('#ResponseDocEditable').css('display', 'none');
        $('.ResponseTitle h2').text(title);
        $('#folio').val(folio);
        data = null;
        blob = null;
    }

    function ResponseDocEditable(title,objectData, closeFunction, replaceFunction) {
        $('#ResponseDocCont').css('display', 'flex');
        $('#ResponseDoc').css('display', 'none');
        $('#WaitDoc').css('display', 'none');
        $('#UploadDoc').css('display', 'none');
        $('#ResponseDocEditable').css('display', 'flex');
        $('.ResponseTitle h2').text(title);
        $('.ResponseObject').attr('data', objectData);
        $('#ResponseDocEditable .ResponseDocCloseCustom').attr('onclick', closeFunction);
        $('#ResponseDocEditable .ResponseDocReplaceButton').attr('onclick', replaceFunction);
        data = null;
        blob = null;
    }

    function generarEntradasyPDF(datoAEnviar, orientacion, toDownload) {
        WaitDoc("Generando registro...", "Por favor espere un momento");
        // Enviar los datos a enviarEntradas.php para darle formato HTML-->>PDF y/o guardar en BD
        $.ajax({
            url: 'enviarEntradas.php',
            type: 'POST',
            data: datoAEnviar,
            processData: false,
            contentType: false,
            success: function (response) {
                console.log(datoAEnviar);
                console.log(data);
                console.log(response);
                // Extraer el folio del registro de la respuesta HTML
                if (toDownload) {
                    var parser = new DOMParser();
                    var htmlDoc = parser.parseFromString(response, 'text/html');
                    var folio = htmlDoc.querySelector('#folioElement').innerText;
                }

                // Envia los datos para generar el PDF a generatePDF.php
                $.ajax({
                    url: 'generatePDF.php',
                    type: 'POST',
                    data: {
                        html: response,
                        orientation: orientacion
                    },
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function (response) {
                        // Crea un objeto Blob con los datos del PDF, esto para que pueda ser leído por el elemento object
                        var blob = new Blob([response], {
                            type: 'application/pdf'
                        });

                        // Crea un enlace para descargar el PDF
                        var link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        ResponseDoc("Registro generado exitosamente", link.href, link.download, 'window.location.reload()');
                        if (toDownload) {
                            // Obtiene la fecha actual y Formatea la fecha en el formato 'dia-mes-año para Asignar el nombre al archivo'
                            var date = new Date();
                            var formattedDate = date.getDate() + '-' + (date.getMonth() + 1) + '-' + date.getFullYear();
                            link.download = 'ENTRADA ' + folio + ' ' + formattedDate + '.pdf';
                            link.click();
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr, status, error);
                        WaitDoc("Error al generar el archivo PDF", "Por favor intente de nuevo");
                    }
                });

            },
            error: function (xhr, status, error) {
                // Maneja los errores aquí
                console.error(xhr, status, error);
                WaitDoc("Error al generar el registro", "Por favor intente de nuevo");
            }
        });
        data = null;
        blob = null;
    }

    function consultarPDFEntradas(datoAEnviar, orientacion, toDownload) {
        WaitDoc("Generando registro...", "Por favor espere un momento");
        // Enviar los datos a enviarEntradas.php para darle formato HTML-->>PDF y/o guardar en BD
        $.ajax({
            url: 'enviarEntradas.php',
            type: 'POST',
            data: {
                folio: datoAEnviar
            },
            success: function (response) {
                // Extraer el folio del registro de la respuesta HTML
                if (toDownload) {
                    var parser = new DOMParser();
                    var htmlDoc = parser.parseFromString(response, 'text/html');
                    var folio = htmlDoc.querySelector('#folioElement').innerText;
                }

                // Envia los datos para generar el PDF a generatePDF.php
                $.ajax({
                    url: 'generatePDF.php',
                    type: 'POST',
                    data: {
                        html: response,
                        orientation: orientacion
                    },
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function (response) {
                        // Crea un objeto Blob con los datos del PDF, esto para que pueda ser leído por el elemento object
                        var blob = new Blob([response], {
                            type: 'application/pdf'
                        });

                        // Crea un enlace para descargar el PDF
                        var link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        ResponseDoc("Registro generado exitosamente", link.href, link.download);
                        if (toDownload) {
                            // Obtiene la fecha actual y Formatea la fecha en el formato 'dia-mes-año para Asignar el nombre al archivo'
                            var date = new Date();
                            var formattedDate = date.getDate() + '-' + (date.getMonth() + 1) + '-' + date.getFullYear();
                            link.download = 'ENTRADA ' + folio + ' ' + formattedDate + '.pdf';
                            link.click();
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr, status, error);
                        WaitDoc("Error al generar el archivo PDF", "Por favor intente de nuevo");
                    }
                });

            },
            error: function (xhr, status, error) {
                // Maneja los errores aquí
                console.error(xhr, status, error);
                WaitDoc("Error al generar el registro", "Por favor intente de nuevo");
            }
        });
        data = null;
        blob = null;
    }

    function subirDocumentos() {
        WaitDoc("Subiendo tus documentos...", "Por favor espere un momento...");
        
        var folio = document.getElementById("folio").value;
        var docs = document.getElementById("SubirDocs").files[0]; // Acceder al archivo seleccionado
        var targetDirectory = "DocsEntradas/";
        var nombrePersonalizado = 'ENTRADAS_' + folio + '.pdf';

        var formData = new FormData();
        formData.append("docs", docs);
        formData.append("targetDirectory", targetDirectory);
        formData.append("targetFolio", folio);
        formData.append("docsName", 'ARCHIVOS ' + folio + '.pdf');
        formData.append("nombrePersonalizado", nombrePersonalizado);

        $.ajax({
            url: 'enviarEntradas.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                console.log(response);
                if (response != "") {
                    ResponseDocEditable("Documentos subidos exitosamente", response, 'location.reload()', 'UploadDoc("Reemplaza tus documentos", ' + folio + ')');
                    folioGlobal = folio;
                } else {
                    WaitDoc("Error al subir los documentos", "Por favor intente de nuevo");
                }
            },
            error: function (xhr, status, error) {
                console.error(xhr, status, error);
                WaitDoc("Error al enviar los documentos al servidor", "Por favor intente de nuevo");
            }
        });
        data = null;
        blob = null;
    }

    function consultarDoc(folio) {
        $.ajax({
            url: 'enviarEntradas.php',
            type: 'POST',
            data: {
                folioDocs: folio
            },
            success: function (response) {
                console.log(response);
                ResponseDocEditable("Documentos subidos", response, 'CloseResponse()', 'UploadDoc("Reemplaza tus documentos", ' + folio + ')');
            },
            error: function (xhr, status, error) {
                console.error(xhr, status, error);
                WaitDoc("Error al consultar los documentos", "Por favor intente de nuevo");
            }
        });
        data = null;
        blob = null;
    }

    function generarSalidasyPDF(datoAEnviar, orientacion, toDownload) {
        WaitDoc("Generando registro...", "Por favor espere un momento");
        // Enviar los datos a enviarSalidas.php para darle formato HTML-->>PDF y/o guardar en BD
        $.ajax({
            url: 'enviarSalidas.php',
            type: 'POST',
            data: datoAEnviar,
            processData: false,
            contentType: false,
            success: function (response) {
                console.log(datoAEnviar);
                console.log(data);
                console.log(response);
                // Extraer el folio del registro de la respuesta HTML
                if (toDownload) {
                    var parser = new DOMParser();
                    var htmlDoc = parser.parseFromString(response, 'text/html');
                    var folio = htmlDoc.querySelector('#folioElement').innerText;
                }

                // Envia los datos para generar el PDF a generatePDF.php
                $.ajax({
                    url: 'generatePDF.php',
                    type: 'POST',
                    data: {
                        html: response,
                        orientation: orientacion
                    },
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function (response) {
                        // Crea un objeto Blob con los datos del PDF, esto para que pueda ser leído por el elemento object
                        var blob = new Blob([response], {
                            type: 'application/pdf'
                        });

                        // Crea un enlace para descargar el PDF
                        var link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        ResponseDoc("Registro generado exitosamente", link.href, link.download, 'window.location.reload()');
                        if (toDownload) {
                            // Obtiene la fecha actual y Formatea la fecha en el formato 'dia-mes-año para Asignar el nombre al archivo'
                            var date = new Date();
                            var formattedDate = date.getDate() + '-' + (date.getMonth() + 1) + '-' + date.getFullYear();
                            link.download = 'SALIDA ' + folio + ' ' + formattedDate + '.pdf';
                            link.click();
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr, status, error);
                        WaitDoc("Error al generar el archivo PDF", "Por favor intente de nuevo");
                    }
                });

            },
            error: function (xhr, status, error) {
                // Maneja los errores aquí
                console.error(xhr, status, error);
                WaitDoc("Error al generar el registro", "Por favor intente de nuevo");
            }
        });
        data = null;
        blob = null;
    }


    // Drag and drop file input
    document.addEventListener("DOMContentLoaded", function () {
        const inputFileCont = document.querySelector(".inputFileLabel"); // Cambiado a querySelector
        const fileInput = document.getElementById("SubirDocs");

        if (inputFileCont) {
            inputFileCont.addEventListener("dragover", (e) => {
                e.preventDefault();
            });

            inputFileCont.addEventListener("dragenter", (e) => {
                e.preventDefault();
                inputFileCont.classList.add("drag-active");
            });

            inputFileCont.addEventListener("dragleave", (e) => {
                e.preventDefault();
                inputFileCont.classList.remove("drag-active");
            });

            inputFileCont.addEventListener("drop", (e) => {
                e.preventDefault();
                inputFileCont.classList.remove("drag-active");
                fileInput.files = e.dataTransfer.files;
            });
        }
    });

</script>