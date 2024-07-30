<?php

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'usuario_id', 'producto_id', 'cantidad', 'tipo_pago', 'estado'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
