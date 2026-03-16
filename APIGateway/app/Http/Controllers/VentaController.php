<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Tymon\JWTAuth\Facades\JWTAuth;

class VentaController extends Controller
{
    public function registrarVenta(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|string',
            'cantidad' => 'required|integer|min:1',
        ]);

        // 1. Verificar stock en Flask
        $productoId = $request->input('producto_id');
        $cantidad = (int) $request->input('cantidad');

        $stockResponse = Http::get("http://localhost:5000/productos/$productoId/stock");

        if ($stockResponse->status() === 404) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }

        if (! $stockResponse->successful()) {
            return response()->json(['error' => 'Error al consultar stock'], 502);
        }

        $stock = (int) ($stockResponse->json('stock', 0));

        if ($stock < $cantidad) {
            return response()->json(['error' => 'Stock insuficiente'], 400);
        }

        // 2. Registrar la venta en Express
        $ventaData = [
            'producto_id' => $productoId,
            'cantidad' => $cantidad,
            'usuario_id' => auth()->user()->id,
            'fecha' => now(),
        ];

        $ventaResponse = Http::post('http://localhost:3000/ventas', $ventaData);

        if (! $ventaResponse->successful()) {
            return response()->json(['error' => 'Error al registrar venta'], 500);
        }

        // 3. Actualizar inventario en Flask
        Http::put("http://localhost:5000/productos/$productoId/inventario", [
            'cantidad' => $cantidad,
        ]);

        // 4. Retornar respuesta al cliente
        return response()->json(['message' => 'Venta registrada correctamente']);
    }
}

