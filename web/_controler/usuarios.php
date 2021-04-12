<?php

ini_set('display_errors', 1);
error_reporting('E_ALL');
require_once('letra_numero.php');
$grupos_proxy = array();
$grupos_fw = array();
$array_pos_edicao = array();
$count = 0;
$status = 0;

function descobreIP($ip)
{
    $ip_cortado = preg_split("/[\s.]+/", $ip);
    return intval($ip_cortado[3]);
}

if (isset($_POST['nome'])) {
    $nome = $_POST['nome'];
    if (empty($nome) || $nome == "não definido") {
        $nome = "-";
    }
}

if (isset($_POST['setor'])) {
    $setor = $_POST['setor'];
    if (empty($setor) || $setor == "não definido") {
        $setor = "-";
    }
}

if (isset($_POST['ip_original'])) {
    $ip_original = $_POST['ip_original'];
}

if (isset($_POST['ip'])) {
    $ip = $_POST['ip'];
} else {
    $ip = $ip_original;
}


if (isset($_POST['mac'])) {
    $mac = $_POST['mac'];
}



if (isset($_POST['g_proxy'])) {
    $info_proxy = $_POST['g_proxy'];
    $grupos_proxy = $_POST['g_proxy'];
    $grupos_proxy = explode(',', $grupos_proxy);
}

if (isset($_POST['acesso_proxy'])) {
    $acesso_proxy = $_POST['acesso_proxy'];
    if ($acesso_proxy == "l_total") {
        $proxy_liberado = true;
    }
}

if (isset($_POST['web'])) {
    $web = $_POST['web'];
    $grupos_fw[0] = $web;
}

if (isset($_POST['mail'])) {
    $mail = $_POST['mail'];
    $grupos_fw[1] = $mail;
}

if (isset($_POST['proxy'])) {
    $proxy = $_POST['proxy'];
    $grupos_fw[2] = $proxy;
}

if (isset($_POST['ts'])) {
    $ts = $_POST['ts'];
    $grupos_fw[3] = $ts;
}

if (isset($_POST['ftp'])) {
    $ftp = $_POST['ftp'];
    $grupos_fw[4] = $ftp;
}

if (isset($_POST['todos'])) {
    $todos = $_POST['todos'];
    $grupos_fw[5] = $todos;
}


$addmac = "../_model/addmac";


if (isset($_POST['action'])) {

    if ($_POST['action'] == 'proxy') {

        if (isset($_POST['ip_js'])) {
            $ip_js = $_POST['ip_js'];
        }
        $addmac = "../_model/addmac";
        $count = 0;
        $existe = 0;

        if ($handle = fopen($addmac, "r")) {
            $f_addmac = fopen($addmac, "r");
        } else {
            $f_addmac = 0;
        }

        while (!feof($f_addmac)) {

            $linha = fgets($f_addmac);

            $procura = preg_match("/;$ip_js;/", $linha);

            if ($procura) {

                $array_linha = explode(";", $linha);

                foreach ($array_linha as $indice => $letra) {
                    if ($letra != "-") {
                        echo "
                            <div class='div_grupo_existe'>
                            <span class='p_grupo_existe'>Grupo $letra </span>
                            <span class='grupo_delete'><i class='fas fa-times-circle text-danger'></i></span>
                            <br><br>
                            <input type='hidden' name='' value='$indice'>
                            </div>
                        ";
                        $existe += 1;
                    }
                    if ($indice == 15) break;
                }
            }

            $count++;
        }
        if ($existe == 0) {
            echo "<p class='aviso_grupo_existe'>Não há grupos cadastrados.</p>";
            exit;
        }
    }
    fclose($f_addmac);
}

if (isset($_POST['action'])) {

    if ($_POST['action'] == 'firewall') {

        if (isset($_POST['ip_js'])) {
            $ip_js = $_POST['ip_js'];
        }
        $addmac = "../_model/addmac";
        $count = 0;
        $existe = 0;
        $arr_grupos = array();
        $tess = array();

        if ($handle = fopen($addmac, "r")) {
            $f_addmac = fopen($addmac, "r");
        } else {
            $f_addmac = 0;
        }

        while (!feof($f_addmac)) {

            $linha = fgets($f_addmac);

            $procura = preg_match("/;$ip_js;/", $linha);

            if ($procura) {
                $cont_procura = 0;
                $array_linha = explode(";", $linha);
                foreach ($array_linha as $indice => $letra) {
                    if ($indice > 15 && $letra != "-") {
                        $arr_grupos[$cont_procura] = $indice;
                        $existe += 1;
                    }
                    if ($indice == 25) break;
                    $cont_procura++;
                }
            }

            $count++;
        }
        echo json_encode($arr_grupos);
    }
    fclose($f_addmac);
}

