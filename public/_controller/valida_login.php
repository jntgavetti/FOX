<?php
$msg = "";
$banco = new PDO('sqlite:' . dirname(__DIR__) . '/_model/hfox.db');
function logout($erro)
{


    if ($erro == 1) {
        $msg = "Usuário desativado.";
    } else {
        $msg = "Usuário ou senha incorretos.";
    }

    echo $msg;
    exit;
}



// Verifica se houve POST e se o usuário ou a senha é(são) vazio(s)
if (empty($_POST['usuario']) || empty($_POST['senha'])) {
    logout(2);
} else {

    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];
    $requisicao = $banco->query("SELECT id_usuario, nome, usuario, senha, nivel, status FROM login WHERE usuario = '$usuario' AND senha = '$senha' LIMIT 1");
    $listagem = $requisicao->fetchAll();

    if(count($listagem) == 0){
        logout(2);
    }else{
        foreach ($listagem as $campo) {
            $status = $campo['status'];
    
            if ($campo['usuario'] == $usuario and $campo['senha'] == $senha) {
    
                if ($status == 1) {
    
                    // Se a sessão não existir, inicia uma
                    if (!isset($_SESSION)) session_start();
                    $_SESSION['id_usuario'] = $campo['id_usuario'];
                    $_SESSION['nome'] = $campo['nome'];
                    $_SESSION['usuario'] = $campo['usuario'];
                    $_SESSION['nivel'] = $campo['nivel'];
                    exit;
                } else {
                    logout(1);
                }
            } else {
                logout(2);
            }
        }
    }
    
}

