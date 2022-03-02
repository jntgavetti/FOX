<?php


ini_set('display_errors', 1);
error_reporting('E_ALL');
require_once('letra_numero.php');
require_once("ssh.php");
$grupos_proxy = array();
$grupos_fw = array();
$array_pos_edicao = array();
$count = 0;
$status = 0;
$addmac = "../regras/addmac";


function ordena_ips($array_pre, $action)
{

    $array_final_ip = array();
    $indice = 0;

    if ($action == 'edit') {
        foreach ($array_pre as $indice => $conteudo) {
            if ($conteudo[0] == "#") {
                $comment = $conteudo;
                continue;
            }

            $linha = explode(";", $conteudo);
            $ip = (string)$linha[27];
            $final_ip = preg_split('/[0-9]+.[0-9]+.[0-9]./', $ip);
            $array_final_ip[$indice] = $final_ip[1];
        }

        sort($array_final_ip, SORT_NUMERIC);

        $array_pre[0] = $comment;


        foreach ($array_pre as $i => $c) {

            $linha = explode(";", $c);
            $ip = (string)$linha[27];
            $final_ip = preg_split('/[0-9]+.[0-9]+.[0-9]./', $ip);
            $final_ip = $final_ip[1];

            for ($indice = 1; $indice < sizeof($array_final_ip); $indice++) {

                $conteudo = $array_final_ip[$indice];
                $conteudo_pai = $c;

                if ($conteudo == $final_ip) {
                    $array_pre[$indice] = $conteudo_pai;
                    break;
                }
            }
        }

        return $array_pre;
    }
}

function descobreIP($ip)
{
    $ip_cortado = preg_split("/[\s.]+/", $ip);
    return intval($ip_cortado[3]);
}
function resultado($st, $dt)
{
    $obj_status = new stdClass;
    $obj_status->status = $st;
    $obj_status->detalhe = $dt;
    return json_encode($obj_status);
}

