<?php
require_once "./functions/json.php";
class Mesa{
    public $id;
    public $capacidad;
    public $estado;

    public function __construct($id, $capacidad, $estado = "libre"){
        if (is_string($id)){
            $this->id = $id;
        }
        if (is_int($capacidad)){
            $this->capacidad = $capacidad;
        }
        if (is_string($estado) && (strtolower($estado) == "libre" || strtolower($estado) == "ocupado")){
            $this->estado = strtolower($estado);
        }
    }

    public function obtenerId(){
        return $this->id;
    }

    public function obtenerCapacidad(){
        return $this->capacidad;
    }

    public function obtenerEstado(){
        return $this->estado;
    }

    public static function GenerarID(){
        $arrayDeMesas = self::LeerMesasJSON();
        $maximoId = 10000;
        if (count($arrayDeMesas) > 0){
            foreach($arrayDeMesas as $mesa){
                if (intval($mesa->obtenerId()) > $maximoId){
                    $maximoId = intval($mesa->obtenerId());
                }
            }
        }
        $maximoId += 1;
        return $maximoId . "M";
    }

    public static function LeerMesasJSON($arrayDeMesas = array()){
        $datosJson = JSON::LecturaJson("./registros/mesas.json");
        if (!empty($datosJson)){
            for ($i = 0; $i < count($datosJson); $i++){
                $mesa = new self(
                    $datosJson[$i]["id"],
                    intval($datosJson[$i]["capacidad"]),
                    $datosJson[$i]["estado"]
                    );
                array_push($arrayDeMesas, $mesa);
            }
        }
        return $arrayDeMesas;
    }

    private function VerificarIntegridadDatos(){
        return (isset($this->id) && isset($this->capacidad) && isset($this->estado));
    }

    // Verifica la existencia de una mesa
    public static function VerificarIDMesa($id){
        $arrayDeMesas = self::LeerMesasJSON();
        if (count($arrayDeMesas) > 0){
            foreach($arrayDeMesas as $mesa){
                if ($mesa->obtenerId() == $id){
                    return true;
                }
            }
        }
        return false;
    }

    private function Equals($mesaParam){
        return is_a($mesaParam, "Mesa") ?
        $this->obtenerId() == $mesaParam->obtenerId()
        : false;
    }

    public static function VerificarMesaExistente($arrayDeMesas, $mesaParam){
        if (is_array($arrayDeMesas) && count($arrayDeMesas) > 0){
            foreach($arrayDeMesas as $mesa){
                if ($mesa->Equals($mesaParam)){
                    return true;
                }
            }
            return false;
        }
        return -1;
    }

    public static function GuardarMesaJSON($mesa){
        if (is_a($mesa, "Mesa")){
            $arrayDeMesas = self::LeerMesasJSON();
            if ($mesa->VerificarIntegridadDatos() && !self::VerificarMesaExistente($arrayDeMesas, $mesa)){
                array_push($arrayDeMesas, $mesa);
                return JSON::EscrituraJson("./registros/mesas.json", $arrayDeMesas);
            } else return "No se ha podido guardar la mesa debido a que ya existe o sus datos estan incorrectos.";
        } else return "El objeto enviado a guardar no es una mesa!";
    }

    public static function MostrarMesas($arrayDeMesas){
        $mensaje = "Lista vacía o incorrecta";
        if (is_array($arrayDeMesas) && count($arrayDeMesas) > 0){
            $mensaje = "<h3 align='center'> Lista de mesas </h3>";
            $mensaje .= "<table align='center'><thead><tr><th>ID</th><th>Capacidad</th><th>Estado</th></tr><tbody>";
            foreach($arrayDeMesas as $mesa){
                $mensaje .= "<tr align='center'>" .
                "<td>" . $mesa->obtenerId() . "</td>" . 
                "<td>" . $mesa->obtenerCapacidad() . " personas</td>" .
                "<td>" . $mesa->obtenerEstado() . "</td></tr>";
            }
            $mensaje .= "</tbody></table>";
        }
        return $mensaje;
    }
}
?>