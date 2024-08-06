<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Usuario;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // Pedidos
    public function getPedidos()
    {
        $pedidos = Pedido::all();
        return response()->json($pedidos);
    }

    public function showPedido($id)
    {
        $pedido = Pedido::findOrFail($id);
        return response()->json($pedido);
    }

    public function updatePedido(Request $request, $id)
    {
        $pedido = Pedido::findOrFail($id);
        $pedido->estado = $request->estado;
        $pedido->save();

        return response()->json(['success' => 'Pedido actualizado correctamente.']);
    }

    // Productos
    public function getProductos()
    {
        $productos = Producto::all();
        return response()->json($productos);
    }

    public function storeProducto(Request $request)
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
            $producto->foto = $request->file('foto')->store('public/fotos');
        }

        $producto->save();

        return response()->json(['success' => 'Producto creado correctamente.']);
    }

    public function updateProducto(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required',
            'descripcion' => 'required',
            'precio' => 'required|numeric',
            'cantidad' => 'required|integer',
            'categoria' => 'required',
        ]);

        $producto = Producto::findOrFail($id);
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

        return response()->json(['success' => 'Producto actualizado correctamente.']);
    }

    public function destroyProducto($id)
    {
        $producto = Producto::findOrFail($id);
        $producto->estado = false;
        $producto->save();

        return response()->json(['success' => 'Producto desactivado correctamente.']);
    }

    // Usuarios
    public function getUsuarios()
    {
        $usuarios = Usuario::all();
        return response()->json($usuarios);
    }

    public function destroyUsuario($id)
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->delete();

        return response()->json(['success' => 'Usuario eliminado correctamente.']);
    }

    // Estadísticas
    public function getEstadisticas()
    {
        $ventasTotales = Pedido::where('estado', 'entregado')->count();
        $ventasPorCategoria = Producto::select('categoria', \DB::raw('count(*) as total'))
            ->join('pedidos', 'productos.id', '=', 'pedidos.producto_id')
            ->groupBy('categoria')
            ->get();

        return response()->json([
            'ventasTotales' => $ventasTotales,
            'ventasPorCategoria' => $ventasPorCategoria
        ]);
    }
}