if (isset($_POST['action'])) {

    $action = $_POST['action'];


    if ($action == 'lista_dispositivo') :
       
        if (isset($_POST['ip_procura'])) {
            $ip_procura = $_POST['ip_procura'];
        }

        $addmac_content =  file_get_contents($addmac);
        $lista_dispositivos = preg_split("/;;;/", $addmac_content);
        $comm = false;
        $nome_obj = 0;
        $ancap = new stdClass;


        foreach ($lista_dispositivos as $dispositivo) {

            $dispositivo = trim($dispositivo);


            if (!empty($dispositivo) && strlen($dispositivo) != 0) {

                $obj_addmac = new StdClass();
                $array_data = explode(";", $dispositivo);
                foreach ($array_data as $data) {

                    if (!empty($data)) {

                        $divide = explode("=", $data);
                        $prop = str_replace(' ', '', trim($divide[0]));
                        $val = trim($divide[1]);

                        if (strpos($val, "usuario") || strpos($val, "exemplo")) {
                            $comm = true;
                            break;
                        } else {
                            $comm = false;
                        }


                        if (!empty($prop) && !empty($val) && strlen($val) != 0) {
                            $obj_addmac->$prop = $val;
                        } else {
                            $obj_addmac->$prop = "N/D";
                        }
                    }
                }

                if ($comm) {
                    continue;
                }


                $nome = $obj_addmac->nome;
                $setor = $obj_addmac->setor;
                $ipv4 = $obj_addmac->ipv4;
                $mac = $obj_addmac->mac;

                if ($ipv4 == $ip_procura) {
                    $dados_procura = json_encode($obj_addmac);
                }

                $ancap->$nome_obj = $obj_addmac;
                $nome_obj++;
            }
        }
        if ($ip_procura == "all") {
            echo json_encode($ancap);
        } else {
            echo $dados_procura;
        }

    endif;

    if ($action == "add") :

        $dispositivo = $_POST["dispositivo"];
        $dispositivo = json_decode($dispositivo);
        $status = 0;
        $obj_status = new stdClass;

        $nome = $dispositivo->nome;
        $setor = $dispositivo->setor;
        $ipv4 = $dispositivo->ipv4;
        $mac = $dispositivo->mac;

        if ($handle = fopen('../regras/addmac', 'r')) {
            $f_addmac = fopen('../regras/addmac', 'r');
        } else {
            $status += 1;
            $detalhe = "Não foi possível abrir o arquivo addmac para leitura. AL001";
        }

        while (!feof($f_addmac)) {

            $linha_arquivo = fgets($f_addmac);
            $divide = explode("=", $linha_arquivo);
            $prop = str_replace(' ', '', trim($divide[0]));
            $val = trim($divide[1]);


            if (!empty(trim($prop)) && !empty(trim($val))) {
                $obj_addmac->$prop = $val;
            } else {
                $obj_addmac->$prop = "N/D";
            }

            $ip_procurado = preg_match("/$ipv4;$/", $val);
            if ($ip_procurado) {
                $status += 1;
                $detalhe = "O Endereço IPv4 informado já existe.";
            }
        }
        fclose($f_addmac);

        if ($status == 0) {

            $novo_dispositivo = "\nnome = $nome;\n";
            $novo_dispositivo .= "setor = $setor;\n";
            $novo_dispositivo .= "ipv4 = $ipv4;\n";
            $novo_dispositivo .= "mac = $mac;\n";
            $novo_dispositivo .= ";;;";

            if ($handle = fopen($addmac, 'a+')) {
                $arquivo = fopen($addmac, 'a+');
            } else {
                $arquivo = 0;
                $status += 1;
                $detalhe = "Não foi possível abrir o arquivo para gravação. <br> Codigo: ADAG02";
            }

            if ($status == 0) {
                if ($handle = fwrite($arquivo, $novo_dispositivo)) {
                    $status += 0;
                } else {
                    $status += 1;
                    $detalhe = "Não foi possível abrir o arquivo para gravação. <br> Codigo: ADAG03";
                }
            }
        }

        echo resultado($status, $detalhe);
    endif;

    if ($action == "edit") :

        $dispositivo = $_POST["dispositivo"];
        $dispositivo = json_decode($dispositivo);
        $status = 0;
        $teste = [];

        $new_nome = $dispositivo->nome;
        $new_setor = $dispositivo->setor;
        $ipv4nilla = $dispositivo->ipv4nilla;
        $new_ipv4 = $dispositivo->ipv4;
        $new_mac = $dispositivo->mac;

        $addmac_content = file_get_contents($addmac);
        $lista_dispositivos = preg_split("/;;;/", $addmac_content);
        $i = 0;

        foreach ($lista_dispositivos as $dispositivo_addmac) {

            $dispositivo_addmac = trim($dispositivo_addmac);


            if (!empty($dispositivo_addmac) && strlen($dispositivo_addmac) != 0) {
                $obj_addmac = new StdClass();
                $array_data = explode(";", $dispositivo_addmac);

                foreach ($array_data as $data) {

                    if (!empty(trim($data))) {

                        $divide = explode("=", $data);
                        $prop = str_replace(' ', '', trim($divide[0]));
                        $val = trim($divide[1]);

                        if (strpos($val, "usuario") || strpos($val, "exemplo")) {
                            $comm = true;
                            break;
                        } else {
                            $comm = false;
                        }

                        if (!empty(trim($prop)) && !empty(trim($val))) {
                            $obj_addmac->$prop = $val;
                        } else {
                            $obj_addmac->$prop = "N/D";
                        }


                        $ip_procurado = preg_match("/^$new_ipv4$/", $val);

                        if ($ipv4nilla != $new_ipv4) {
                            if ($ip_procurado) {
                                $status += 1;
                                $detalhe = "O Endereço IPv4 informado já existe.";
                                echo resultado($status, $detalhe);
                                exit;
                            }
                        }
                    }
                }

                if ($comm) {
                    continue;
                }


                if ($ipv4nilla == $obj_addmac->ipv4) {
                    $obj_addmac->nome = $new_nome;
                    $obj_addmac->setor = $new_setor;
                    $obj_addmac->ipv4 = $new_ipv4;
                    $obj_addmac->mac = $new_mac;
                }

                $teste[$i] = $obj_addmac;
                $i++;
            }
        }

        if ($handle = fopen($addmac, 'w+')) {
            $arquivo = fopen($addmac, 'w+');
        } else {
            $arquivo = 0;
            $status += 1;
            $detalhe = "Não foi possível abrir o arquivo para gravação. <br> Codigo: ADAG02";
        }

        //print_r($teste);
        if ($status == 0) {
            $device;
            $i = 0;


            foreach ($teste as $objs) {
                foreach ($objs as $key => $line) {
                    $device .= $key . " = " . $line . ";\n";
                }
                $device .= ";;;\n";
            }

            if ($handle = fwrite($arquivo, $device)) {
                $status += 0;
            } else {
                $status += 1;
                $detalhe = "Não foi possível abrir o arquivo para gravação. <br> Codigo: ADAG03";
            }
        }

        echo resultado($status, $detalhe);

    endif;

    if ($action == "del") :

        $ipv4 = $_POST['ipv4'];
        $addmac_content = file_get_contents($addmac);
        $lista_dispositivos = preg_split("/;;;/", $addmac_content);
        $i = 0;

        foreach ($lista_dispositivos as $dispositivo_addmac) {


            $excluido = false;
            $dispositivo_addmac = trim($dispositivo_addmac);

            if (!empty($dispositivo_addmac) && strlen($dispositivo_addmac) != 0) {

                $obj_addmac = new StdClass();
                $array_data = explode(";", $dispositivo_addmac);

                foreach ($array_data as $data) {


                    if (!empty(trim($data))) {

                        $divide = explode("=", $data);
                        $prop = str_replace(' ', '', trim($divide[0]));
                        $val = trim($divide[1]);

                        if ($ipv4 == $val) {
                            $excluido = true;
                        }

                        if ($val == "usuario" || $val == "exemplo") {
                            $comm = true;
                            break;
                        } else {
                            $comm = false;
                        }

                        if (!empty(trim($prop)) && !empty(trim($val))) {
                            $obj_addmac->$prop = $val;
                        } else {
                            $obj_addmac->$prop = "N/D";
                        }
                    }
                }
                if ($comm) {
                    continue;
                }

                if ($excluido === true) {
                    $status += 0;
                    continue;
                } else {
                    $teste[$i] = $obj_addmac;
                    $i++;
                }
            }
        }

        if ($handle = fopen($addmac, 'w+')) {
            $arquivo = fopen($addmac, 'w+');
        } else {
            $arquivo = 0;
            $status += 1;
            $detalhe = "Não foi possível abrir o arquivo para gravação. <br> Codigo: ADAG02";
        }


        if ($status == 0) {
            $device = $device_example;

            foreach ($teste as $objs) {
                foreach ($objs as $key => $line) {
                    $device .= $key . " = " . $line . ";\n";
                }
                $device .= ";;;\n";
            }

            if ($handle = fwrite($arquivo, $device)) {
                $status += 0;
            } else {
                $status += 1;
                $detalhe = "Não foi possível abrir o arquivo para gravação. <br> Codigo: ADAG03";
            }
        }

        echo resultado($status, $detalhe);

    endif;
}
