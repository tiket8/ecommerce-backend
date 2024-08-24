<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class OfertaController extends Controller
{
    public function index()
    {
        // Filtrar productos que estén en oferta y activos (estado = true)
        $ofertas = Producto::where('oferta', true)
                            ->where('estado', true) // Solo productos activos
                            ->get();

        return response()->json($ofertas);
    }
}
