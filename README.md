# Documentación
El sistema sigue un patrón de Microservicios orientado a la nube, utilizando Flask como framework de desarrollo para los servicios individuales y Firebase (Firestore/Authentication) como persistencia de datos.

API Gateway: Actúa como el punto de entrada único para las solicitudes del cliente, gestionando el enrutamiento hacia los microservicios internos (Usuarios, Productos, Ventas).

Servicio de Usuarios: Gestiona la autenticación y perfiles de usuario.

Servicio de Productos: Administra el inventario y catálogo.

Servicio de Ventas: Procesa las transacciones y el historial de compras.

#rutas desde api.php
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout'])->middleware('jwt.auth');

Route::middleware(['jwt.auth'])->group(function () {
    Route::post('/ventas', [VentaController::class, 'registrarVenta']);
    Route::get('/productos', [ProductoController::class, 'consultarProductos']);
    Route::post('/productos', [ProductoController::class, 'crearProducto']);
});

#Flujo de registro de una venta
3. Flujo de Registro de una Venta
El proceso de registro de una venta sigue una secuencia lógica para asegurar la integridad de los datos:

Validación de Sesión: El Gateway verifica el token del usuario.

Verificación de Stock: El Servicio de Ventas consulta al Servicio de Productos si existe disponibilidad del ítem solicitado.

Creación de Transacción: Se genera un documento en la colección sales de Firebase con el timestamp, user_id y el arreglo de items.

Actualización de Inventario: Se descuentan las unidades vendidas del stock de productos.

Confirmación: El sistema (Thunder Client) retorna un código 201 Created con el ID de la transacción.
