<?php
    function generatePDFCustomFilename($html, $orientation){
        require_once 'dompdf/autoload.inc.php';
        
        $options = new Dompdf\Options();
        $options->isHtml5ParserEnabled();
        $options->isPhpEnabled();
        $options->isRemoteEnabled();
        $options->setChroot('');

        $dompdf = new Dompdf\Dompdf();
        $dompdf->setOptions($options);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', $orientation);
        $dompdf->render();
        ob_end_clean();
        // Output the generated PDF (1 = download and 0 = preview)
        //$dompdf->stream("REGISTRO ENTRADAS.pdf", array("Attachment" => 0));
        $dompdf->stream();
    }

    function generatePDFPrueba(){
        require_once 'dompdf\autoload.inc.php';
        $dompdf = new Dompdf\Dompdf();
        $html = "<h1>Prueba de documento PDF</h1>";
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $output = $dompdf->output();
        echo base64_encode($output);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['html']) && isset($_POST['orientation'])) {
            generatePDFCustomFilename($_POST['html'], $_POST['orientation']);
        } else {
            //echo "Parámetros faltantes para generar el PDF";
        }
    } else {
        header('Location: index');
    }
?>