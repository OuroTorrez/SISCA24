<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Google fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700;800&family=Asap+Condensed:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<!-- Styles -->
<link rel="stylesheet" href="Styles/generalStyles.css">
<link rel="stylesheet" href="../bootstrap-icons-1.11.3/font/bootstrap-icons.css">
<!-- Scripts -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<!-- Data tables -->
<script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
<script>
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            // Si el foco está en un input que no debería enviar el formulario
            const target = event.target;
            const isInput = target.tagName === 'INPUT' || target.tagName === 'TEXTAREA';

            if (isInput) {
                event.preventDefault();
                return false;
            }
        }
    });

</script>