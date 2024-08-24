<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Pedido;

class FechaAsignada extends Mailable
{
    use Queueable, SerializesModels;

    public $pedido;
    public $fecha_entrega;

    public function __construct(Pedido $pedido, $fecha_entrega)
    {
        $this->pedido = $pedido;
        $this->fecha_entrega = $fecha_entrega;
    }

    public function build()
    {
        return $this->view('emails.fecha_asignada')
                    ->with([
                        'pedido' => $this->pedido,
                        'fecha_entrega' => $this->fecha_entrega
                    ]);
    }
}
