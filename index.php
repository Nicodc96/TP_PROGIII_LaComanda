<?php

switch($_SERVER["REQUEST_METHOD"]){
    case "POST":
        switch(key($_POST))
        {
            case "altaUsuario":
                include_once "./modelos/usuario.php";
                require_once "./altas/altaUsuario.php";
                break;
            case "altaProducto":
                include_once "./modelos/producto.php";
                require_once "./altas/altaProducto.php";
                break;
            case "altaMesa":
                include_once "./modelos/mesa.php";
                require_once "./altas/altaMesa.php";
                break;
            case "altaPedido":
                include_once "./modelos/producto.php";
                include_once "./modelos/pedido.php";
                require_once "./altas/altaPedido.php";
                break;
        }
        break;
    case "GET":
        switch(key($_GET))
        {
            case "usuarios":
                include_once "./modelos/usuario.php";
                require_once "./listar/listarUsuarios.php";
                break;
            case "productos":
                include_once "./modelos/producto.php";
                require_once "./listar/listarProductos.php";
                break;
            case "mesas":
                include_once "./modelos/mesa.php";
                require_once "./listar/listarMesas.php";
                break;
            case "pedidos":
                include_once "./modelos/pedido.php";
                require_once "./listar/listarPedidos.php";
                break;
        }
        break;
}
?>