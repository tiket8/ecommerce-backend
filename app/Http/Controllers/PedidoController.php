<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;
use App\Mail\PedidoCreado;
use Illuminate\Support\Facades\Mail;

class PedidoController extends Controller
{
    public function store(Request $request)
    {
        // Crear el pedido
        $pedido = Pedido::create([
            'usuario_id' => auth()->id(),
            'producto_id' => $request->producto_id,
            'cantidad' => $request->cantidad,
            'tipo_pago' => $request->tipo_pago,
            'estado' => 'en proceso',
        ]);

        // Enviar correo electrónico al usuario autenticado
        Mail::to(auth()->user()->email)->send(new PedidoCreado($pedido));

        return response()->json(['mensaje' => 'Pedido creado y correo enviado.']);
    }
}
