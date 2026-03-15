const express = require('express');
const mongoose = require('mongoose');

const app = express();
app.use(express.json());

// Cambia la URI según tu configuración
const mongoURI = 'mongodb://localhost:27017';

mongoose.connect(mongoURI)
  .then(() => console.log('Conexión exitosa a MongoDB'))
  .catch(err => console.error('Error de conexión:', err));

// Define una ruta de prueba
app.get('/', (req, res) => {
  res.send('¡Microservicio conectado a MongoDB!');
});

// Esquema y modelo de Venta
const ventaSchema = new mongoose.Schema({
    producto_id: String,
    cantidad: Number,
    usuario_id: String,
    fecha: Date
});
const Venta = mongoose.model('Venta', ventaSchema);

// Endpoint para registrar venta
app.post('/ventas', async (req, res) => {
    console.log('Datos recibidos:', req.body); // <-- Esto imprime los datos en la terminal
    const venta = new Venta(req.body);
    await venta.save();
    res.status(201).json({ message: 'Venta registrada', venta });
});

// Endpoint para consultar ventas
app.get('/ventas', async (req, res) => {
    const ventas = await Venta.find();
    res.json(ventas);
});

// Inicia el servidor
app.listen(3000, () => {
  console.log('Servidor Express escuchando en el puerto 3000');
});

app.get('/ventas/usuario/:usuario_id', async (req, res) => {
    const ventas = await Venta.find({ usuario_id: req.params.usuario_id });
    res.json(ventas);
});

app.get('/ventas/fecha/:fecha', async (req, res) => {
    const ventas = await Venta.find({ fecha: req.params.fecha });
    res.json(ventas);
});