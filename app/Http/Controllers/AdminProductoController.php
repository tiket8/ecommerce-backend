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
        $request->validate([
            'nombre' => 'required',
            'descripcion' => 'required',
            'precio' => 'required|numeric',
            'cantidad' => 'required|integer',
            'categoria' => 'required',
            'foto' => 'required|image',
        ]);

        $producto = new Producto();
        $producto->nombre = $request->nombre;
        $producto->descripcion = $request->descripcion;
        $producto->precio = $request->precio;
        $producto->cantidad = $request->cantidad;
        $producto->categoria = $request->categoria;
        $producto->oferta = $request->oferta;
        $producto->estado = true;

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('public/fotos');
            $producto->foto = basename($path);
        }

        $producto->save();

        return response()->json(['success' => 'Producto creado correctamente.']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([ //valida solicituddd 
            'nombre' => 'required',
            'descripcion' => 'required',
            'precio' => 'required|numeric',
            'cantidad' => 'required|integer',
            'categoria' => 'required',
        ]);

        $producto = Producto::findOrFail($id);//busca prodcto

        //actualiza campos
        $producto->nombre = $request->nombre;
        $producto->descripcion = $request->descripcion;
        $producto->precio = $request->precio;
        $producto->cantidad = $request->cantidad;
        $producto->categoria = $request->categoria;
        $producto->oferta = $request->oferta;

        if ($request->hasFile('foto')) {
            $producto->foto = $request->file('foto')->store('public/fotos');
        }

        $producto->save();
        //retorna respuesta JSON
        return response()->json(['success' => 'Producto actualizado correctamente.']);
    }

    public function destroy($id)
    {
        $producto = Producto::findOrFail($id);
        $producto->estado = false;
        $producto->save();

        return response()->json(['success' => 'Producto desactivado correctamente.']);
    }
}
