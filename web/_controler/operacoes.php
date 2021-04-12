<?php

require_once("../_model/db_operacoes.php");
$operacao = new Operacoes();



if(isset($_POST['db_id'])){
    $db_id = $_POST['db_id'];
    $operacao->setId($db_id);
}

if(isset($_POST['db_tabela'])){
    $db_tabela = $_POST['db_tabela'];
    $operacao->setTabela($db_tabela);
}

if(isset($_POST['db_coluna_comparacao'])){
    $db_coluna_comparacao = $_POST['db_coluna_comparacao'];
    $operacao->setColunaComparacao($db_coluna_comparacao);
}

if(isset($_POST['db_coluna'])){
    $db_coluna = $_POST['db_coluna'];
    $operacao->setColuna($db_coluna);
}

if(isset($_POST['db_dado'])){
    $db_dado = $_POST['db_dado'];
    $operacao->setDado($db_dado);
}

if(isset($_POST['db_dado_comparacao'])){
    $db_dado_comparacao = $_POST['db_dado_comparacao'];
    $operacao->setDadoComparacao($db_dado_comparacao);
}





if(isset($_POST['action'])){
    $action = $_POST['action'];
    

    switch($action){

        case "consulta_existencia":
            $requisicao = $operacao->filtragem();
            if(count($requisicao) != 0){
                echo json_encode($requisicao);
            }else{
                echo 0;
            }
        break;

        case "lista_maximo":
            $requisicao = $operacao->lista_maximo();
            if(count($requisicao) != 0){
                echo json_encode($requisicao);
            }else{
                echo 0;
            }
        break;

        case "altera":
            $requisicao = $operacao->alterar();
            echo json_encode($requisicao);
        break;

    }
    
    
   
}

?>