if (isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action == "add") :

        if (empty($ip) || !isset($ip)) {
            echo "Campo ip vazio";
            exit;
        }

        // Abre arquivo para leitura

        if ($handle = fopen('../_model/addmac', 'r')) {
            $f_addmac = fopen('../_model/addmac', 'r');
        } else {
            echo "Não foi possível abrir o arquivo addmac para leitura. AL001";
            exit;
        }


        $str_procura = null;
        $status = null;


        while (!feof($f_addmac)) { // Enquanto o arquivo não acabar faz um loop


            // Variavel recebe a linha atual
            $linha_arquivo = fgets($f_addmac);


            // Procura o ip na linha atual
            $str_procura = preg_match("/;$ip;/", $linha_arquivo);


            if ($str_procura) {
                echo "Endereço IP já cadastrado";
                exit;
            }
            $count++;
        }


        $addmac_grupos_tracos = explode(';', "-;-;-;-;-;-;-;-;-;-;-;-;-;-;-;-;-;-;-;-;-;-;-;-;-;-;-;-;-;-;");
        $addmac_grupos_tracos[28] = $nome;
        $addmac_grupos_tracos[29] = $setor;
        $addmac_grupos_tracos[27] = $ip;
        $addmac_grupos_tracos[26] = $mac;

        // Itera sobre todas as palavras da linha 
        foreach ($addmac_grupos_tracos as $indice => $addmac_string) {
            if ($indice <= 15) {
                for ($i = 0; $i <= 14; $i++) {
                    $gp = $grupos_proxy[$i];

                    if (!isset($gp)) {
                        $addmac_grupos_tracos[$indice] = "-";
                        continue;
                    } else {
                        if (array_key_exists($gp, $addmac_grupos_tracos)) {
                            $letra = troca_letra_numero($gp);
                            $addmac_grupos_tracos[$gp] = $letra;
                        } else {
                            $addmac_grupos_tracos[$indice] = '-';
                        }
                    }
                }
            }


            if ($indice > 15) {
                for ($i = 0; $i <= 5; $i++) {
                    $gfw = $grupos_fw[$i];

                    if (!isset($gfw) || $gfw == null || $gfw == "") {
                        $addmac_grupos_tracos[$indice] = "-";
                        continue;
                    } else {
                        if (array_key_exists($gfw, $addmac_grupos_tracos)) {
                            $letra = troca_letra_numero($gfw);
                            $addmac_grupos_tracos[$gfw] = $letra;
                        } else {
                            $addmac_grupos_tracos[$gfw] = '-';
                        }
                    }
                }
            }

            // Se os grupos acabarem pare o loop
            if ($indice == 25) {
                break;
            }
        }

        if ($proxy_liberado && strlen($info_proxy) == 0) {
            $addmac_grupos_tracos[15] = "P";
        } else {
            $addmac_grupos_tracos[15] = "-";
        }



        $linha_pronta = "\n".implode(";", $addmac_grupos_tracos);


        // Fecha o arquivo addmac
        fclose($f_addmac);





        // Abre ou cria o arquivo principal para escrita e zera ele
        if ($handle = fopen($addmac, 'a+')) {
            $arquivo = fopen($addmac, 'a+');
        } else {
            $arquivo = 0;
            echo "Não foi possível abrir o arquivo para gravação. <br> Codigo: ADAG02";
            exit;
        }

    
        if ($handle = fwrite($arquivo, $linha_pronta)) {
            $status += 1;
        } else {
            $status += 0;
        }


        if ($status != 0) {
            echo 1;
            exit;
        } else {
            echo "Não foi possível abrir o arquivo para gravação. <br> Codigo: ADAG03";
            exit;
        }


    endif;


    if ($action == "edit") :

        if (empty($ip_original) || !isset($ip_original)) {
            echo "Campo ip vazio";
            exit;
        }

        // Abre arquivo para leitura

        if ($handle = fopen('../_model/addmac', 'r')) {
            $f_addmac = fopen('../_model/addmac', 'r');
        } else {
            echo "Não foi possível abrir o arquivo addmac para leitura. EL001";
            exit;
        }





        // Enquanto o arquivo não acabar faz um loop


        $str_procura = null;
        $status = null;


        while (!feof($f_addmac)) {


            // Variavel recebe a linha atual
            $linha_arquivo = fgets($f_addmac);


            // Procura o ip na linha atual

            $str_procura = preg_match("/;$ip_original;/", $linha_arquivo);




            // Se encontrar, incrementa o contador do array backup e força o loop ir para proxima iteração
            // Isso faz com que o array novo nao receba a linha atual que é a linha que a gente quer excluir
            if ($str_procura) {
                $status_procura = 1;


                $addmac_grupos_tracos = explode(';', $linha_arquivo);

                // Editando informaçoes do usuario
                $addmac_grupos_tracos[28] = $nome;
                $addmac_grupos_tracos[29] = $setor;
                $addmac_grupos_tracos[27] = $ip;
                $addmac_grupos_tracos[26] = $mac;

                // Itera sobre todas as palavras da linha 
                foreach ($addmac_grupos_tracos as $indice => $addmac_string) {


                    if ($indice <= 15) {
                        for ($i = 0; $i <= 14; $i++) {
                            $gp = $grupos_proxy[$i];

                            if (!isset($gp)) {
                                $addmac_grupos_tracos[$indice] = "-";
                                continue;
                            } else {
                                if (array_key_exists($gp, $addmac_grupos_tracos)) {
                                    $letra = troca_letra_numero($gp);
                                    $addmac_grupos_tracos[$gp] = $letra;
                                } else {
                                    $addmac_grupos_tracos[$indice] = '-';
                                }
                            }
                        }
                    }


                    if ($indice > 15) {
                        for ($i = 0; $i <= 5; $i++) {
                            $gfw = $grupos_fw[$i];

                            if (!isset($gfw) || $gfw == null || $gfw == "") {
                                $addmac_grupos_tracos[$indice] = "-";
                                continue;
                            } else {
                                if (array_key_exists($gfw, $addmac_grupos_tracos)) {
                                    $letra = troca_letra_numero($gfw);
                                    $addmac_grupos_tracos[$gfw] = $letra;
                                } else {
                                    $addmac_grupos_tracos[$gfw] = '-';
                                }
                            }
                        }
                    }

                    // Se os grupos acabarem pare o loop
                    if ($indice == 25) {
                        break;
                    }
                }

                if ($proxy_liberado && strlen($info_proxy) == 0) {
                    $addmac_grupos_tracos[15] = "P";
                } else {
                    $addmac_grupos_tracos[15] = "-";
                }



                $linha_pronta = implode(";", $addmac_grupos_tracos);
                $array_pos_edicao[$count] = $linha_pronta;
            } else { // Se nao alimenta o novo array com a linha atual
                $array_pos_edicao[$count] = $linha_arquivo;
            }
            $count++;
        }


        // Fecha o arquivo addmac
        fclose($f_addmac);

        if ($status_procura != 0) {
            if (count($array_pos_edicao) != 0) {


                // Abre ou cria o arquivo principal para escrita e zera ele
                if ($handle = fopen($addmac, 'w+')) {
                    $arquivo_zerado = fopen($addmac, 'w+');
                } else {
                    $arquivo_zerado = 0;
                    echo "Não foi possível abrir o arquivo para gravação. <br> Codigo: EDAG02";
                    exit;
                }

                for ($i = 0; $i < count($array_pos_edicao); $i++) {
                    if ($handle = fwrite($arquivo_zerado, $array_pos_edicao[$i])) {
                        $status += 1;
                    } else {
                        $status += 0;
                    }
                }
            } else {
                echo "Arquivo vazio. Codigo: EDAV01";
                exit;
            }

            if ($status != 0) {
                echo 1;
                exit;
            } else {
                echo "Não foi possível abrir o arquivo para gravação. <br> Codigo: EDAG03";
                exit;
            }
        } else {
            echo "Não foi possível encontrar o usuário.";
            exit;
        }

    endif;

    if ($action == "del") :

        // Abre arquivo para leitura
        if ($handle = fopen('../_model/addmac', 'r')) {
            $f_addmac = fopen('../_model/addmac', 'r');
        } else {
            echo "Não foi possível abrir o arquivo addmac para leitura. EL001";
            exit;
        }

        $addmac_old = "../_model/addmac.old";
        $array_backup = array();
        $array_pos_delecao = array();
        $count_bkp = 0;
        $count_novo = 0;
        $status = 0;
        $status_procura = 0;

        // Enquanto o arquivo não acabar faz um loop

        while (!feof($f_addmac)) {

            // Variavel recebe a linha atual
            $linha_arquivo = fgets($f_addmac);

            // Cria um array de backup caso precise recuperar as linhas excluidas
            $array_backup[$count_bkp] = $linha_arquivo;

            // Procura o ip na linha atual
            $str_procura = preg_match("/\b$ip\b/", $linha_arquivo);


            // Se encontrar, incrementa o contador do array backup e força o loop ir para proxima iteração
            // Isso faz com que o array novo nao receba a linha atual que é a linha que a gente quer excluir

            if ($str_procura === 1) {
                $count_bkp++;
                $status_procura = 1;
                continue;
            } else { // Se nao alimenta o novo array com a linha atual
                $array_pos_delecao[$count_novo] = $linha_arquivo;
                $count_novo++;
            }
            $count_bkp++;
        }



        if ($status_procura != 1) {
            echo "Não foi possível encontrar o usuário.";
            exit;
        }

        // Fecha o arquivo addmac
        fclose($f_addmac);



        if (count($array_backup) != 0) {
            // Abre ou cria o arquivo de backup para escrita e zera ele
            if ($handle = fopen($addmac_old, 'w+')) {
                $arquivo_backup = fopen($addmac_old, 'w+');
            } else {
                echo "Erro na abertura do arquivo de backup. <br> Codigo: EDAG01";
                exit;
            }

            // Para cada indice no array, insere as linhas coletadas anteriormente
            for ($i = 0; $i < count($array_backup); $i++) {
                fwrite($arquivo_backup, $array_backup[$i]);
            }
        } else {
            echo "Array vazio. Codigo: EDAV02";
            exit;
        }





        if (count($array_backup) === 1) {

            if ($handle = fopen($addmac, 'w+')) {
                $status += 1;
            } else {
                $status += 0;
                echo "Não foi possível abrir o arquivo para gravação. <br> Codigo: EDAG02";
                exit;
            }
        } else {
            if (count($array_pos_delecao) !== 0) {

                // Abre ou cria o arquivo principal para escrita e zera ele
                if ($handle = fopen($addmac, 'w+')) {
                    $arquivo_zerado = fopen($addmac, 'w+');
                } else {
                    $arquivo_zerado = 0;
                    echo "Não foi possível abrir o arquivo para gravação. <br> Codigo: EDAG02";
                    exit;
                }

                for ($i = 0; $i < count($array_pos_delecao); $i++) {
                    if ($handle = fwrite($arquivo_zerado, $array_pos_delecao[$i])) {
                        $status += 1;
                    } else {
                        $status += 0;
                    }
                }
            } else {
                echo "Array vazio. Codigo: EDAV01";
                exit;
            }
        }



        if ($status != 0) {
            echo 1;
            exit;
        } else {
            echo "Não foi possível abrir o arquivo para gravação. <br> Codigo: EDAG03";
            exit;
        }

    endif;


    if ($action == "undo") :
        if ($handle = rename("../_model/addmac", "../_model/addmac_bak")) {
            if ($handle = rename("../_model/addmac.old", "../_model/addmac")) {
                echo 1;
            } else {
                rename("../_model/addmac_bak", "../_model/addmac");
                echo "Falha ao tentar desfazer alteração. <br> Codigo: EURA02";
                exit;
            }
        } else {
            echo "Falha ao tentar desfazer alteração. <br> Codigo: EURA01";
            exit;
        }
    endif;

    if ($action == "apply") :
        require_once("../_model/info_ssh.php");

        $conne = ssh2_connect('172.17.0.1', 5322);
        if (!$conne) die('Falha na conexão SSH !');


        ssh2_auth_password($conne, $use, $passwd);

        $cmd = ssh2_exec($conne, "sudo /opt/hsistema/scripts/grupo.sh");
        $err_stream = ssh2_fetch_stream($cmd, SSH2_STREAM_STDERR);
        $dio_stream = ssh2_fetch_stream($cmd, SSH2_STREAM_STDIO);

        stream_set_blocking($err_stream, true);
        stream_set_blocking($dio_stream, true);

        $result_err = stream_get_contents($err_stream);
        $result_dio = stream_get_contents($dio_stream);

        if (strlen($result_err) == 0) {
            echo 1;
        } else {
            echo $result_err;
        }
    endif;
}
