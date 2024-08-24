@component('mail::message')
# Hola {{ $pedido->usuario->nombre }},

Tu pedido con el ID #{{ $pedido->id }} ha sido actualizado. Se ha asignado la fecha de entrega:

**Fecha de entrega:** {{ $fechaEntrega }}

Por favor, pasa a recoger tu pedido en esa fecha.

Gracias por comprar con nosotros.

@component('mail::button', ['url' => url('/')])
Ir a la tienda
@endcomponent

Gracias,<br>
{{ config('app.name') }}
@endcomponent
