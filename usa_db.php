<?php

/* 
 * Ejemplifica el uso de la clase de conexion
 * 
 */
ini_set("display_errors","1");
include './DB.php';
$con = new DB();

$sql  = " INSERT INTO usuarios(nombre,apaterno,amaterno,usuario,passwd,creado,editado,grupo,status) VALUES ";
$sql .= " ('Nombre','De','Usuario','nickname','".md5("abcd1234")."','".date("Y-m-d H:i:s")."','".date("Y-m-d H:i:s")."','1','1') ";
$rs = $con->consulta($sql);

if($rs){
    echo "Usuario registrado";
}

$sql = "select * from usuarios";
$rs = $con->consulta($sql);
echo count($rs);
echo "<br>";
echo $con->registros;


while(!$rs->EOF){
    echo $rs->datos["nombre"]."<br>";
    $rs->siguiente();
}

$con->libera();
