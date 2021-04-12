<?php


class Manipulacao{
    
    private $banco;
    private $interface;
    private $tipo_placa;
    private $modo;
    private $class_placa;
    private $ip;
    private $mask;
    private $rede;
    private $bcast;
    private $mac;
    private $status;
    
    public function setInterface($interface){$this->interface = $interface;}
    public function setTipoPlaca($tipo_placa){$this->tipo_placa = $tipo_placa;}
    public function setModo($modo){$this->modo = $modo;}
    public function setClassPlaca($class_placa){$this->class_placa = $class_placa;}
    public function setIp($ip){$this->ip = $ip;}
    public function setMask($mask){$this->mask = $mask;}
    public function setRede($rede){$this->rede = $rede;}
    public function setBcast($bcast){$this->bcast = $bcast;}
    public function setMac($mac){$this->mac = $mac;}
    public function setStatus($status){$this->status = $status;}
    
    


    public function listar($int, $table){
        $this->banco = new PDO('sqlite:'. dirname(__DIR__).'/_model/hfox.db');
        
        if($int == null){
            $requisicao = $this->banco->query("SELECT * FROM $table WHERE classificacao = 'interna' ORDER BY ethernet");
        }else{
            $requisicao = $this->banco->query("SELECT * FROM $table WHERE ethernet = '$int'");
        }
        
        $campos = $requisicao->fetchAll();
        return $campos;
        $this->banco = null;
    }

    public function adicionar(){
        $this->banco = new SQLite3(dirname(__DIR__).'/_model/hfox.db');
        $this->banco->enableExceptions(true);
        try{
            $this->banco->exec("INSERT INTO interfaces VALUES ('$this->interface', '$this->tipo_placa', '$this->modo', 'interna', '$this->ip', '$this->mask', '', '$this->rede', '$this->bcast', '', '', '', $this->status)");
        }catch(Exception $msg){
            die( $msg->getMessage() );
            return $msg;
        }
        $this->banco = null;
    }


    public function deletar($table){
        $this->banco = new PDO('sqlite:'. dirname(__DIR__).'/_model/hfox.db');
        $comando = "DELETE FROM $table WHERE ethernet = '$this->interface'";
        $this->banco->exec($comando);
        $this->banco = null;
    }

    public function editar(){
        $this->banco = new SQLite3(dirname(__DIR__).'/_model/hfox.db');
        $this->banco->enableExceptions(true);
        try{
            $this->banco->exec("UPDATE interfaces SET ethernet='$this->interface', tipo_placa = '$this->tipo_placa',  modo = '$this->modo', classificacao = '$this->class_placa', ip='$this->ip', mascara='$this->mask', gateway = '', rede='$this->rede',broadcast='$this->bcast', dns1 = '', dns2 = '', mac = '', status = $this->status WHERE ethernet = '$this->interface'");
        }catch(Exception $msg){
            die( $msg->getMessage() );
            return $msg;
        }

        $this->banco = null;
    }
}
