<?php

echo Mesa::GuardarMesaJSON(new Mesa(
    Mesa::GenerarID(),
    intval($_POST["capacidad"]),
    $_POST["estado"]));