<?php
require_once "./functions/json.php";
class Producto{
    public $id;
    public $nombre;
    public $precio;
    public $tipo;
    public $cantidad;

    public function __construct($id, $nombre, $precio, $cantidad = 1, $tipo = "comida"){
        if (is_string($id)){
            $this->id = $id;
        }
        if (is_string($nombre)){
            $this->nombre = strtolower($nombre);
        }
        if (is_float($precio)){
            $this->precio = $precio;
        }
        if (is_int($cantidad)){
            $this->cantidad = $cantidad;
        }
        if (is_string($tipo) && ($tipo == "comida" || $tipo == "bebida")){
            $this->tipo = strtolower($tipo);
        }
    }

    public function obtenerId(){
        return $this->id;
    }

    public function obtenerNombre(){
        return $this->nombre;
    }

    public function obtenerPrecio(){
        return $this->precio;
    }

    public function obtenerCantidad(){
        return $this->cantidad;
    }

    public function actualizarCantidad($value){
        if (is_int($value)){
            $this->cantidad += $value;
        }
    }

    public function obtenerTipo(){
        return $this->tipo;
    }

    private function Equals($productoAComparar){
        return is_a($productoAComparar, "Producto") ?
        $this->obtenerNombre() == $productoAComparar->obtenerNombre() 
        && $this->obtenerTipo() && $productoAComparar->obtenerTipo()
        : false;
    }

    public static function GenerarID(){
        $arrayDeProductos = self::LeerProductosJSON();
        $maximoId = 10000;
        if (count($arrayDeProductos) > 0){
            foreach($arrayDeProductos as $producto){
                if (intval($producto->obtenerId()) > $maximoId){
                    $maximoId = intval($producto->obtenerId());
                }
            }
        }
        $maximoId += 1;
        return $maximoId . "P";
    }

    private function VerificarIntegridadDatos(){
        return (isset($this->id) && isset($this->nombre) && isset($this->precio) && isset($this->cantidad) && isset($this->tipo));
    }

    private static function VerificarProducto($arrayDeProductos, $productoParam){
        if (is_array($arrayDeProductos) && count($arrayDeProductos) > 0){
            foreach($arrayDeProductos as $producto){
                if ($producto->Equals($productoParam)){
                    return true;
                }
            }
            return false;
        }
        return false;
    }

    public static function ObtenerProducto($productoParam){
        $arrayDeProductos = self::LeerProductosJSON();
        $productoReturn = null;
        if (count($arrayDeProductos) > 0){
            foreach($arrayDeProductos as $produc){
                if($produc->Equals($productoParam)){
                    $productoReturn = $produc;
                }
            }
        }
        return $productoReturn;
    }

    public static function GuardarProductoJSON($producto, $cantidad = 1){
        if (is_a($producto, "Producto")){
            $arrayDeProductos = self::LeerProductosJSON();
            if ($producto->VerificarIntegridadDatos() && !self::VerificarProducto($arrayDeProductos, $producto)){
                array_push($arrayDeProductos, $producto);           
                return JSON::EscrituraJson("./registros/productos.json", $arrayDeProductos);
            } else if ($producto->VerificarIntegridadDatos() && self::VerificarProducto($arrayDeProductos, $producto)){
                foreach($arrayDeProductos as $prod){
                    if ($prod->Equals($producto)){
                        $prod->actualizarCantidad($cantidad);
                    }
                    JSON::EscrituraJson("./registros/productos.json", $arrayDeProductos);
                    return "Se ha actualizado la lista de productos";
                }
            } else return "No se ha podido guardar el producto debido a que sus datos estan incorrectos."; 
        } else return "El objeto enviado a guardar no es un producto!";
    }

    public static function LeerProductosJSON($arrayDeProductos = array()){
        $datosJson = JSON::LecturaJson("./registros/productos.json");
        if (!empty($datosJson)){
            for ($i = 0; $i < count($datosJson); $i++){
                $producto = new self(
                    $datosJson[$i]["id"],
                    $datosJson[$i]["nombre"],
                    floatval($datosJson[$i]["precio"]),
                    intval($datosJson[$i]["cantidad"]),
                    $datosJson[$i]["tipo"]
                    );
                array_push($arrayDeProductos, $producto);
            }
        }
        return $arrayDeProductos;
    }


    public static function GenerarListaProductos($strIdProductos){        
        $listaGenerada = array();
        $arrayDeProductos = self::LeerProductosJSON();
        if (count($arrayDeProductos) > 0){
            $listaIdProductos = explode(",", $strIdProductos);
            if (!is_null($listaIdProductos) && count($listaIdProductos) > 0){
                for ($i = 0; $i < count($listaIdProductos); $i++){
                    for ($j = 0; $j < count($arrayDeProductos); $j++){
                        if ($arrayDeProductos[$j]->obtenerId() == $listaIdProductos[$i]){
                            $producto = $arrayDeProductos[$j];
                            $existenciaEnListaGenerada = false;
                            if (count($listaGenerada) > 0){
                                /*
                                    Si necesito generar 2 o mas productos a mi lista que devolveré,
                                    primero busco si ya existe el producto que intento agregar en
                                    ese momento, si existe le sumo +1 y actualizo el JSON. De lo contrario
                                    simplemente pusheo el producto distinto.
                                */
                                for($k = 0; $k < count($listaGenerada); $k++){
                                    if ($listaGenerada[$k]->obtenerId() == $producto->obtenerId()){
                                        $listaGenerada[$k]->actualizarCantidad(1);
                                        $existenciaEnListaGenerada = true;
                                        self::GuardarProductoJSON($producto, -1);
                                        break;
                                    }
                                }
                                if (!$existenciaEnListaGenerada){
                                    $producto->actualizarCantidad(($producto->obtenerCantidad() * -1) + 1);
                                    array_push($listaGenerada, $producto);
                                    self::GuardarProductoJSON($producto, -1);
                                }
                            } else{
                                /* 
                                    En la primera iteración, guardo el producto en mi lista que devolveré
                                    y le resto la cantidad al producto correspondiente de mi JSON y actualizo
                                */
                                $producto->actualizarCantidad(($producto->obtenerCantidad() * -1) + 1);
                                array_push($listaGenerada, $producto);
                                self::GuardarProductoJSON($producto, -1);
                            }
                        }
                    }
                }
            }
        }
        return $listaGenerada;
    }

    public static function MostrarProductos($arrayDeProductos){
        $mensaje = "Lista vacía o incorrecta";
        if (is_array($arrayDeProductos) && count($arrayDeProductos) > 0){
            $mensaje = "<h3 align='center'> Lista de productos </h3>";
            $mensaje .= "<table align='center'><thead><tr><th>ID</th><th>Nombre</th><th>Precio</th><th>Cantidad</th><th>Tipo</th></tr><tbody>";
            foreach($arrayDeProductos as $producto){
                $mensaje .= "<tr align='center'>" .
                "<td>" . $producto->obtenerId() . "</td>" . 
                "<td>" . $producto->obtenerNombre() . "</td>" .
                "<td> $" . $producto->obtenerPrecio() . "</td>" .
                "<td>" . $producto->obtenerCantidad() . "</td>" .
                "<td>" . $producto->obtenerTipo() . "</td></tr>";
            }
            $mensaje .= "</tbody></table>";
        }
        return $mensaje;
    }
}
?>