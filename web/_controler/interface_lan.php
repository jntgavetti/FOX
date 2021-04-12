<?php

require_once("../_model/Lan.php");
$manipulacao = new Manipulacao();
ini_set('display_errors', 0 );
error_reporting(0);


if(isset($_POST['interface'])){
    $interface = $_POST['interface'];
    $manipulacao->setInterface($interface);
}


if(isset($_POST['tipo_placa'])){
    $tipo_placa = $_POST['tipo_placa'];
    $manipulacao->setTipoPlaca($tipo_placa);
}

if(isset($_POST['class_placa'])){
    $class_placa = $_POST['class_placa'];
    $manipulacao->setClassPlaca($class_placa);
}

if(isset($_POST['modo_placa'])){
    $modo_placa = $_POST['modo_placa'];
    $manipulacao->setModo($modo_placa);
}

if(isset($_POST['ip'])){
    $ip = $_POST['ip'];
    $manipulacao->setIp($ip);
}

if(isset($_POST['mask'])){
    $mask = $_POST['mask'];
    $manipulacao->setMask($mask);
}
if(isset($_POST['rede'])){
    $rede = $_POST['rede'];
    $manipulacao->setRede($rede);
}

if(isset($_POST['bcast'])){
    $bcast = $_POST['bcast'];
    $manipulacao->setBcast($bcast);
}

if(isset($_POST['status'])){
    $status = $_POST['status'];
    $manipulacao->setStatus($status);
}




if(isset($_POST['action'])){
    $action = $_POST['action'];
    
    if ($action == "add") :
        $manipulacao->adicionar();
    endif;

    if ($action == "edit") :
        $manipulacao->editar();
    endif;
   
    if ($action == "del") :
       $manipulacao->deletar('interfaces');
    endif;
    
   
}

?>