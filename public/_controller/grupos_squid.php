<?php

ini_set('display_errors', 1);
error_reporting('E_ALL');
require_once('ssh.php');
$action = $_POST['action'];
$dir_grupos = '../regras/proxy/grupos/';
$dir_politicas = '../regras/proxy/politicas/';

if ($action == "lista_categoria") {
    $nome_grupo = $_POST['arquivo'];

    $categorias = [];

    if ($nome_grupo == "nivel-a" || $nome_grupo == "nivel-b" || $nome_grupo == "nivel-c" || $nome_grupo == "nivel-d" || $nome_grupo == "todos-niveis") {
        $g_sites = $nome_grupo . '.bloq_sites';
        $g_palavra = $nome_grupo . '.bloq_palavras';
        $g_ip = $nome_grupo . '.bloq_ips';
    } else {
        $g_sites = $nome_grupo . '.lib_sites';
        $g_palavra = $nome_grupo . '.lib_palavras';
        $g_ip = $nome_grupo . '.lib_ips';
    }


    $arq_site = fopen($dir_politicas . $g_sites, 'r');
    $arq_palavra = fopen($dir_politicas . $g_palavra, 'r');
    $arq_ip = fopen($dir_politicas . $g_ip, 'r');
    $arq_ext = fopen($dir_politicas . $g_ext, 'r');


    function gera_html($arquivo, $tipo)
    {
        $i = 0;
        $tipo = [];
        if ($arquivo != 0) {
            while (!feof($arquivo)) {

                $linha = fgets($arquivo);
                $linha = str_replace('', '', trim($linha));
                
                if (strlen(trim($linha)) != 0) {

                    if ($linha[0] == "#") {
                        $tipo[$i] =
                            "</div>
                        <div class='div_topico'>
                            <div class='draggable'>
                                <div class=''>
                                    <h5>Solte aqui</h5><br>
                                    <i class='fa fa-arrow-alt-circle-down fa-2x'></i>
                                </div>
                            </div>
                            <div class='div_info_topico'>
                                <input type='text' value='$linha' class='form-control titulo_topico' data-toggle='tooltip' data-placement='right' title='' required>
                                <button class='btn btn-sm btn_add_tp'><i class='fa fa-plus-circle'></i></button>
                                <button class='btn btn-sm btn_del'><i class='fa fa-times-circle'></i></button>
                            </div>";
                        $i++;
                    } else {
                       
                        if ($linha != "sitepadraoh2" && $linha != "palavrapadraoh2" && $linha != "169.254.254.255") {

                            $tipo[$i] =
                                "<div class='div_info_linha'>
                                    <span class='btn btn-sm btn_move'><i class='fa fa-arrows-alt'></i></span>
                                    <input type='text' value='$linha' class='form-control linha_topico defou orfao' data-toggle='tooltip' data-placement='right' title='' required>
                                    <button class='btn btn-sm btn_add'><i class='fa fa-plus-circle'></i></button>
                                    <button class='btn btn-sm btn_del'><i class='fa fa-times-circle'></i></button>
                                </div>";
                            $i++;
                        }
                    }
                }
            }
        }


        return $tipo;
    }


    $categorias["sites"] = gera_html($arq_site, "sites");
    $categorias["palavras"] = gera_html($arq_palavra, "palavras");
    $categorias["ips"] = gera_html($arq_ip, "ips");
    $categorias["extensoes"] = gera_html($arq_ext, "extensoes");

    echo json_encode($categorias);
}

if ($action == "lista_grupos") {

    if (isset($_POST['tipo_grupo'])) {
        $tipo_grupo = $_POST['tipo_grupo'];
    }

    $dir = "../regras/proxy/grupos/";
    $ext_info = array("nome", "setor", "ipv4", "ipv6", "mac");
    $grupos_perso = array(".", "..", "_bloq_proxy", "_lib_proxy", "politicas", "todos-niveis");


    $grupos = array();
    $i = 0;

    if ($handle = opendir($dir_grupos)) {

        while ($entry = readdir($handle)) {

            $ext = strtolower(pathinfo($entry, PATHINFO_EXTENSION));

            if (!in_array($ext, $ext_info)) {

                if (!in_array($entry, $grupos_perso)) {

                    $grupos[$i] = $entry;
                }
            }
            $i++;
        }
        closedir($handle);
    }
    sort($grupos);
    echo json_encode($grupos);
}

