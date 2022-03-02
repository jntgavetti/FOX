<?php


class Manipulacao
{

    private $banco;
    private $id;
    private $descricao;
    private $origem;
    private $portaOrigem;
    private $destino;
    private $portaDestino;
    private $protocolo;
    private $status;
    private $nivel;

    function getId()
    {
        return $this->id;
    }
    function setId($id)
    {
        $this->id = $id;
    }
    function getDescricao()
    {
        return $this->descricao;
    }
    function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }
    function getOrigem()
    {
        return $this->origem;
    }
    function setOrigem($origem)
    {
        $this->origem = $origem;
    }
    function getPortaOrigem()
    {
        return $this->portaOrigem;
    }
    function setPortaOrigem($portaOrigem)
    {
        $this->portaOrigem = $portaOrigem;
    }
    function getDestino()
    {
        return $this->destino;
    }
    function setDestino($destino)
    {
        $this->destino = $destino;
    }
    function getPortaDestino()
    {
        return $this->portaDestino;
    }
    function setPortaDestino($portaDestino)
    {
        $this->portaDestino = $portaDestino;
    }
    function getProtocolo()
    {
        return $this->protocolo;
    }
    function setProtocolo($protocolo)
    {
        $this->protocolo = $protocolo;
    }
    function getStatus()
    {
        return $this->status;
    }
    function setStatus($status)
    {
        $this->status = $status;
    }
    function getNivel()
    {
        return $this->nivel;
    }
    function setNivel($nivel)
    {
        $this->nivel = $nivel;
    }


    public function listar($nivel)
    {
        $this->banco = new PDO('sqlite:' . dirname(__DIR__) . '/_model/hfox.db');
        $requisicao = $this->banco->query("SELECT * FROM redireciona_portas");
        $campos = $requisicao->fetchAll();
        return $campos;
        $this->banco = null;
    }

    public function adicionar()
    {
        $con = $this->banco = new PDO('sqlite:' . dirname(__DIR__) . '/_model/hfox.db');

        try {
            $comando = "INSERT INTO redireciona_portas (id_redirec, descricao, origem, portaOrigem, destino, portaDestino, protocolo, status, nivel) VALUES (null, '$this->descricao', '$this->origem', '$this->portaOrigem', '$this->destino', '$this->portaDestino', '$this->protocolo', '$this->status', '$this->nivel')";
            return 1;
        } catch (PDOException $exc) {
            echo $exc->getMessage();
        } finally {
            $con->exec($comando);
            $con = null;
        }
    }



    public function deletar($table)
    {
        $con = $this->banco = new PDO('sqlite:' . dirname(__DIR__) . '/_model/hfox.db');
        $query = $con->query("SELECT nivel FROM redireciona_portas WHERE id_redirec = $this->id");
        $result = $query->fetchAll();
       
        
            if ($this->id == '-1') {
                if ($this->nivel == "admin") {
                    $comando = "DELETE FROM $table";
                } else {
                    if ($this->nivel == 'cliente') {
                        $comando = "DELETE FROM $table WHERE nivel = 'cliente'";
                    } else {
                        exit;
                    }
                }
            } else {
                $nivel_tabela = $result[0]['nivel'];
                if ($nivel_tabela == $this->nivel || $nivel_tabela == 'cliente' && $this->nivel == 'admin') {
                    $comando = "DELETE FROM redireciona_portas WHERE id_redirec = $this->id";
                } else {
                    exit;
                }
            }
            
        try {
           $con->exec($comando);
        } catch (PDOException $exc) {
            echo $exc->getMessage();
        } finally {
            $con = null;
        }
    }

    public function editar()
    {
        $con = $this->banco = new PDO('sqlite:' . dirname(__DIR__) . '/_model/hfox.db');
        $query = $con->query("SELECT nivel FROM redireciona_portas WHERE id_redirec = $this->id");
        $result = $query->fetchAll();
        $nivel_tabela = $result[0]['nivel'];

        if ($nivel_tabela == $this->nivel || $nivel_tabela == 'cliente' && $this->nivel == 'admin') {

            $comando = "UPDATE redireciona_portas SET descricao = '$this->descricao', origem = '$this->origem', portaOrigem = '$this->portaOrigem', destino = '$this->destino', portaDestino = '$this->portaDestino', protocolo = '$this->protocolo', status = '$this->status' WHERE id_redirec = '$this->id'";
        } else {
            return false;
        }
        $con->exec($comando);
        $con->banco = null;
    }
}
