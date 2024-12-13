<?php

// Claves de sustitución (las mismas que en el cifrado)
$clave1 = 'QWERTYUIOPASDFGHJKLZXCVBNM';
$clave2 = 'MNBVCXZLKJHGFDSAPOIUYTREWQ';
$clave3 = 'ZAYXWVUTSRQPONMLKJIHGFEDCB';

// Función para descifrar el mensaje
function descifrarMensaje($mensaje, $clave1, $clave2, $clave3) {
    $alfabeto = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $frecuencia = [];
    $descifrado = '';

    // Convertir a mayúsculas y eliminar espacios
    $mensaje = strtoupper($mensaje);

    // Recorrer cada letra del mensaje cifrado
    for ($i = 0; $i < strlen($mensaje); $i++) {
        $letra = $mensaje[$i];

        // Solo desciframos si es una letra
        if (ctype_alpha($letra)) {
            // Incrementar la frecuencia de la letra
            if (!isset($frecuencia[$letra])) {
                $frecuencia[$letra] = 0;
            }
            $frecuencia[$letra]++;

            // Seleccionar la clave adecuada según la frecuencia
            if ($frecuencia[$letra] == 1) {
                $posicion = strpos($clave1, $letra);
                $descifrado .= $alfabeto[$posicion];
            } elseif ($frecuencia[$letra] == 2) {
                $posicion = strpos($clave2, $letra);
                $descifrado .= $alfabeto[$posicion];
            } else {
                $posicion = strpos($clave3, $letra);
                $descifrado .= $alfabeto[$posicion];
            }
        } else {
            // Si no es una letra, se mantiene igual (números, espacios, etc.)
            $descifrado .= $letra;
        }
    }

    return $descifrado;
}

// Verificar si se envió un mensaje
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mensaje'])) {
    $mensaje = $_POST['mensaje'];
    $descifrado = descifrarMensaje($mensaje, $clave1, $clave2, $clave3);

    // Redirigir al formulario con el mensaje descifrado
    header("Location: formulario.php?descifrado=" . urlencode($descifrado));
    exit();
}
?>