if ($action == "salvar") {
    $obj_cate = $_POST['dados'];
    $old_grupo = $_POST['old_grupo'];
    $grupo = $_POST['grupo'];

    $obj_cate = json_decode($obj_cate);
    $sites = $obj_cate->sites;
    $palavras = $obj_cate->palavras;
    $ips = $obj_cate->ips;
    $downloads = $obj_cate->downloads;


    foreach ($sites as $site) {
        $linhas_sites .= $site . "\n";
    }
    foreach ($palavras as $palavra) {
        $linhas_palavras .= $palavra . "\n";
    }
    foreach ($ips as $ip) {
        $linhas_ips .= $ip . "\n";
    }

    if ($grupo == "nivel-a" || $grupo == "nivel-b" || $grupo == "nivel-c" || $grupo == "nivel-d" || $grupo == "todos-niveis") {
        $g_sites = $grupo . '.bloq_sites';
        $g_palavra = $grupo . '.bloq_palavras';
        $g_ip = $grupo . '.bloq_ips';
    } else {
        $g_sites = $grupo . '.lib_sites';
        $g_palavra = $grupo . '.lib_palavras';
        $g_ip = $grupo . '.lib_ips';
        $g_down = $grupo . '.downloads';
    }


    if ($old_grupo != $grupo) {
        rename($dir_grupos . $old_grupo, $dir_grupos . $grupo);
    }

    $found = 0;
    if ($handle = opendir($dir_grupos)) {
        while ($entry = readdir($handle)) {
            if ($grupo == $entry) {
                $found++;
                break;
            }
        }
        if ($found == 0) {
            $arq = fopen($dir_grupos . $grupo, 'a');
        }
    }


    $grupo_sites = $dir_politicas . $g_sites;
    $grupo_palavras = $dir_politicas . $g_palavra;
    $grupo_ips = $dir_politicas . $g_ip;
    $grupo_down = $dir_politicas . $g_down;

    $proc = "";


    if ($handle = fopen($grupo_sites, 'w+')) {
        fwrite($handle, $linhas_sites);
        $st += 0;
    } else {
        $proc .= "<p class='status'><span class='status_notok'> Erro ao salvar alterações. Erro: EG001</span></p>";
    }

    if ($handle = fopen($grupo_palavras, 'w+')) {
        fwrite($handle, $linhas_palavras);
        $st += 0;
    } else {
        $proc .= "<p class='status'><span class='status_notok'> Erro ao salvar alterações. Erro: EG002</span></p>";
    }

    if ($handle = fopen($grupo_ips, 'w+')) {
        fwrite($handle, $linhas_ips);
        $st += 0;
    } else {
        $proc .= "<p class='status'><span class='status_notok'> Erro ao salvar alterações. Erro: EG003</span></p>";
    }

    if ($grupo != "nivel-a" && $grupo != "nivel-b" && $grupo != "nivel-c" && $grupo != "nivel-d" && $grupo != "todos-niveis") {
        if ($handle = fopen($grupo_down, 'w+')) {
            fwrite($handle, $downloads);
            $st += 0;
        } else {
            $proc .= "<p class='status'><span class='status_notok'> Erro ao salvar alterações. Erro: EG004</span></p>";
        }
    }

    $exec_squid = shell_exec("sudo php /opt/hsistema/scripts/squid.php");

    $arr_processamento = [];
    $arr_processamento["status"] = $st;
    $arr_processamento["detalhe"] = $proc;
    echo json_encode($arr_processamento);
}

if ($action == "excluir") {
    $st = 0;

    if (isset($_POST['grupo'])) {
        $grupo = $_POST['grupo'];
    }

    if ($handle = opendir($dir_grupos)) {

        while ($entry = readdir($handle)) {
            $grupo_reg = "/$grupo$/";

            $ret = preg_match($grupo_reg, $entry);
            if ($ret) {
                $rem = unlink($dir_grupos . $entry);
                if ($rem) {
                    $st += 0;
                } else {
                    $st++;
                    $dt = "Erro ao remover grupo.";
                }
                break;
            }
            $i++;
        }
        closedir($handle);
    }

    if ($handle = opendir($dir_politicas)) {

        while ($entry = readdir($handle)) {
            $pol_reg = "/$grupo$|$grupo\.[\w\d]+/";

            $ret = preg_match($pol_reg, $entry);


            if ($ret) {
                $rem = unlink($dir_politicas . $entry);

                if ($rem) {
                    $st += 0;
                } else {
                    $st++;
                    $dt = "Erro ao remover políticas de grupo.";
                }
            }

            $i++;
        }
        closedir($handle);
    }
    $arr_processamento = [];
    $arr_processamento["status"] = $st;
    $arr_processamento["detalhe"] = $dt;
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
