<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
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
