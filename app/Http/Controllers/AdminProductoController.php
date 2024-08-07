<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;

class AdminProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::all();
        return response()->json($productos);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'precio' => 'required|numeric',
            'cantidad' => 'required|integer',
            'categoria' => 'required|string',
            'estado' => 'required|boolean',
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $producto = new Producto();
        $producto->nombre = $request->nombre;
        $producto->descripcion = $request->descripcion;
        $producto->precio = $request->precio;
        $producto->cantidad = $request->cantidad;
        $producto->categoria = $request->categoria;
        $producto->estado = $request->estado;

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $path = $file->store('public/fotos');
            $producto->foto = $path;
        }

        $producto->save();

        return response()->json(['message' => 'Producto agregado correctamente'], 201);
    }

    public function destroy($id)
    {
        $producto = Producto::findOrFail($id);
        $producto->estado = false;
        $producto->save();

        return response()->json(['message' => 'Producto desactivado correctamente']);
    }
}
