<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Carrito;
use App\Mail\PedidoCreado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Mail\FechaAsignada;

class PedidoController extends Controller
{
    // Método para almacenar un nuevo pedido basado en la categoría
    public function store(Request $request)
    {
        // Obtener el usuario autenticado
        $user = Auth::user();
    
        // Agregar un log para verificar el rol del usuario
        \Log::info('Rol del usuario:', ['rol' => $user->rol]);
    
        $categoria = $request->input('categoria');  // Obtener la categoría enviada desde el frontend
    
        // Verificar si el carrito del usuario para la categoría está vacío
        $carritoItems = Carrito::where('usuarios_id', $user->id)
                            ->where('categoria', $categoria)  // Filtra por la categoría
                            ->get();
    
        if ($carritoItems->isEmpty()) {
            return response()->json(['error' => 'El carrito está vacío para esta categoría'], 400);
        }
    
        // Validar el campo 'tipo_pago'
        $request->validate([
            'tipo_pago' => 'required|string',
        ]);
    
        try {
            // Verificar si el usuario es un vendedor
            $estadoPedido = $user->rol === 'vendedor' ? 'vendido' : 'en proceso';
    
            // Log para verificar el estado que se está asignando
            \Log::info('Estado asignado al pedido:', ['estado' => $estadoPedido]);
    
            // Crear un nuevo pedido con el estado correspondiente
            $pedido = Pedido::create([
                'usuario_id' => $user->id,
                'estado' => $estadoPedido,  // Asigna el estado según el rol del usuario
                'tipo_pago' => $request->tipo_pago,
            ]);
    
            // Asociar los productos del carrito de la categoría con el pedido
            foreach ($carritoItems as $item) {
                $pedido->productos()->attach($item->producto_id, ['cantidad' => $item->cantidad]);
            }
    
            // Vaciar solo los productos de la categoría seleccionada en el carrito después de crear el pedido
            Carrito::where('usuarios_id', $user->id)
                    ->where('categoria', $categoria)
                    ->delete();
    
            // Cargar las relaciones necesarias antes de enviar el correo
            $pedido = Pedido::with('productos', 'usuario')->find($pedido->id);
    
            if (!$pedido || !$pedido->usuario || $pedido->productos->isEmpty()) {
                \Log::error('Error: Pedido, usuario o productos no encontrados.');
                return response()->json(['error' => 'Error al procesar el pedido.'], 500);
            }
    
            // Enviar correo electrónico confirmando el pedido
            Mail::to($user->email)->send(new PedidoCreado($pedido));
    
            return response()->json(['mensaje' => 'Pedido creado y correo enviado.'], 201);
    
        } catch (\Exception $e) {
            // Registrar el error en los logs
            \Log::error('Error al crear el pedido: ' . $e->getMessage());
            return response()->json(['error' => 'Ocurrió un error al crear el pedido'], 500);
        }
    }

    // Método para actualizar el estado de un pedido
    public function updateEstado(Request $request, $id)
    {
        // Valida el estado y la fecha de entrega
        $request->validate([
            'estado' => 'required|string|in:en proceso,completado,cancelado,vendido',  // Incluye "vendido" en la validación
            'fecha_entrega' => 'nullable|date',  // Valida que la fecha de recogida sea opcional y una fecha válida
        ]);

        // Busca el pedido por su ID, si no lo encuentra lanza un error 404
        $pedido = Pedido::findOrFail($id);

        // Actualiza el estado del pedido con el valor proporcionado en la solicitud
        $pedido->estado = $request->estado;

        // Si se proporciona una fecha de entrega, la actualiza
        if ($request->filled('fecha_entrega')) {
            $pedido->fecha_entrega = $request->fecha_entrega;
        }

        // Guarda los cambios en la base de datos
        $pedido->save();

        // Retorna una respuesta exitosa con un mensaje
        return response()->json(['mensaje' => 'Estado del pedido actualizado correctamente.']);
    }

    public function obtenerPedidosUsuario()
{
    try {
        // Obtener el usuario autenticado
        $usuario = Auth::user();

        // Obtener los pedidos del usuario
        $pedidos = Pedido::where('usuario_id', $usuario->id)->with('productos')->get();

        return response()->json($pedidos, 200);
    } catch (\Exception $e) {
        // Registrar el error en los logs de Laravel
        \Log::error('Error al obtener los pedidos del usuario: ' . $e->getMessage());

        // Retornar un error 500 con un mensaje genérico
        return response()->json(['error' => 'Error al obtener los pedidos del usuario'], 500);
    }
}
}
