<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Carrito;
use App\Models\Producto;
use App\Mail\PedidoCreado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class CarritoController extends Controller
{
    // Obtener el carrito de productos de la categoría electrónica
    public function obtenerCarritoElectronica()
    {
        // Obtener el usuario autenticado
        $user = Auth::user();

        // Obtener los productos del carrito de la categoría 'electronica' para el usuario
        $carrito = Carrito::where('usuarios_id', $user->id)
                          ->where('categoria', 'electronica')
                          ->with('producto')
                          ->get();

        // Retornar los productos del carrito en formato JSON
        return response()->json($carrito);
    }

    // Obtener el carrito de productos de la categoría beterwere
    public function obtenerCarritoBeterwere()
    {
        // Obtener el usuario autenticado
        $user = Auth::user();

        // Obtener los productos del carrito de la categoría 'beterwere' para el usuario
        $carrito = Carrito::where('usuarios_id', $user->id)
                          ->where('categoria', 'beterwere')
                          ->with('producto')
                          ->get();

        // Retornar los productos del carrito en formato JSON
        return response()->json($carrito);
    }

    // Agregar producto al carrito de la categoría electrónica
    public function agregarProductoCarritoElectronica(Request $request)
    {
        // Llama a la lógica compartida para agregar el producto al carrito de electrónica
        return $this->agregarProductoCarrito($request, 'electronica');
    }

    // Agregar producto al carrito de la categoría beterwere
    public function agregarProductoCarritoBeterwere(Request $request)
    {
        // Llama a la lógica compartida para agregar el producto al carrito de beterwere
        return $this->agregarProductoCarrito($request, 'beterwere');
    }

    // Lógica compartida para agregar productos al carrito (funciona para cualquier categoría)
    private function agregarProductoCarrito(Request $request, $categoria)
    {
        // Validar que se proporcionen los datos requeridos (producto_id y cantidad)
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1',
        ]);

        // Obtener el usuario autenticado y buscar el producto
        $user = Auth::user();
        $producto = Producto::find($request->producto_id);

        // Verificar si el producto ya está en el carrito del usuario para la categoría dada
        $carritoItem = Carrito::where('usuarios_id', $user->id)
                              ->where('producto_id', $request->producto_id)
                              ->where('categoria', $categoria)
                              ->first();
        if ($carritoItem) {
        // Verifica que el stock disponible permita agregar más cantidad
        if ($producto->cantidad < ($carritoItem->cantidad + $request->cantidad)) {
        return response()->json(['error' => 'Cantidad excede el stock disponible'], 400);
        }                   

        // Si ya está en el carrito, suma la cantidad
        $carritoItem->cantidad += $request->cantidad;
        $carritoItem->save();
        } else {
            // Si no está en el carrito, crear una nueva entrada
            $carritoItem = new Carrito();
            $carritoItem->usuarios_id = $user->id;
            $carritoItem->producto_id = $request->producto_id;
            $carritoItem->cantidad = $request->cantidad;
            $carritoItem->categoria = $categoria;
            $carritoItem->save();
        }

        // Retornar una respuesta de éxito en formato JSON
        return response()->json(['success' => "Producto agregado al carrito de $categoria correctamente."]);
    }

    // Realizar el pedido de los productos en el carrito de la categoría electrónica
    public function realizarPedidoElectronica(Request $request)
    {
        // Llama a la lógica compartida para realizar el pedido de electrónica
        return $this->realizarPedido($request, 'electronica');
    }

    // Realizar el pedido de los productos en el carrito de la categoría beterwere
    public function realizarPedidoBeterwere(Request $request)
    {
        // Llama a la lógica compartida para realizar el pedido de beterwere
        return $this->realizarPedido($request, 'beterwere');
    }

    // Lógica compartida para realizar pedidos (funciona para cualquier categoría)
    private function realizarPedido(Request $request, $categoria)
    {
        // Obtener el usuario autenticado
        $user = Auth::user();

        // Validar que se proporcione el tipo de pago
        $request->validate(['tipo_pago' => 'required|string']);

        // Obtener los productos en el carrito de la categoría dada
        $carritoItems = Carrito::where('usuarios_id', $user->id)
                                ->where('categoria', $categoria)
                                ->with('producto')
                                ->get();

        // Si el carrito está vacío, retornar un error
        if ($carritoItems->isEmpty()) {
            return response()->json(['error' => 'El carrito está vacío'], 400);
        }

        // Crear un nuevo pedido para el usuario autenticado
        $pedido = Pedido::create([
            'usuario_id' => $user->id,
            'estado' => 'en proceso',
            'tipo_pago' => $request->tipo_pago,
        ]);

        // Asociar cada producto del carrito con el pedido
        foreach ($carritoItems as $item) {
            $pedido->productos()->attach($item->producto_id, ['cantidad' => $item->cantidad]);
        }

        // Vaciar el carrito de la categoría dada después de realizar el pedido
        Carrito::where('usuarios_id', $user->id)
               ->where('categoria', $categoria)
               ->delete();

        // Cargar las relaciones del pedido (usuario y productos) para enviar el correo de confirmación
        $pedido = Pedido::with('productos', 'usuario')->find($pedido->id);

        // Verificar que el pedido y sus relaciones existan antes de enviar el correo
        if (!$pedido || !$pedido->usuario || $pedido->productos->isEmpty()) {
            \Log::error('Error: Pedido, usuario o productos no encontrados.');
            return response()->json(['error' => 'Error al procesar el pedido.'], 500);
        }

        // Intentar enviar el correo de confirmación del pedido
        try {
            Mail::to($user->email)->send(new PedidoCreado($pedido));
        } catch (\Exception $e) {
            \Log::error('Error al enviar el correo de confirmación: ' . $e->getMessage());
            return response()->json(['error' => 'Pedido creado, pero ocurrió un error al enviar el correo.'], 500);
        }

        // Retornar una respuesta de éxito en formato JSON
        return response()->json(['success' => "Pedido de $categoria realizado con éxito y correo enviado."]);
    }

    // Eliminar un producto del carrito
    public function destroy($id)
    {
        // Obtener el usuario autenticado
        $user = Auth::user();

        // Buscar el producto en el carrito por su ID y el ID del usuario
        $carritoItem = Carrito::where('usuarios_id', $user->id)->where('id', $id)->first();

        // Si el producto no se encuentra en el carrito, registrar una advertencia y retornar un error
        if (!$carritoItem) {
            \Log::warning('Producto no encontrado en el carrito para el usuario.', [
                'usuario_id' => $user->id,
                'producto_id' => $id,
            ]);
            return response()->json(['error' => 'Producto no encontrado en el carrito.'], 404);
        }

        // Intentar eliminar el producto del carrito
        try {
            $carritoItem->delete();
            return response()->json(['success' => 'Producto eliminado del carrito correctamente.']);
        } catch (\Exception $e) {
            \Log::error('Error al eliminar producto del carrito: ' . $e->getMessage());
            return response()->json(['error' => 'Ocurrió un error al intentar eliminar el producto.'], 500);
        }
    }
}
