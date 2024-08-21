<!DOCTYPE html>
<html>
<head>
    <title>Confirmación de Pedido</title>
</head>
<body>
    <h1>¡Gracias por tu pedido!</h1>
    <p>Hola {{ $pedido->usuario->nombre ?? 'Cliente' }},</p>
    <p>Tu pedido ha sido creado exitosamente. Aquí tienes los detalles de tu compra:</p>

    <h2>Detalles del Pedido:</h2>
    <p><strong>Estado del Pedido:</strong> {{ $pedido->estado }}</p>
    <p><strong>Tipo de Pago:</strong> {{ $pedido->tipo_pago }}</p>
    
    <h2>Productos en tu Pedido:</h2>
    <ul>
        @foreach ($pedido->productos as $producto)
    <li>
        <strong>{{ $producto->nombre ?? 'Producto desconocido' }}</strong> - 
        Cantidad: {{ $producto->pivot->cantidad ?? 0 }} - 
        Precio: {{ $producto->precio ?? 'No disponible' }}
    </li>
@endforeach
    </ul>

    <p><strong>Total:</strong> ${{ $pedido->productos->sum(fn($p) => ($p->pivot->cantidad ?? 0) * ($p->precio ?? 0)) }}</p>

    <p>Gracias por comprar con nosotros.</p>

    <p>Atentamente,<br>El equipo de {{ config('app.name') }}</p>
</body>
</html>
