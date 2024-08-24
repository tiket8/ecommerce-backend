<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    // Obtener productos de Electrónica
    public function getProductosElectronica()
    {
        // Filtrar productos activos de la categoría 'electronica'
        $productos = Producto::where('categoria', 'electronica')
                             ->where('estado', 1)  // Solo productos activos
                             ->get();

        return response()->json($productos);
    }

    // Obtener productos de Beterwere
    public function getProductosBeterwere()
    {
        // Filtrar productos activos de la categoría 'beterwere'
        $productos = Producto::where('categoria', 'beterwere')
                             ->where('estado', 1)  // Solo productos activos
                             ->get();

        return response()->json($productos);
    }
}
