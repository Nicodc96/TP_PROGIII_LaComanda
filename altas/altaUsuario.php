<?php

$user = new Usuario($_POST["nombre"],
$_POST["apellido"],
intval($_POST["edad"]),
$_POST["rol"],
Usuario::GenerarId(),
$_POST["estado"]);

echo Usuario::GuardarUsuarioJSON($user);

