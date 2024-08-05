<!DOCTYPE html>
<html>
<head>
    <title>Pedido Creado</title>
</head>
<body>
    <h1>Pedido Creado</h1>
    <p>Tu pedido ha sido creado con éxito.</p>
    <p>Detalles del Pedido:</p>
    <ul>
        <li>ID del Pedido: {{ $pedido->id }}</li>
        <li>Producto: {{ $pedido->producto->nombre }}</li>
        <li>Cantidad: {{ $pedido->cantidad }}</li>
        <li>Tipo de Pago: {{ $pedido->tipo_pago }}</li>
        <li>Estado: {{ $pedido->estado }}</li>
    </ul>
</body>
</html>
