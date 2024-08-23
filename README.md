# Gestión de Flotas ANDUG

## Descripción
Este proyecto es una aplicación web para la gestión de flotas de camiones, seguimiento de rutas, control de gastos, y otras funciones relacionadas con la logística y el transporte.

## Actualizaciones del Proyecto

### 2024-08-20
- **Módulo:** Cotizaciones
  - **Descripción:** 
    - Se implementó la funcionalidad de actualizar el estado del camión a "Ocupado" al seleccionar un camión en una cotización.
    - Se corrigió un error relacionado con la columna `Tipo_Camiones` en la base de datos, que arrojaba un valor `NULL` no permitido.
    - Se añadieron validaciones para asegurar que `Tipo_Camiones` tenga un valor válido antes de realizar la inserción en la base de datos.
    - Se solucionó un error en la función `obtenerCamionesLibres` al pasar el argumento de conexión a la base de datos.
    - Se resolvió el error de la función `obtenerCamionesLibres()` que no recibía el parámetro necesario en el archivo `cotizacion.php`.
    - Se corrigió la lógica para manejar el array de camiones libres al insertar en la tabla `cotizacion_camion`.
    - Se añadió la opción de notificar a usuarios con tipo "administrador" y "contabilidad" sobre nuevas cotizaciones.
    - Se mejoró la funcionalidad de manejo de rutas y detalles de la cotización utilizando datos en formato JSON.

- **Módulo:** Gestión de Camiones
  - **Descripción:** 
    - Se creó la tabla `camion` en la base de datos con campos como `Placas`, `Peso`, `Unidad`, `Status`, y otros detalles específicos del camión.
    - Se añadieron triggers para actualizar automáticamente las fechas `fecha_inicio` y `fecha_final` en la tabla de camiones.

### 2024-08-21
### **Módulo de Registro de Usuarios**
  - **Corrección en la inserción de usuarios:** Se corrigió un error en la inserción de nuevos usuarios en la base de datos que causaba un fallo por no reconocer la columna `vCorreo`. Ahora se ha asegurado que todos los campos necesarios se manejen correctamente en el proceso de registro.
  - **Validación de tipo de usuario:** Se añadió una validación para que las notificaciones solo sean visibles para el tipo de usuario "Recursos Humanos" con `id_TypeUser = 3`. Esto incluye la creación de una columna `ID_TypeUser` en la tabla `mensajes` para gestionar esta funcionalidad.

### **Módulo de Notificaciones**
- **Filtrado de notificaciones por tipo de usuario:** Se implementó un mecanismo que permite que las notificaciones sobre nuevos registros de usuarios sean vistas únicamente por los usuarios del tipo "Recursos Humanos" (`id_TypeUser = 3`). Esta funcionalidad fue agregada para mejorar la seguridad y relevancia de las notificaciones en el sistema.

### **Base de Datos**
- **Ajuste en la tabla `mensajes`:** Se añadió la columna `ID_TypeUser` en la tabla `mensajes` para relacionar los mensajes con tipos de usuarios específicos. Esto permite que ciertos mensajes sean visibles solo para los usuarios correspondientes.
- **Optimización de índices:** Se revisaron y optimizaron los índices en las tablas `mensajes` y `usuarios` para mejorar la velocidad de consulta y asegurar la integridad referencial.

### 2024-08-22
- **Módulo de Facturación**
  - **Corrección en el código:**
    - Se corrigió un error en la función que genera mensajes para la base de datos, específicamente en la línea de código que accede al nombre del cliente.
    - Se ajustó el manejo de errores y se resolvió el problema de intento de acceso a un índice nulo en el array.
    - Se actualizó el proceso de inserción de facturas para incluir la lógica correcta de descuento y actualización de crédito.
    - Se solucionaron errores en la creación de mensajes para la tabla `mensajes`, asegurando que se manejen correctamente los datos y se prevengan fallos de integridad referencial.
    - Se mejoró el archivo de configuración para manejar correctamente la base de datos y las actualizaciones de datos.

### 2024-08-22
- **Módulo de Diseño de Factura**
  - **Mejora del diseño del PDF de factura:** Se mejoró el diseño del PDF para las facturas, incluyendo la incorporación del logo de la empresa y una estructura más profesional para la presentación de la información.

## Instalación y Configuración
1. Clona el repositorio: `git clone https://github.com/tu-usuario/gestión-de-flotas-andug.git`
2. Configura la base de datos utilizando el archivo SQL proporcionado en `/database/`.
3. Configura el entorno local (recomendado: Laragon) y asegúrate de tener PHP, MySQL y otras dependencias instaladas.

## Contacto
Para cualquier duda o sugerencia, puedes contactar a través del correo [soportetecnico@techpromx.com].

## Licencia
Este proyecto está licenciado bajo los términos de [MIT License](LICENSE).
