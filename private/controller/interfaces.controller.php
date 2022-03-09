<?php

require_once "../private/model/interfaces.model.php";
require_once "../private/model/interfaces.service.php";
require_once "../private/model/Conexao.php";

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : $action;

if ($action == 'list') {
    $connection = new Connection();
    $iface = new Iface();

    $intSvc = new InterfaceService($connection, $iface);


    $interfaces = $intSvc->list();


    /* echo "<pre>";
    print_r($interfaces);
    echo "</pre>"; */
}

if ($action == 'delete') {

    if (isset($_REQUEST['iface'])) {

        $connection = new Connection();

        $iface = new Iface();
        $iface->__set('iface', $_REQUEST['iface']);

        $intSvc = new InterfaceService($connection, $iface);
        echo $intSvc->delete();
        
    }
}
