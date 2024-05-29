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
        <!-- Ventana de respuesta con objeto normal -->
        <div id="ResponseDoc">
            <div class="ResponseTitle">
                <h2 style="color: var(--Background);">Registro generado exitosamente</h2>
                <button id="ResponseDocClose" class="ResponseDocCloseCustom" onclick="CloseResponse()">
                    <i class="bi bi-x-circle"></i>
                </button>
            </div>
            <object class="ResponseObject" data="" download=""></object>
        </div>
        <!-- Ventana de información -->
        <div id="WaitDoc">
            <div class="ResponseTitle">
                <h2 style="color: var(--Background);">Generando registro...</h2>
                <button id="ResponseDocClose" class="WaitResponseDocClose" onclick="">
                    <i class="bi bi-x-circle"></i>
                </button>
            </div>
            <h3 class="WaitDocText">Por favor espere un momento</h3>
        </div>
        <!-- Ventana de subida de documentos -->
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
            <button class="ResponseDocUploadButton" type="submit" onclick="subirDocumentos()">
                <i class="bi bi-cloud-upload"></i>
                <span>Subir</span>
            </button>
        </div>
        <!-- Ventana de respuesta con objeto editable -->
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
        <!-- Ventana de respuesta con campo para nota de cancelacion -->
        <div id="ResponseCancel">
            <div class="ResponseTitle">
                <h2 style="color: var(--Background);">Cancelar registro</h2>
                <button id="ResponseDocClose" class="CancelResponseDocClose" onclick="">
                    <i class="bi bi-x-circle"></i>
                </button>
            </div>
            <h3 id="ResponseCancelQuestion">¿Estás seguro de que deseas cancelar este registro?</h3>
            <h4 id="ResponseCancelWarning">⚠️ Una vez cancelado, no se podrá reactivar ⚠️</h4>
            <textarea id="ResponseCancelTextarea" class="ResponseCancelNote" placeholder="Escribe una nota de cancelación (max. 500 carácteres)" maxlength="500"></textarea>
            <button class="ResponseCancelButton" onclick="">
                <i class="bi bi-x-octagon"></i>
                <span>Cancelar registro</span>
            </button>
            <button class="ResponseVerifyButton" onclick="">
                <i class="bi bi-check-circle"></i>
                <span>Verificar registro</span>
            </button>
        </div>
    </div>
</body>

