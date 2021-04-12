<?php

require_once("../_model/Provedores.php");
$manipulacao = new Provedor();
error_reporting(E_ALL);

if(isset($_POST['id'])){
    $id = $_POST['id'];
    $manipulacao->setId($id);
}


if(isset($_POST['interface'])){
    $interface = $_POST['interface'];
    $manipulacao->setInterface($interface);
}


if(isset($_POST['provedor'])){
    $provedor = $_POST['provedor'];
    $manipulacao->setProvedor($provedor);
}

if(isset($_POST['prioridade'])){
    $prioridade = $_POST['prioridade'];
    $manipulacao->setPrioridade($prioridade);
}

if(isset($_POST['modo_operacao'])){
    $modo_operacao = $_POST['modo_operacao'];
    $manipulacao->setModoOperacao($modo_operacao);
}

if(isset($_POST['d_pppoe'])){
    $d_pppoe = $_POST['d_pppoe'];
    $manipulacao->setDPPPoe($d_pppoe);
}

if(isset($_POST['u_pppoe'])){
    $u_pppoe = $_POST['u_pppoe'];
    $manipulacao->setUserPPPoe($u_pppoe);
}

if(isset($_POST['s_pppoe'])){
    $s_pppoe = $_POST['s_pppoe'];
    $manipulacao->setSenhaPPPoe($s_pppoe);
}

if(empty($_POST['ip_valido'])){
    $ip_valido = "189.200.120.1";
    $manipulacao->setIpValido($ip_valido);
}

if(isset($_POST['status'])){
    $status = $_POST['status'];
    $manipulacao->setStatus($status);
}




if(isset($_POST['action'])){
    $action = $_POST['action'];
    
    if ($action == "add") :
        $retorno = $manipulacao->adicionar();
        if($retorno == ""){
            echo 1;
        }else{
            echo json_encode($retorno);
        }
        
    endif;

    if ($action == "edit") :
        $manipulacao->editar();
    endif;
   
    if ($action == "del") :
        $retorno = $manipulacao->deletar($id);
       if($retorno == ""){
        echo 1;
        }else{
            echo json_encode($retorno);
        }
    endif;
    
   
}
