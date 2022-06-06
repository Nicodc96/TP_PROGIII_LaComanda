<?php
// Diseñado por Díaz, Lautaro Nicolás - UTN Fra
class JSON{
    public static function LecturaJson($nombreArchivo){
        try{
            if (file_exists($nombreArchivo)){
                $archivo = fopen($nombreArchivo, "r");
                if ($archivo == true){
                    $json = fread($archivo, filesize($nombreArchivo));
                    $datosJson = json_decode($json, true);
                }
                fclose($archivo);
                return $datosJson;
            }
        } catch(Exception $e){
            return "Se ha producido un error al leer los usuarios.<br/>Razón: " . $e->getMessage();
        }
    }
    public static function EscrituraJson($nombreArchivo, $arrayDeDatos){
        $archivo = fopen($nombreArchivo, 'w');
        if ($archivo){
            $json = json_encode($arrayDeDatos, JSON_PRETTY_PRINT);
            if(fwrite($archivo, $json)){
                $mensaje = 'Se ha generado el archivo JSON correctamente.';
            } else{
                $mensaje = 'Hubo un problema al guardar el archivo JSON.';
            }
            fclose($archivo);
        }
        return $mensaje;
    }
}
?>