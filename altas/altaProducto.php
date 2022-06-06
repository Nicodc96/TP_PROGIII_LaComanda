<?php

echo Producto::GuardarProductoJSON(new Producto(
    Producto::GenerarID(),
    $_POST["nombre"],
    floatval($_POST["precio"]),
    intval($_POST["cantidad"]),
    $_POST["tipo"]
));