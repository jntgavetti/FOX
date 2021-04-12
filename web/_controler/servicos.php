<?php

ini_set('display_errors', 1);
error_reporting('E_ALL');
require_once('ssh.php');
$action = $_POST['action'];
$file = '../regras/firewall/protocolos';


if ($action == "getDoors") {

    if (isset($_POST['protocol'])) {
        $protocol_req = $_POST['protocol'];
    }

    $file = fopen($file, 'r');
    $doors = [];
    $html = [];

    $html[0] =
    "<div class='div_info_linha'>
        <span><i class='fas fa-door-open'></i></span>
        <input type='text' value='0' class='form-control linha_topico defou orfao' data-toggle='tooltip' data-placement='right' title='' required>
        <button class='btn btn-sm btn_add'><i class='fa fa-plus-circle'></i></button>
        <button class='btn btn-sm btn_del'><i class='fa fa-times-circle'></i></button>
    </div>";

    if ($file != 0) {
        while (!feof($file)) {
            $linha = fgets($file);
            $linha = str_replace(' ', '', trim($linha));

            $array_protocolo = explode('=', $linha);
            $doors_array = explode(',', $array_protocolo[1]);

            
            if ($array_protocolo[0] == $protocol_req) {
               
                foreach ($doors_array as $key => $door) {
                    $html[$key] =
                        "<div class='div_info_linha'>
                            <span><i class='fas fa-door-open'></i></span>
                            <input type='text' value='$door' class='form-control linha_topico defou orfao' data-toggle='tooltip' data-placement='right' title='' required>
                            <button class='btn btn-sm btn_add'><i class='fa fa-plus-circle'></i></button>
                            <button class='btn btn-sm btn_del'><i class='fa fa-times-circle'></i></button>
                        </div>";
                }
            }
        }
    } 
    echo json_encode($html);
}

if ($action == "getProtocol") {

    $file = fopen($file, 'r');

    $i = 0;
    $protocolos = [];

    if ($file != 0) {
        while (!feof($file)) {
            $linha = fgets($file);
            $linha = str_replace(' ', '', trim($linha));

            if (!empty($linha) && strlen($linha) > 0) {
                $array_protocolo = explode("=", $linha);

                $protocolos[$i] = $array_protocolo[0];
                $i++;
            }
        }
    }

    sort($protocolos);
    echo json_encode($protocolos);
}

if ($action == "salvar") {

    $servico = $_POST["servico"];
    $servico = json_decode($servico);
    $old_prot = $servico->old_prot;
    $prot = $servico->prot;
    $doors = $servico->doors;
    $status = 0;
    $new_file = [];
    foreach ($doors as $door) {
        if ($door != end($doors)) {
            $ports .= $door . ",";
        } else {
            $ports .= $door;
        }
    }


    $handle = file_get_contents($file);
    $lista = preg_split("/\n/", $handle);
    $i = 0;

    foreach ($lista as $linha) {

        $linha = trim($linha);


        if (!empty($linha) && strlen($linha) != 0) {

            $divide = explode("=", $linha);
            $divide_prot = trim($divide[0]);

            if ($divide_prot == $prot) {
                $new_file[$i] = $prot . "=" . $ports;
                $i++;
                continue;
            }

            $new_file[$i] = $linha;
            $i++;
        }
    }


    if ($handle = fopen($file, 'a')) {
        $status = 1;
    } else {
        $arquivo = 0;
        $status = 0;
        $detalhe = "Não foi possível abrir o arquivo para gravação. <br> Codigo: ADAG02";
    }


    $str = "";

    if ($status > 0) {
        foreach ($new_file as $linha) {
            $str .= $linha . "\n";
        }


        if ($handle = fopen($file, 'w+')) {
            fwrite($handle, $str);
            $status = 1;
        } else {
            $status = 0;
            $detalhe = "Não foi possível abrir o arquivo para gravação. <br> Codigo: ADAG03";
        }
    }

    $arr_processamento = [];
    $arr_processamento["status"] = $status;
    $arr_processamento["detalhe"] = $detalhe;
    echo json_encode($arr_processamento);
}

if ($action == "excluir") {
    $st = 0;

    if (isset($_POST['grupo'])) {
        $grupo = $_POST['grupo'];
    }

    $str_protocols = file_get_contents($file);
    $array_protocols = preg_split("/\n/", $str_protocols);

    $i = 0;
    $status = 0;
    $dados = [];
    $excluido = false;

    foreach ($array_protocols as $protocol_line) {

        $protocol_line = trim($protocol_line);

        if (!empty($protocol_line) && strlen($protocol_line) != 0) {

            $split = explode("=", $protocol_line);
            $prot = trim($split[0]);

            if ($prot == $grupo) {
                $excluido = true;
                $status = 1;
                continue;
            }
            $dados[$i] = $protocol_line;

            $i++;
        }
    }



    if ($handle = fopen($file, 'a')) {
        $status = 1;
    } else {
        $arquivo = 0;
        $status = 0;
        $detalhe = "Não foi possível abrir o arquivo para gravação. <br> Codigo: ADAG02";
    }


    $str = "";

    if ($status > 0) {
        foreach ($dados as $linha) {
            $str .= $linha . "\n";
        }


        if ($handle = fopen($file, 'w+')) {
            fwrite($handle, $str);
            $status = 1;
        } else {
            $status = 0;
            $detalhe = "Não foi possível abrir o arquivo para gravação. <br> Codigo: ADAG03";
        }
    }


    $arr_processamento = [];
    $arr_processamento["status"] = $status;
    $arr_processamento["detalhe"] = $detalhe;
    echo json_encode($arr_processamento);
}

if ($action == "aplicar") {

    $exec_squid = shell_exec("sudo php /opt/hsistema/scripts/squid.php 2>&1");

    $arr_processamento = [];

    if ($exec_squid == 1) {
        $arr_processamento["status"] = 1;
    } else {
        $arr_processamento["detalhe"] = $exec_squid;
    }

    echo json_encode($arr_processamento);
}
