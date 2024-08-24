<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class AdminProductoController extends Controller
{
    // Obtener todos los productos
    public function index()
    {
        $productos = Producto::all();
        return response()->json($productos);
    }

    // Crear un nuevo producto
    public function store(Request $request)
    {
        // Validar los datos de la solicitud
        $request->validate([
            'nombre' => 'required',
            'descripcion' => 'required',
            'precio' => 'required|numeric',
            'cantidad' => 'required|integer',
            'categoria' => 'required',
            'foto' => 'required|image',  // La imagen es requerida para crear
            'codigo' => 'nullable|string',
        ]);

        // Crear una nueva instancia de Producto y asignar valores
        $producto = new Producto();
        $producto->nombre = $request->nombre;
        $producto->descripcion = $request->descripcion;
        $producto->precio = $request->precio;
        $producto->cantidad = $request->cantidad;
        $producto->categoria = $request->categoria;
        $producto->oferta = $request->oferta;
        $producto->estado = true;
        $producto->codigo = $request->codigo;

        // Manejar la subida de la imagen
        if ($request->hasFile('foto')) {
            $producto->foto = $request->file('foto')->store('fotos');
        }

        // Guardar el producto en la base de datos
        $producto->save();

        // Retornar respuesta de éxito
        return response()->json(['success' => 'Producto creado correctamente.']);
    }

    public function update(Request $request, $id)
{
    // Registrar los datos recibidos antes de la validación para depuración
    \Log::info('Datos recibidos en la solicitud de actualización:', $request->all());

    // Validar los datos de la solicitud
    $validatedData = $request->validate([
        'nombre' => 'required|string',
        'descripcion' => 'required|string',
        'precio' => 'required|numeric',
        'cantidad' => 'required|integer',
        'categoria' => 'required|string',
        'foto' => 'nullable|image',  // La imagen es opcional
        'codigo' => 'nullable|string',
    ]);

    // Buscar el producto por su ID
    $producto = Producto::findOrFail($id);

    // Actualizar los datos del producto con los datos validados
    $producto->update($validatedData);

    // Manejar la actualización de la imagen (si se subió una nueva)
    if ($request->hasFile('foto')) {
        $producto->foto = $request->file('foto')->store('fotos');  // Guardar la nueva imagen
    }

    // Guardar los cambios en la base de datos
    $producto->save();

    // Retornar una respuesta JSON de éxito
    return response()->json(['success' => 'Producto actualizado correctamente.']);
}


    // Desactivar un producto (no eliminarlo)
    public function destroy($id)
    {
        // Buscar el producto por su ID
        $producto = Producto::findOrFail($id);

        // Cambiar el estado del producto a falso (desactivado)
        $producto->estado = false;
        $producto->save();

        // Retornar respuesta de éxito
        return response()->json(['success' => 'Producto desactivado correctamente.']);
    }

        //Activar Producto
        public function activar($id)
    {
        $producto = Producto::findOrFail($id);
        $producto->estado = true;
        $producto->save();

        return response()->json(['success' => 'Producto activado correctamente.']);
    }
}
