<?php
$host="localhost";
$bd="sitioweb";
$usuario="root";
$contrasenia="";

//Problema de conexion

try{
    $conexion=new PDO("mysql:host=$host;dbname=$bd",$usuario,$contrasenia);
    if($conexion){echo "Conectado correctamente";}

} catch(Exception $ex){
    echo $ex->getMessage();
}
?>