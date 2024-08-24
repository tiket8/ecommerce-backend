<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EstadisticasController extends Controller
{
    public function obtenerVentasPorCategoria(Request $request)
{
    try {
        // El filtro puede ser 'dia' o 'semana'
        $filtro = $request->query('filtro', 'semana');  
        
        // Determina la fecha de inicio según el filtro
        $fechaInicio = $filtro === 'dia' 
            ? Carbon::now()->startOfDay() 
            : Carbon::now()->startOfWeek();

        // Obtiene las ventas por categoría desde la fecha de inicio
        $ventasPorCategoria = Pedido::where('created_at', '>=', $fechaInicio)
            ->where('estado', 'vendido')
            ->with('productos')
            ->get();

        // Agrupación y lógica de ventas
        $agrupadasPorCategoria = $ventasPorCategoria->flatMap(function ($pedido) {
            return $pedido->productos->map(function ($producto) use ($pedido) {
                // Aquí usamos explícitamente la tabla pivote 'pedido_producto'
                return [
                    'categoria' => $producto->categoria,
                    'cantidad' => $pedido->productos()
                                        ->where('producto_id', $producto->id)
                                        ->sum('pedido_producto.cantidad')
                ];
            });
        })->groupBy('categoria')->map(function ($ventas, $categoria) {
            return $ventas->sum('cantidad');
        });

        return response()->json([
            'categorias' => $agrupadasPorCategoria->keys(),
            'ventas' => $agrupadasPorCategoria->values()
        ]);
    } catch (\Exception $e) {
        \Log::error('Error en obtenerVentasPorCategoria: ' . $e->getMessage());
        return response()->json(['error' => 'Error al obtener las estadísticas de ventas'], 500);
    }
}

}


