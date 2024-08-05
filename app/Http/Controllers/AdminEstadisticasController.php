<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Http\Request;

class AdminEstadisticasController extends Controller
{
    public function index()
    {
        $ventasTotales = Pedido::where('estado', 'entregado')->count();
        $ventasPorCategoria = Producto::select('categoria', \DB::raw('count(*) as total'))
            ->join('pedidos', 'productos.id', '=', 'pedidos.producto_id')
            ->groupBy('categoria')
            ->get();
        
        return view('admin.estadisticas.index', compact('ventasTotales', 'ventasPorCategoria'));
    }
}
