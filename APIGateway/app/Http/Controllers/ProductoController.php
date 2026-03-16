<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function consultarProductos()
    {
        // Llama al microservicio Flask (ejemplo: http://localhost:5000/productos)
        $response = Http::get('http://localhost:5000/productos');

        // Retorna la respuesta del microservicio al cliente
        return response()->json($response->json(), $response->status());
    }

    public function crearProducto(Request $request)
    {
        $response = Http::post('http://localhost:5000/productos', $request->all());

        return response()->json($response->json(), $response->status());
    }
}
