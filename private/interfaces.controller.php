<?php

require_once "interfaces.model.php";
require_once "interfaces.service.php";
require_once "conexao.php";

$action = isset($_GET['action']) ? $_GET['action'] : $action;

if ($action == 'list') {
    $connection = new Connection();

    $intSvc = new InterfaceService($connection);

    
    $interfaces = $intSvc->list();
   

    /* echo "<pre>";
    print_r($interfaces);
    echo "</pre>"; */
}
