<?php


class Manipulacao{
    
    private $banco;
    private $id;
    private $dispositivo;
    private $usuario;
    private $setor;
    private $ip;
    private $mac;
    private $status;
    
    public function setId($id){$this->id = $id;}
    public function setDispositivo($dispositivo){$this->dispositivo = $dispositivo;}
    public function setUsuario($usuario){$this->usuario = $usuario;}
    public function setSetor($setor){$this->setor = $setor;}
    public function setIp($ip){$this->ip = $ip;}
    public function setMac($mac){$this->mac = $mac;}
    public function setStatus($status){$this->status = $status;}
    


    public function listar($table){
        $this->banco = new PDO('sqlite:'. dirname(__DIR__).'/_model/hfox.db');
        $requisicao = $this->banco->query("SELECT * FROM $table");
        $campos = $requisicao->fetchAll();
        return $campos;
        $this->banco = null;
    }

    public function adicionar(){
        $this->banco = new PDO('sqlite:'. dirname(__DIR__).'/_model/hfox.db');
        $this->banco->exec("INSERT INTO dispositivos (id_dispositivo, dispositivo, usuario, setor, ip, mac, status) VALUES (null, '$this->dispositivo', '$this->usuario', '$this->setor', '$this->ip', '$this->mac', $this->status)");
        $this->banco = null;
    }


    public function deletar(){
        $this->banco = new PDO('sqlite:'. dirname(__DIR__).'/_model/hfox.db');
        if($this->id == null){
            
                $comando = "DELETE FROM dispositivos";
        }else{
            $comando = "DELETE FROM dispositivos WHERE id_dispositivo = $this->id";
        }
        
        $this->banco->exec($comando);
        $this->banco = null;
    }

    public function editar(){
        $this->banco = new PDO('sqlite:'. dirname(__DIR__).'/_model/hfox.db');
        $this->banco->exec("UPDATE dispositivos SET dispositivo = '$this->dispositivo', usuario = '$this->usuario', setor = '$this->setor', ip = '$this->ip', mac = '$this->mac', status = $this->status WHERE id_dispositivo = $this->id");
        $this->banco = null;
    }
}
