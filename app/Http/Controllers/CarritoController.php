<?php

namespace App\Http\Controllers;

use App\Models\Carrito;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarritoController extends Controller
{
    public function index($categoria)
    {
        $user = Auth::user();
        $carrito = Carrito::where('usuarios_id', $user->id)
                          ->where('categoria', $categoria)
                          ->with('producto')
                          ->get();

        return response()->json($carrito);
    }

    public function store(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        $producto = Producto::find($request->producto_id);

        // Verificar si el producto ya está en el carrito
        $carritoItem = Carrito::where('usuarios_id', $user->id)
                              ->where('producto_id', $request->producto_id)
                              ->first();

        if ($carritoItem) {
            $carritoItem->cantidad += $request->cantidad;
            $carritoItem->save();
        } else {
            $carritoItem = new Carrito();
            $carritoItem->usuarios_id = $user->id;  // Cambiado aquí a usuarios_id
            $carritoItem->producto_id = $request->producto_id;
            $carritoItem->cantidad = $request->cantidad;
            $carritoItem->categoria = $producto->categoria;
            $carritoItem->save();
        }

        return response()->json(['success' => 'Producto agregado al carrito correctamente.']);
    }

    public function destroy($id)
    {
        $carritoItem = Carrito::findOrFail($id);
        $carritoItem->delete();

        return response()->json(['success' => 'Producto eliminado del carrito correctamente.']);
    }
}
