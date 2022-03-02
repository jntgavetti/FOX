<?php


class Provedor
{

    private $banco;
    private $id;
    private $interface;
    private $provedor;
    private $prioridade;
    private $modo_operacao;
    private $d_pppoe;
    private $user_pppoe;
    private $senha_pppoe;
    private $ip_valido;
    private $status;

    function getId()
    {
        return $this->id;
    }
    function setId($id)
    {
        $this->id = $id;
    }
    function getInterface()
    {
        return $this->interface;
    }
    function setInterface($interface)
    {
        $this->interface = $interface;
    }
    function getProvedor()
    {
        return $this->provedor;
    }
    function setProvedor($provedor)
    {
        $this->provedor = $provedor;
    }
    function getPrioridade()
    {
        return $this->prioridade;
    }
    function setPrioridade($prioridade)
    {
        $this->prioridade = $prioridade;
    }
    function getModoOperacao()
    {
        return $this->modo_operacao;
    }
    function setModoOperacao($modo_operacao)
    {
        $this->modo_operacao = $modo_operacao;
    }
    function getDPPPoe()
    {
        return $this->d_pppoe;
    }
    function setDPPPoe($d_pppoe)
    {
        $this->d_pppoe = $d_pppoe;
    }
    function getUserPPPoe()
    {
        return $this->user_pppoe;
    }
    function setUserPPPoe($user_pppoe)
    {
        $this->user_pppoe = $user_pppoe;
    }
    function getSenhaPPPoe()
    {
        return $this->senha_pppoe;
    }
    function setSenhaPPPoe($senha_pppoe)
    {
        $this->senha_pppoe = $senha_pppoe;
    }
    function getIpValido()
    {
        return $this->ip_valido;
    }
    function setIpValido($ip_valido)
    {
        $this->ip_valido = $ip_valido;
    }
    function getStatus()
    {
        return $this->status;
    }
    function setStatus($status)
    {
        $this->status = $status;
    }


    public function listar($provedor)
    {
        $this->banco = new PDO('sqlite:' . dirname(__DIR__) . '/_model/hfox.db');
        if($provedor == "-1"){
            $requisicao = $this->banco->query("SELECT id_provedor,interface,provedor,prioridade,modo_operacao,dispositivo_pppoe,usuario_pppoe,senha_pppoe,ip_valido,status FROM provedores");
        }else{
            $requisicao = $this->banco->query("SELECT id_provedor,interface,provedor,prioridade,modo_operacao,dispositivo_pppoe,usuario_pppoe,senha_pppoe,ip_valido,status FROM provedores WHERE id_provedor = $provedor");
        }
       
        $campos = $requisicao->fetchAll();
        return $campos;


        $this->banco = null;
    }

    public function adicionar()
    {
        $con = $this->banco = new PDO('sqlite:'. dirname(__DIR__).'/_model/hfox.db');
        try{
            $stm = $con->query("INSERT INTO provedores (id_provedor,interface,provedor,prioridade,prioridade_num, modo_operacao,dispositivo_pppoe,usuario_pppoe,senha_pppoe,ip_valido, status) VALUES (null, '$this->interface', '$this->provedor', '$this->prioridade', 1, '$this->modo_operacao', '$this->d_pppoe', '$this->user_pppoe', '$this->senha_pppoe', '$this->ip_valido', '$this->status')");
        }catch(PDOException $exc){
            echo $exc->getMessage();
        }finally{
            $con = null;
        }
        
     
    }


    public function deletar()
    {
        $this->banco = new PDO('sqlite:' . dirname(__DIR__) . '/_model/hfox.db');
        if ($this->id == '-1') {
            $comando = "DELETE FROM provedores";
        } else {
            $comando = "DELETE FROM provedores WHERE id_provedor = $this->id";
        }

        $this->banco->exec($comando);
        $this->banco = null;
    }

    public function editar()
    {
        $this->banco = new SQLite3(dirname(__DIR__).'/_model/hfox.db');
        $this->banco->enableExceptions(true);
        try{
            $this->banco->exec("UPDATE provedores SET interface = '$this->interface', provedor = '$this->provedor', prioridade = '$this->prioridade', modo_operacao = '$this->modo_operacao', dispositivo_pppoe = '$this->d_pppoe', usuario_pppoe = '$this->user_pppoe', senha_pppoe = '$this->senha_pppoe', ip_valido = '$this->ip_valido', status = $this->status WHERE id_provedor = '$this->id'");
        }catch(Exception $msg){
            die( $msg->getMessage() );
            return $msg;
        }
        
        
        $this->banco = null;
    }
}
