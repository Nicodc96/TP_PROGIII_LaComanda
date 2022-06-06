<?php

$pedido = new Pedido(
    Pedido::GenerarCodigo(5),
    $_POST["detalle"],
    $_POST["idMesa"],
    $_POST["fecha"],
    intval($_POST["tiempoEstimado"]),
    Producto::GenerarListaProductos(($_POST["productos"])),
    $_POST["estado"]   
);

echo Pedido::GuardarPedidoJSON($pedido);