<?php 
    $server= 'localhost';
    $username = 'root';
    $password = '';
    $database = 'curricual';


    $conexion = new mysqli($server,$username,$password,$database);

if($conexion->connect_errno){
    echo "fallos";
    exit();
}

?>