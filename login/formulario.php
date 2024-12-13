<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cifrado y Descifrado Simétrico</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            width: 400px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        textarea {
            width: 100%;
            height: 100px;
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 10px;
        }
        button:hover {
            background-color: #218838;
        }
        .result {
            margin-top: 20px;
            padding: 10px;
            background-color: #f1f1f1;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Cifrado y Descifrado</h2>
    
    <!-- Formulario para cifrar el mensaje -->
    <form action="cifrado.php" method="POST">
        <label for="mensaje">Ingresa el mensaje a codificar:</label>
        <textarea name="mensaje" id="mensaje" required></textarea>
        <button type="submit">Codificar</button>
    </form>

    <!-- Formulario para descifrar el mensaje -->
    <form action="descifrado.php" method="POST">
        <label for="mensaje">Ingresa el mensaje a decodificar:</label>
        <textarea name="mensaje" id="mensaje" required></textarea>
        <button type="submit">Decodificar</button>
    </form>
</div>

<script>
// Función para mostrar alertas si el mensaje está presente en la URL
window.onload = function() {
    const params = new URLSearchParams(window.location.search);

    // Verifica si hay un mensaje cifrado o descifrado en los parámetros de la URL
    if (params.has('cifrado')) {
        alert("Mensaje Cifrado: " + params.get('cifrado'));
    }
    if (params.has('descifrado')) {
        alert("Mensaje Decodificado: " + params.get('descifrado'));
    }
};
</script>

</body>
</html>
