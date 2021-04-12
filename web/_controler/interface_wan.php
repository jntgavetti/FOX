<?php

require_once("../_model/Wan.php");
$manipulacao = new Manipulacao();
error_reporting(E_ALL);


if(isset($_POST['interface'])){
    $interface = $_POST['interface'];
    $manipulacao->setInterface($interface);
}


if(empty($_POST['tipo_placa'])){
    $tipo_placa = "fisica";
    $manipulacao->setTipoPlaca($tipo_placa);
}

if(empty($_POST['class_placa'])){
    $class_placa = 'externa';
    $manipulacao->setClassPlaca($class_placa);
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

if(isset($_POST['gw'])){
    $gw = $_POST['gw'];
    $manipulacao->setGw($gw);
}

if(isset($_POST['rede'])){
    $rede = $_POST['rede'];
    $manipulacao->setRede($rede);
}

if(isset($_POST['bcast'])){
    $bcast = $_POST['bcast'];
    $manipulacao->setBcast($bcast);
}

if(isset($_POST['dns1'])){
    $dns1 = $_POST['dns1'];
    $manipulacao->setDns1($dns1);
}

if(isset($_POST['dns2'])){
    $dns2 = $_POST['dns2'];
    $manipulacao->setDns2($dns2);
}

if(empty($_POST['mac'])){
    $mac = 'd4:v3:b6:2f:bg:d2';
}else{
    $mac = $_POST['mac'];
}

$manipulacao->setMac($mac);


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