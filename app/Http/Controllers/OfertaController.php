<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Oferta;

class OfertaController extends Controller
{
    public function index()
    {
        $ofertas = Oferta::all();
        return response()->json($ofertas);
    }
}
