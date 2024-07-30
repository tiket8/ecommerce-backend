<?php

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nombre', 'direccion', 'telefono', 'celular', 'email', 'password', 'rol', 'estado'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }
}
