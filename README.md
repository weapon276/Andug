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
- **Módulo:** Registro de Usuarios
  - **Descripción:**
    - Se corrigió un error en la inserción de nuevos usuarios en la base de datos que causaba un fallo por no reconocer la columna `vCorreo`. Ahora se ha asegurado que todos los campos necesarios se manejen correctamente en el proceso de registro.
    - Se añadió una validación para que las notificaciones solo sean visibles para el tipo de usuario "Recursos Humanos" con `id_TypeUser = 3`. Esto incluye la creación de una columna `ID_TypeUser` en la tabla `mensajes` para gestionar esta funcionalidad.

- **Módulo:** Notificaciones
  - **Descripción:**
    - Se implementó un mecanismo que permite que las notificaciones sobre nuevos registros de usuarios sean vistas únicamente por los usuarios del tipo "Recursos Humanos" (`id_TypeUser = 3`). Esta funcionalidad fue agregada para mejorar la seguridad y relevancia de las notificaciones en el sistema.

- **Módulo:** Base de Datos
  - **Descripción:**
    - Se añadió la columna `ID_TypeUser` en la tabla `mensajes` para relacionar los mensajes con tipos de usuarios específicos. Esto permite que ciertos mensajes sean visibles solo para los usuarios correspondientes.
    - Se revisaron y optimizaron los índices en las tablas `mensajes` y `usuarios` para mejorar la velocidad de consulta y asegurar la integridad referencial.

### 2024-08-22
- **Módulo:** Facturación
  - **Descripción:**
    - Se corrigió un error en la función que genera mensajes para la base de datos, específicamente en la línea de código que accede al nombre del cliente.
    - Se ajustó el manejo de errores y se resolvió el problema de intento de acceso a un índice nulo en el array.
    - Se actualizó el proceso de inserción de facturas para incluir la lógica correcta de descuento y actualización de crédito.
    - Se solucionaron errores en la creación de mensajes para la tabla `mensajes`, asegurando que se manejen correctamente los datos y se prevengan fallos de integridad referencial.
    - Se mejoró el archivo de configuración para manejar correctamente la base de datos y las actualizaciones de datos.

- **Módulo:** Diseño de Factura
  - **Descripción:**
    - Se mejoró el diseño del PDF de factura, incluyendo la incorporación del logo de la empresa y una estructura más profesional para la presentación de la información.

### 2024-08-26
- **Módulo:** Cotizaciones
  - **Descripción:**
    - Se creó el archivo `rutas.php` para consultar la API del INEGI de México y obtener rutas, costos de combustible y distancias entre puntos. Además, se implementó la funcionalidad para almacenar estas rutas en la base de datos.
    - Se corrigió un problema en la función que calculaba el costo total de una ruta, ajustando la fórmula para considerar correctamente todos los parámetros relevantes.
    - Se mejoró el manejo de errores en la interacción con la API del INEGI, asegurando que el sistema responda adecuadamente ante fallos de red o datos incompletos.