</html>
<script>
    function CloseResponse() {
        data = null;
        blob = null;
        $('#ResponseDocCont').css('display', 'none');
        $('#ResponseDoc').css('display', 'none');
        $('#WaitDoc').css('display', 'none');
        $('#UploadDoc').css('display', 'none');
        $('#ResponseDocEditable').css('display', 'none');
        $('#ResponseCancel').css('display', 'none');
    }

    function WaitDoc(title, message, closeFunction) {
        CloseResponse();
        $('#ResponseDocCont').css('display', 'flex');
        $('#WaitDoc').css('display', 'flex');
        $('.ResponseTitle h2').text(title);
        $('#WaitDoc .WaitDocText').text(message);
        if(closeFunction == "CloseResponse()" || closeFunction == "location.reload()"){
            $('.WaitResponseDocClose').attr('onclick', closeFunction);
        } else {
            $('.WaitResponseDocClose').off('click').on('click', closeFunction);
        }
    }

    function ResponseDoc(title, objectData, downloadName, closeFunction) {
        CloseResponse();
        $('#ResponseDocCont').css('display', 'flex');
        $('#ResponseDoc').css('display', 'flex');
        $('.ResponseTitle h2').text(title);
        $('.ResponseObject').attr('data', objectData);
        $('.ResponseObject').attr('download', downloadName);
        $('#ResponseObjectFail').attr('href', objectData);
        $('#ResponseObjectFail').attr('download', downloadName);
        $('#ResponseDocClose').attr('onclick', closeFunction);
    }

    function UploadDoc(title, folio, accion) {
        CloseResponse();
        $('#ResponseDocCont').css('display', 'flex');
        $('#UploadDoc').css('display', 'flex');
        $('.ResponseTitle h2').text(title);
        $('#folio').val(folio);
        if (accion == "Entradas") { 
            $('.ResponseDocUploadButton').attr('onclick', 'subirDocumentos("DocsEntradas/", "ENTRADAS_' + folio + '.pdf")');
        } else if (accion == "Salidas") {
            $('.ResponseDocUploadButton').attr('onclick', 'subirDocumentos("DocsSalidas/", "SALIDAS_' + folio + '.pdf")');
        } else if (accion == "SalidasCoord") {
            $('.ResponseDocUploadButton').attr('onclick', 'subirDocumentos("DocsSalidasCoord/", "SALIDAS_COORDINADOR_' + folio + '.pdf")');
        }
    }

    function ResponseDocEditable(title,objectData, closeFunction, replaceFunction) {
        CloseResponse();
        $('#ResponseDocCont').css('display', 'flex');
        $('#ResponseDocEditable').css('display', 'flex');
        $('.ResponseTitle h2').text(title);
        $('.ResponseObject').attr('data', objectData);
        $('#ResponseDocEditable .ResponseDocCloseCustom').attr('onclick', closeFunction);
        $('#ResponseDocEditable .ResponseDocReplaceButton').attr('onclick', replaceFunction);
    }

    function ResponseCancel(title, accion, tipo, folio, closeFunction, element){
        CloseResponse();
        if(accion=="Verificar"){
            $('#ResponseCancelQuestion').text('¿Estás seguro de que deseas verificar este registro?');
            $('#ResponseCancelWarning').text('⚠️ Una vez verificado, no se podrá deshacer ⚠️');
            $('#ResponseCancelTextarea').css('display', 'none');
            $('.ResponseCancelButton').css('display', 'none');
            $('.ResponseVerifyButton').css('display', 'flex');
        } else if(accion=="Cancelar"){
            $('#ResponseCancelQuestion').text('¿Estás seguro de que deseas cancelar este registro?');
            $('#ResponseCancelWarning').text('⚠️ Una vez cancelado, no se podrá reactivar ⚠️');
            $('#ResponseCancelTextarea').css('display', 'block');
            $('.ResponseCancelButton').css('display', 'flex');
            $('.ResponseVerifyButton').css('display', 'none');

        }
        $('#ResponseDocCont').css('display', 'flex');
        $('#ResponseCancel').css('display', 'flex');
        $('.ResponseTitle h2').text(title);
        $('#ResponseCancel .ResponseCancelButton').off('click').on('click', function() {accionesRegistros(accion, tipo, folio, element);});
        $('#ResponseCancel .ResponseVerifyButton').off('click').on('click', function() {accionesRegistros(accion, tipo, folio, element);});
        $('.CancelResponseDocClose').off('click').on('click', closeFunction);
    }
    function uncheckSlider(element) {
        CloseResponse();
        element.checked = false;
        $('.ResponseCancelNote').val('');
    }
    

    function generarEntradasyPDF(datoAEnviar, orientacion, toDownload) {
        WaitDoc("Generando registro...", "Por favor espere un momento", "CloseResponse()");
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
                        WaitDoc("Error al generar el archivo PDF", "Por favor intente de nuevo", "CloseResponse()");
                    }
                });

            },
            error: function (xhr, status, error) {
                // Maneja los errores aquí
                console.error(xhr, status, error);
                WaitDoc("Error al generar el registro", "Por favor intente de nuevo", "CloseResponse()");
            }
        });
        data = null;
        blob = null;
    }

    function consultarPDFEntradas(datoAEnviar, orientacion, toDownload) {
        WaitDoc("Generando registro...", "Por favor espere un momento", "CloseResponse()");
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
                        WaitDoc("Error al generar el archivo PDF", "Por favor intente de nuevo", "CloseResponse()");
                    }
                });

            },
            error: function (xhr, status, error) {
                // Maneja los errores aquí
                console.error(xhr, status, error);
                WaitDoc("Error al generar el registro", "Por favor intente de nuevo", "CloseResponse()");
            }
        });
        data = null;
        blob = null;
    }

    function subirDocumentos(targetDirectory, nombrePersonalizado) {
        WaitDoc("Subiendo tus documentos...", "Por favor espere un momento...", "CloseResponse()");
        
        var folio = document.getElementById("folio").value;
        var docs = document.getElementById("SubirDocs").files[0]; // Acceder al archivo seleccionado
        var targetDirectory = targetDirectory;
        var nombrePersonalizado = nombrePersonalizado;

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
                    WaitDoc("Error al subir los documentos", "Por favor intente de nuevo", "CloseResponse()");
                }
            },
            error: function (xhr, status, error) {
                console.error(xhr, status, error);
                WaitDoc("Error al enviar los documentos al servidor", "Por favor intente de nuevo", "CloseResponse()");
            }
        });
        data = null;
        blob = null;
    }

    function consultarDoc(folio, tipo, rol, isEditable) {
        $.ajax({
            url: 'enviarEntradas.php',
            type: 'POST',
            data: {
                folioDocs: folio,
                tipo: tipo
            },
            success: function (response) {
                console.log(response);
                console.log(isEditable);
                if (isEditable) {
                    ResponseDocEditable("Documentos subidos", response, 'CloseResponse()', 'UploadDoc("Reemplaza tus documentos", ' + folio + ')');
                } else {
                    ResponseDoc("Documentos subidos", response, 'ARCHIVOS ' + folio + '.pdf', 'CloseResponse()');

                }
            },
            error: function (xhr, status, error) {
                console.error(xhr, status, error);
                WaitDoc("Error al consultar los documentos", "Por favor intente de nuevo", "CloseResponse()");
            }
        });
        data = null;
        blob = null;
    }

    function generarSalidasyPDF(datoAEnviar, orientacion, toDownload) {
        WaitDoc("Generando registro...", "Por favor espere un momento", "CloseResponse()");
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
                        WaitDoc("Error al generar el archivo PDF", "Por favor intente de nuevo", "CloseResponse()");
                    }
                });

            },
            error: function (xhr, status, error) {
                // Maneja los errores aquí
                console.error(xhr, status, error);
                WaitDoc("Error al generar el registro", "Por favor intente de nuevo", "CloseResponse()");
            }
        });
        data = null;
        blob = null;
    }

    function consultarPDFSalidas(datoAEnviar, orientacion, toDownload) {
        WaitDoc("Generando registro...", "Por favor espere un momento", "CloseResponse()");
        // Enviar los datos a enviarEntradas.php para darle formato HTML-->>PDF y/o guardar en BD
        $.ajax({
            url: 'enviarSalidas.php',
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
                        WaitDoc("Error al generar el archivo PDF", "Por favor intente de nuevo", "CloseResponse()");
                    }
                });

            },
            error: function (xhr, status, error) {
                // Maneja los errores aquí
                console.error(xhr, status, error);
                WaitDoc("Error al generar el registro", "Por favor intente de nuevo", "CloseResponse()");
            }
        });
        data = null;
        blob = null;
    }

    function accionesRegistros(accion, tipo, folio, element){
        var nota = $('#ResponseCancel .ResponseCancelNote').val();

        $.ajax({
            url: 'accionesRegistros.php',
            type: 'POST',
            data: {
                folio: folio,
                tipo: tipo,
                nota: nota,
                accion: accion
            },
            success: function (response) {
                console.log(response);
                if(response == "Success" && accion == "Cancelar"){
                    WaitDoc("Registro " + folio + " cancelado exitosamente", "La solicitud de cancelación ha sido procesada con éxito.", "location.reload()");
                    element.disabled = true;
                } else if(response != "Success" && accion == "Cancelar"){
                    console.log(response + "\n Por favor");
                    WaitDoc("Error al cancelar el registro", response + "\n Por favor intente de nuevo", function() {uncheckSlider(element);});
                }else if(response == "Success" && accion == "Verificar"){
                    WaitDoc("Registro " + folio + " verificado exitosamente", "La solicitud de verificación ha sido procesada con éxito.", "location.reload()");
                    element.disabled = true;
                } else {
                    console.log(response);
                    WaitDoc("Error al verificar el registro", "Por favor intente de nuevo", function() {uncheckSlider(element);});
                }
            },
            error: function (xhr, status, error) {
                console.error(xhr, status, error);
                WaitDoc("Error al cancelar el registro", "Por favor intente de nuevo", "CloseResponse()");
            }
        });
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