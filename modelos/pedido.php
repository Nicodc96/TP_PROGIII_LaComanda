<?php
require_once "./functions/json.php";
include_once "./modelos/mesa.php";
class Pedido{
    public $id;
    public $detalle;
    public $idMesa;
    public $fecha;
    public $precio;
    public $productos;
    public $tiempoEstimado;
    public $estado;

    public function __construct($id, $detalle, $idMesa, $fecha, $tiempoEstimado, $productos = array(), $estado = "pendiente"){
        if (is_string($id)){
            $this->id = $id;
        }
        if (is_string($detalle)){
            $this->detalle = strtolower($detalle);
        }
        if (is_string($idMesa)){
            $this->idMesa = $idMesa;
        }
        if (date_parse($fecha)){
            $this->fecha = $fecha;
        } else $this->fecha = date("Y-n-j");
        if (is_array($productos)){
            $this->productos = $productos;
        }
        $this->precio = $this->generarPrecio();
        if (is_int($tiempoEstimado)){
            $this->tiempoEstimado = $tiempoEstimado;
        }
        if (is_string($estado)){
            $this->estado = strtolower($estado);
        }
    }

    public function obtenerId(){
        return $this->id;
    }

    public function obtenerIdMesa(){
        return $this->idMesa;
    }

    public function obtenerDetalle(){
        return $this->detalle;
    }

    public function obtenerFecha(){
        return $this->fecha;
    }

    public function obtenerPrecio(){
        return $this->precio;
    }
    
    public function obtenerProductos(){
        if (count($this->productos) > 0){
            return $this->productos;
        } else return "Sin productos.";
    }

    public function obtenerTiempoEstimado(){
        return $this->tiempoEstimado;
    }

    public function obtenerEstado(){
        return $this->estado;
    }

    private function generarPrecio(){
        $precio = 0.00;
        if (is_array($this->obtenerProductos()) && count($this->obtenerProductos()) > 0){
            $productos = $this->obtenerProductos();
            foreach($productos as $produc){
                if (is_a($produc, "Producto")){
                    $precio += floatval($produc->obtenerPrecio() * $produc->obtenerCantidad());
                } else{
                    $precio += (floatval($produc["precio"]) * floatval($produc["cantidad"]));
                }
            }
        }
        return $precio;
    }

    public static function GenerarCodigo($longitud){
        $caracteres = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z");
        $codigo = "";
        for ($i = 0; $i < $longitud; $i++){
            $codigo .= $caracteres[rand(0, 35)];
        }
        return $codigo;
    }

    public static function LeerPedidosJSON($arrayDePedidos = array())
    {
        $datosJson = JSON::LecturaJson("./registros/pedidos.json");
        if (!empty($datosJson)){
            for ($i = 0; $i < count($datosJson); $i++){
                $pedido = new self(
                    $datosJson[$i]["id"],
                    $datosJson[$i]["detalle"],
                    $datosJson[$i]["idMesa"],
                    $datosJson[$i]["fecha"],
                    intval($datosJson[$i]["tiempoEstimado"]),
                    $datosJson[$i]["productos"],
                    $datosJson[$i]["estado"]);
                array_push($arrayDePedidos, $pedido);
            }
        }
        return $arrayDePedidos;
    }

    private function VerificarIntegridadDatos(){
        return (isset($this->id) && isset($this->detalle) && isset($this->idMesa) && isset($this->fecha)
                && isset($this->precio) && isset($this->tiempoEstimado) && isset($this->productos) 
                && isset($this->estado));
    }

    private function Equals($pedidoParam){
        return is_a($pedidoParam, "Pedido") ?
        $this->obtenerIdMesa() == $pedidoParam->obtenerIdMesa() && $this->obtenerDetalle() == $pedidoParam->obtenerDetalle()
        && $this->obtenerPrecio() == $pedidoParam->obtenerPrecio() : false;
    }

    public static function VerificarPedidoExistente($arrayDePedidos, $pedidoParam){
        if (is_array($arrayDePedidos) && count($arrayDePedidos) > 0){
            foreach($arrayDePedidos as $pedido){
                if ($pedido->Equals($pedidoParam)){
                    return true;
                }
            }
        }
        return false;
    }
    public static function GuardarPedidoJSON($pedido){
        if (is_a($pedido, "Pedido")){
            $arrayDePedidos = self::LeerPedidosJSON();
            if ($pedido->VerificarIntegridadDatos() 
            && !self::VerificarPedidoExistente($arrayDePedidos, $pedido)
            && Mesa::VerificarIDMesa($pedido->obtenerIdMesa())){
                array_push($arrayDePedidos, $pedido);
                return JSON::EscrituraJson("./registros/pedidos.json", $arrayDePedidos);
            } else return "No se ha podido guardar el pedido debido a que ya existe o sus datos estan incorrectos.";
        } else return "El objeto enviado a guardar no es un pedido!";
    }

    public static function MostrarPedidos($arrayDePedidos){
        $mensaje = "Lista vacía o incorrecta";
        if (is_array($arrayDePedidos) && count($arrayDePedidos) > 0){
            $mensaje = "<h3 align='center'> Lista de pedidos </h3>";
            $mensaje .= "<table align='center'><thead><tr><th>ID</th><th>Detalle</th><th>Fecha</th><th>Precio</th><th>Productos</th><th>Estado</th><th>Tiempo estimado</th></tr><tbody>";
            foreach($arrayDePedidos as $pedido){
                $mensaje .= "<tr align='center'>" .
                "<td>" . $pedido->obtenerId() . "</td>" .
                "<td>" . $pedido->obtenerDetalle() . "</td>" .
                "<td>" . $pedido->obtenerFecha() . "</td>" .
                "<td> $" . $pedido->obtenerPrecio() . "</td><td>";
                foreach($pedido->productos as $producto){
                    $mensaje .= $producto["nombre"] . "-";
                }
                $mensaje .= "</td><td>" . $pedido->obtenerEstado() . "</td>" .
                "<td> Aproximadamente " . $pedido->obtenerTiempoEstimado() . " minutos</td></tr>"; 
            }
            $mensaje .= "</tbody></table>";
        }
        return $mensaje;
    }
}
?>