- **Módulo:** Diseño de Interface
  - **Descripción:**
    - Se actualizó el diseño del formulario de inicio de sesión, incorporando los colores especificados (#e03c12, #285de2, #ee7755, #f4c0b2, #323232) y añadiendo una imagen de fondo difuminada para mejorar la experiencia del usuario.
    - Se añadieron iconos a los botones de acciones en la tabla (Pago, Prórroga, Cancelar, Descargar PDF) y se configuró para que muestren descripciones al pasar el cursor sobre ellos. Además, todos los botones fueron uniformizados en tamaño utilizando Bootstrap y W3Schools.

- **Módulo:** Base de Datos
  - **Descripción:**
    - Se implementaron vistas y `JOIN` logs para las tablas, permitiendo un mejor rastreo y auditoría de las modificaciones realizadas por los usuarios.
    - Se optimizó el almacenamiento y registro de movimientos en el `log_movimientos`, asegurando que se registren todos los movimientos realizados por cualquier usuario y que se almacene el ID del usuario que realiza bajas de clientes.

- **Módulo:** Facturación
  - **Descripción:**
    - Se mejoró la estructura del PDF de la factura, incorporando el logo de la empresa y refinando el diseño general para un aspecto más profesional y claro.
    - Se implementó la funcionalidad para que el modal de información del cliente liste todas las facturas asociadas, permitiendo la descarga de cada factura de manera sencilla.

### 2024-08-27
- **Módulo:** Registro de Empleados
  - **Descripción:**
    - Se corrigió la función `registrarEmpleado()` para aceptar el número correcto de argumentos y se actualizó el manejo de errores en la inserción de datos de empleados.
    - Se revisó y ajustó el manejo de parámetros en el archivo `registrar_empleado.php` para asegurar que se pasen los argumentos correctos a la función.

### 2024-08-28
- **Módulo:** Registro de Usuarios
  - **Descripción:**
    - Se solucionó el error relacionado con el número de parámetros en la función `registrarUsuario()` y se corrigió la declaración SQL para la inserción de datos de usuario.
    - Se actualizó el archivo `registrar_empleado.php` para asegurar que la función `registrarEmpleado()` se llame con los parámetros correctos.

    ### 2024-08-29
#### Módulo: Gestión de Camiones
- **Funcionalidades añadidas:**
  - Creación de `gestion_camiones.php` con opciones de modificación, suspensión, activación, baja y mantenimiento de camiones.
  - Implementación de sistema de comentarios para registrar razones al suspender o dar de baja camiones.
  - Modal para agregar nuevos camiones y tablas para mostrar camiones activos, suspendidos o dados de baja.
  - Optimización de mensajes de éxito o error al realizar acciones.

  ### 2024-08-30
#### Módulo: Autenticación de Usuarios
- **Problemas resueltos:**
  - **Problema:** La función `obtenerNombreEmpleado` tenía un error en la consulta SQL, lo cual impedía obtener correctamente el nombre del empleado.
  - **Corrección:** Se corrigió la consulta SQL eliminando el uso incorrecto de la coma y ajustando los parámetros para asegurar la correcta obtención del nombre completo del empleado a partir de su `ID_Usuario`. 

#### Módulo: Interfaz de Usuario
- **Problemas resueltos:**
  - **Problema:** La interfaz de usuario no mostraba el nombre del empleado que inició sesión.
  - **Corrección:** Se actualizó la interfaz para que muestre correctamente el nombre del empleado al iniciar sesión, utilizando la función corregida `obtenerNombreEmpleado`.

### Fecha: 30 de Agosto de 2024

#### Módulo Trabajado: Inserción de Viajes en la Base de Datos

**Problemas:**
 **Inconsistencia en la estructura del INSERT:** 
   - Se detectaron discrepancias entre la sentencia SQL `INSERT INTO` y la estructura real de la tabla `viaje` en la base de datos. Las columnas especificadas en la sentencia de inserción no coincidían con las columnas definidas en la tabla, lo que provocó errores al intentar ejecutar la inserción.

 **Columnas faltantes en la sentencia SQL:** 
   - Algunas columnas requeridas por la tabla `viaje` no estaban siendo consideradas en la sentencia de inserción, como `ID_Camion`, `ID_Operador`, `Fecha_Despacho`, `Gastos`, entre otras.

 **Incompatibilidad de tipos de datos:** 
   - Se encontraron problemas con la correspondencia de tipos de datos entre los valores a insertar y los tipos de columnas en la tabla, lo que podría haber causado errores de ejecución y problemas de integridad de datos.

**Soluciones:**
 **Revisión y corrección de la sentencia `INSERT INTO`:** 
   - Se ajustó la sentencia SQL de inserción para que coincida con la estructura de la tabla `viaje`. Se actualizaron los nombres de las columnas y se añadieron todas las columnas requeridas para evitar errores de ejecución.

 **Vinculación de parámetros corregida:** 
   - Se aseguró que todos los parámetros vinculados en la sentencia `INSERT INTO` tengan valores correspondientes y estén correctamente definidos antes de ejecutar la consulta, lo que garantiza la integridad de los datos insertados.

 **Validación de tipos de datos:** 
   - Se revisaron y validaron los tipos de datos para asegurar que sean compatibles con los definidos en la base de datos, previniendo errores de tipo de datos y asegurando que los datos se almacenen correctamente.

**Otros Cambios Realizados:**
- Se actualizaron las descripciones y comentarios en el código para mayor claridad y mantenimiento futuro.
- Se probó la funcionalidad de inserción de viajes para asegurar que los datos se almacenan correctamente en la base de datos.
- Se realizaron pruebas de validación para asegurar que la funcionalidad respeta las restricciones de la base de datos y se ejecuta sin errores.

### 2024-09-02

#### Módulos Trabajados
- **Revisión de la estructura de la base de datos**: Asegurarse de que todas las tablas necesarias para el proyecto estén correctamente definidas.

#### Actualizaciones
- Verificación y ajuste de las relaciones entre las tablas para garantizar la integridad referencial y el correcto funcionamiento de las consultas.

#### Problemas Enfrentados
- **Errores de sintaxis SQL**: Ajustes menores en las declaraciones SQL para asegurar la compatibilidad con la estructura de la base de datos.

### 2024-09-03

#### Módulos Trabajados
- **`viaje.php`**: Implementación de la gestión de viajes con las siguientes características:
  - **Visualización** de todos los viajes registrados en la base de datos.
  - **Modificación** del estado del viaje con botones para "Modificar", "Completado", "Cancelado" y "Suspender".
  - **Modal de comentarios** para cuando se selecciona la opción "Suspender", permitiendo ingresar razones para la suspensión del viaje.
  - **Obtención de información** relacionada de las tablas `rutas`, `cotizacion`, `operador`, y `cliente`.

#### Actualizaciones
- Se añadió un **modal** para la opción "Suspender", permitiendo ingresar comentarios sobre la razón de la suspensión.
- Se corrigió el código para el manejo de datos con PDO, reemplazando el método `fetch_assoc()` por `fetch(PDO::FETCH_ASSOC)`.

#### Problemas Enfrentados
- **Error de método no definido**: El método `fetch_assoc()` no es válido para PDO en PHP. Se resolvió reemplazándolo con `fetch(PDO::FETCH_ASSOC)`.

### 2024-09-04

#### Módulos Trabajados
- **`viaje.php`**: Actualización para la gestión de viajes con las siguientes características:
  - **Visualización** de todos los viajes registrados en la base de datos.
  - **Modificación** del estado del viaje con botones para "Modificar", "Completado", "Cancelado" y "Suspender".
  - **Modal de comentarios** para cuando se selecciona la opción "Suspender", permitiendo ingresar razones para la suspensión del viaje.
  - **Obtención de información** relacionada de las tablas `rutas`, `cotizacion`, `operador`, y `cliente`.
  
#### Actualizaciones
- Se corrigió un **error de método no definido** en el archivo `viaje.php`, reemplazando el método `fetch_assoc()` con `fetch(PDO::FETCH_ASSOC)` para cumplir con la sintaxis de PDO.
- Se ha **mejorado la funcionalidad del modal** para comentarios en la opción "Suspender", permitiendo una mejor interacción con el usuario.

#### Problemas Enfrentados
- **Error de método no definido**: Se enfrentó un problema con el uso del método `fetch_assoc()` en PDO, el cual fue solucionado al actualizar el código con `fetch(PDO::FETCH_ASSOC)`.

### 2024-09-11

#### Módulos Trabajados
**Viaje (viaje.php)**

Problemas Enfrentados:
**Error en la Carga de Rutas:**

Descripción: **Al intentar cargar las rutas para un nuevo viaje**, se presentó un error que impedía la visualización correcta de las rutas disponibles.
Solución: Se actualizó la consulta SQL para asegurar que todas las rutas se carguen correctamente desde la base de datos. También se mejoró la lógica en el backend para manejar posibles excepciones durante la carga.

**Inconsistencia en el Registro de Gastos:**
Algunos gastos asociados a los viajes no se estaban registrando correctamente en la base de datos.
Solución: Se revisó y corrigió el código que gestiona el registro de gastos, asegurando que cada gasto se vincule correctamente con el viaje correspondiente.

**Mejoras/Anexos:**
Interfaz de Usuario Mejorada:
Descripción: Se mejoró el diseño de la interfaz de usuario para la selección de rutas y el registro de gastos, incluyendo una visualización más clara de la información y una navegación más intuitiva.

**Validación de Datos Mejorada:**
Descripción: Se añadieron validaciones adicionales para asegurar que todos los campos necesarios para registrar un viaje estén completos antes de permitir el envío del formulario.

**Optimización de Consultas SQL:**
Descripción: Se optimizaron las consultas SQL utilizadas en viaje.php para mejorar el rendimiento y reducir el tiempo de carga.


## Instalación y Configuración
1. Clona el repositorio: `git clone https://github.com/tu-usuario/gestion-de-flotas-andug.git`
2. Configura la base de datos utilizando el archivo SQL proporcionado en `/database/`.
3. Configura el entorno local (recomendado: Laragon) y asegúrate de tener PHP, MySQL y otras dependencias instaladas.

## Contacto
Para cualquier duda o sugerencia, puedes contactar a través del correo [soportetecnico@techpromx.com].

## Licencia
Este proyecto está licenciado bajo los términos de [MIT License](LICENSE).


## Instalación y Configuración
1. Clona el repositorio: `git clone https://github.com/tu-usuario/gestión-de-flotas-andug.git`
2. Configura la base de datos utilizando el archivo SQL proporcionado en `/database/`.
3. Configura el entorno local (recomendado: Laragon) y asegúrate de tener PHP, MySQL y otras dependencias instaladas.

## Contacto
Para cualquier duda o sugerencia, puedes contactar a través del correo [soportetecnico@techpromx.com].

## Licencia
Este proyecto está licenciado bajo los términos de [MIT License](LICENSE).
