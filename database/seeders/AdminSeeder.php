<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;

class AdminSeeder extends Seeder
{
    public function run()
    {
        Usuario::create([
            'nombre' => 'Administrador',
            'direccion' => 'Dirección Admin',
            'telefono' => '000000000',
            'celular' => '000000000',
            'email' => 'admin@gpetronic.com',
            'password' => Hash::make('admin123'),
            'rol' => 'admin',
            'estado' => true,
        ]);
    }
}

// Crear el usuario vendedor
Usuario::create([
    'nombre' => 'Vendedor',
    'direccion' => 'Dirección Vendedor',
    'telefono' => '111111111',
    
   
'celular' => '111111111',
    'email' => 'vendedor@gpetronic.com',
    'password' => Hash::make('vendedor123'),
    'rol' => 'vendedor', // Rol de vendedor
    'estado' => true,
]);

