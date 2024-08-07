<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class AdminProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::all();
        return response()->json($productos);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'precio' => 'required|numeric',
            'cantidad' => 'required|integer',
            'categoria' => 'required|string',
            'estado' => 'required|boolean',
            'oferta' => 'nullable|numeric',
            'foto' => 'nullable|image',
        ]);

        $producto = new Producto();
        $producto->nombre = $validatedData['nombre'];
        $producto->descripcion = $validatedData['descripcion'];
        $producto->precio = $validatedData['precio'];
        $producto->cantidad = $validatedData['cantidad'];
        $producto->categoria = $validatedData['categoria'];
        $producto->estado = $validatedData['estado'];
        $producto->oferta = $validatedData['oferta'] ?? 0; // Si no hay oferta, establecer como 0

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('public/fotos');
            $producto->foto = $path;
        }

        $producto->save();

        return response()->json(['message' => 'Producto creado correctamente', 'producto' => $producto]);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'precio' => 'required|numeric',
            'cantidad' => 'required|integer',
            'categoria' => 'required|string',
            'estado' => 'required|boolean',
            'oferta' => 'nullable|numeric',
            'foto' => 'nullable|image',
        ]);

        $producto = Producto::findOrFail($id);
        $producto->nombre = $validatedData['nombre'];
        $producto->descripcion = $validatedData['descripcion'];
        $producto->precio = $validatedData['precio'];
        $producto->cantidad = $validatedData['cantidad'];
        $producto->categoria = $validatedData['categoria'];
        $producto->estado = $validatedData['estado'];
        $producto->oferta = $validatedData['oferta'] ?? 0;

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('public/fotos');
            $producto->foto = $path;
        }

        $producto->save();

        return response()->json(['message' => 'Producto actualizado correctamente', 'producto' => $producto]);
    }

    public function destroy($id)
    {
        $producto = Producto::findOrFail($id);
        $producto->estado = false; // Desactivar el producto en lugar de eliminarlo
        $producto->save();

        return response()->json(['message' => 'Producto desactivado correctamente']);
    }
}
