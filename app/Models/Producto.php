<?php

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre', 'descripcion', 'precio', 'cantidad', 'categoria', 'oferta', 'foto', 'estado'
    ];

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }
}
