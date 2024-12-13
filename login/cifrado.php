<?php

// Claves de sustitución
$clave1 = 'QWERTYUIOPASDFGHJKLZXCVBNM';
$clave2 = 'MNBVCXZLKJHGFDSAPOIUYTREWQ';
$clave3 = 'ZAYXWVUTSRQPONMLKJIHGFEDCB';

// Función para cifrar el mensaje
function cifrarMensaje($mensaje, $clave1, $clave2, $clave3) {
    $alfabeto = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $frecuencia = [];
    $cifrado = '';

    // Convertir a mayúsculas y eliminar espacios
    $mensaje = strtoupper($mensaje);

    // Recorrer cada letra del mensaje
    for ($i = 0; $i < strlen($mensaje); $i++) {
        $letra = $mensaje[$i];

        // Solo ciframos si es una letra
        if (ctype_alpha($letra)) {
            // Incrementar la frecuencia de la letra
            if (!isset($frecuencia[$letra])) {
                $frecuencia[$letra] = 0;
            }
            $frecuencia[$letra]++;

            // Seleccionar la clave adecuada según la frecuencia
            if ($frecuencia[$letra] == 1) {
                $posicion = strpos($alfabeto, $letra);
                $cifrado .= $clave1[$posicion];
            } elseif ($frecuencia[$letra] == 2) {
                $posicion = strpos($alfabeto, $letra);
                $cifrado .= $clave2[$posicion];
            } else {
                $posicion = strpos($alfabeto, $letra);
                $cifrado .= $clave3[$posicion];
            }
        } else {
            // Si no es una letra, se mantiene igual (números, espacios, etc.)
            $cifrado .= $letra;
        }
    }

    return $cifrado;
}

// Verificar si se envió un mensaje
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mensaje'])) {
    $mensaje = $_POST['mensaje'];
    $cifrado = cifrarMensaje($mensaje, $clave1, $clave2, $clave3);

    // Redirigir al formulario con el mensaje cifrado
    header("Location: formulario.php?cifrado=" . urlencode($cifrado));
    exit();
}
?>
