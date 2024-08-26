<?php
include 'conexion.php';
include 'index.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Rutas</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="stylesheet" type="text/css" href="CSS/CSS.css">
    <style>
        #map-container {
            width: 100%;
            height: 600px;
            position: relative;
        }
        #map {
            width: 100%;
            height: 100%;
            border: none;
        }
        #info {
            margin-top: 10px;
        }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>
 <!-- Margen de tabla y menu lateral -->
 <div class="w3-main" style="margin-left:320px;margin-top:60px;">
<!--Fin de margen -->
<body>
   

    <div id="map-container">
        <iframe id="map" src="https://gaia.inegi.org.mx/mdm6/?v=bGF0OjIzLjMyMDA4LGxvbjotMTAxLjUwMDAwLHo6MSxsOmMxMTFzZXJ2aWNpb3N8dGMxMTFzZXJ2aWNpb3M="></iframe>
    </div>

    <div id="info"></div>
    
    <script>
        $(document).ready(function() {
            $("#formularioRuta").submit(function(e) {
                e.preventDefault();
                var puntoA = $("#punto_a").val();
                var puntoB = $("#punto_b").val();

                $.post("ruta.php", { punto_a: puntoA, punto_b: puntoB }, function(data) {
                    var resultado = JSON.parse(data);

                    if (resultado.error) {
                        alert("Error: " + resultado.error);
                    } else {
                        // Mostrar información en el div #info
                        $("#info").html(
                            "<strong>Distancia:</strong> " + resultado.distancia + " km<br>" +
                            "<strong>Costo Combustible:</strong> " + resultado.costo_combustible + "<br>" +
                            "<strong>Costo Caseta:</strong> " + resultado.costo_caseta + "<br>" +
                            "<strong>Tiempo:</strong> " + resultado.tiempo_min + " min<br>" +
                            "<strong>Advertencia:</strong> " + resultado.advertencia
                        );

                    }
                });
            });
        });
    </script>
</body>
</html>
