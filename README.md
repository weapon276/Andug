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

## Instalación y Configuración
1. Clona el repositorio: `git clone https://github.com/tu-usuario/gestión-de-flotas-andug.git`
2. Configura la base de datos utilizando el archivo SQL proporcionado en `/database/`.
3. Configura el entorno local (recomendado: Laragon) y asegúrate de tener PHP, MySQL y otras dependencias instaladas.

## Contacto
Para cualquier duda o sugerencia, puedes contactar a través del correo [soportetecnico@techpromx.com].

## Licencia
Este proyecto está licenciado bajo los términos de [MIT License](LICENSE).
