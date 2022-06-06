<?php
require_once "./functions/json.php";
class Usuario{
    public $nombre;
    public $apellido;
    public $edad;
    public $rol;
    public $estado;
    public $id;

    public function __construct($nombre, $apellido, $edad, $rol = "mozo", $id, $estado = "activo"){
        if (is_string($nombre)){
            $this->nombre = strtolower($nombre);
        }
        if (is_string($apellido)){
            $this->apellido = strtolower($apellido);
        }
        if (is_int($edad)){
            $this->edad = $edad;
        }
        if (is_string($rol)){
            $this->rol = strtolower($rol);
        }
        if (is_string($id)){
            $this->id = $id;
        }
        if (is_string($estado) && ($estado == "activo" || $estado == "inactivo")){
            $this->estado = strtolower($estado);
        }
    }

    public function obtenerRol(){
        return $this->rol;
    }

    public function obtenerId(){
        return $this->id;
    }

    public function obtenerEdad(){
        return $this->edad;
    }

    public function obtenerEstado(){
        return $this->estado;
    }

    public function obtenerNombre(){
        return $this->apellido . ", " . $this->nombre;
    }

    private function Equals($usuarioAComparar){
        return is_a($usuarioAComparar, "Usuario") ?
        $this->obtenerId() == $usuarioAComparar->obtenerId() : false;
    }

    private function verificarDatosIguales($usuarioAComparar){
        return is_a($usuarioAComparar, "Usuario") ?
        $this->obtenerNombre() == $usuarioAComparar->obtenerNombre() 
        && $this->obtenerRol() == $usuarioAComparar->obtenerRol() 
        : false;
    }
    
    public static function GuardarUsuarioJSON($usuario){
        if (is_a($usuario, "Usuario")){
            $arrayDeUsuarios = self::LeerUsuariosJSON();
            if (!self::VerificarUsuario($arrayDeUsuarios, $usuario)){
                array_push($arrayDeUsuarios, $usuario);
                return JSON::EscrituraJson("./registros/usuarios.json", $arrayDeUsuarios);
            } else return "Ya existe un usuario registrado con el mismo nombre y rol.";   
        } else return "El objeto enviado por parámetro no es un usuario.";
    }

    public static function LeerUsuariosJSON($arrayDeUsuarios = array()){
        $datosJson = JSON::LecturaJson("./registros/usuarios.json");
        if (!empty($datosJson)){
            for ($i = 0; $i < count($datosJson); $i++){
                $usuario = new self(
                    $datosJson[$i]["nombre"],
                    $datosJson[$i]["apellido"],
                    intval($datosJson[$i]["edad"]),
                    $datosJson[$i]["rol"],
                    $datosJson[$i]["id"],
                    $datosJson[$i]["estado"]);
                array_push($arrayDeUsuarios, $usuario);
            }
        }
        return $arrayDeUsuarios;
    }

    private static function VerificarUsuario($arrayDeUsuarios, $usuario){
        if (is_array($arrayDeUsuarios) && count($arrayDeUsuarios) > 0 && is_a($usuario, "Usuario")){
            foreach($arrayDeUsuarios as $userArray){
                if ($userArray->verificarDatosIguales($usuario)){
                    return true;
                }
            }
        }
        return false;
    }

    public static function GenerarId(){
        $arrayDeUsuarios = self::LeerUsuariosJSON();
        $idMax = 10000;
        if (count($arrayDeUsuarios) > 0){
            foreach($arrayDeUsuarios as $user){
                if(intval($user->obtenerId() > $idMax)){
                    $idMax = intval($user->obtenerId());
                }
            }            
        }
        $idMax += 1;
        return $idMax . "U";
    }

    public static function MostrarUsuarios($arrayDeUsuarios){
        $mensaje = "Lista vacia.";
        if (is_array($arrayDeUsuarios) && count($arrayDeUsuarios) >= 0){
            $mensaje = "<h3 align='center'> Lista de usuarios </h3>";
            $mensaje .= "<table align='center'><thead><tr><th>ID</th><th>Nombre</th><th>Edad</th><th>Rol</th><th>Estado</th></tr><tbody>";
            foreach($arrayDeUsuarios as $user){
                $mensaje .= "<tr align='center'>" .
                "<td>" . $user->obtenerId() . "</td>" .
                "<td>" . $user->obtenerNombre() . "</td>" .
                "<td>" . $user->obtenerEdad() . "</td>" .
                "<td>" . $user->obtenerRol() . "</td>" .
                "<td>" . $user->obtenerEstado() . "</td></tr>";
            }
            $mensaje .= "</tbody></table>";
        }
        return $mensaje;
    }
}
?>