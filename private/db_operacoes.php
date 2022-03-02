<?php


class Operacoes{
    
    private $banco;
    private $id;
    private $tabela;
    private $coluna;
    private $coluna_comparacao;
    private $dado;
    private $dado_comparacao;
   
    
    public function setId($id){$this->id = $id;}
    public function setTabela($tabela){$this->tabela = $tabela;}
    public function setColuna($coluna){$this->coluna = $coluna;}
    public function setColunaComparacao($coluna_comparacao){$this->coluna_comparacao = $coluna_comparacao;}
    public function setDado($dado){$this->dado = $dado;}
    public function setDadoComparacao($dado_comparacao){$this->dado_comparacao = $dado_comparacao;}
    


    public function listaTudo($table, $column){
        $this->banco = new PDO('sqlite:'. dirname(__DIR__).'/_model/hfox.db');
        $requisicao = $this->banco->query("SELECT $column FROM $table");
        $campos = $requisicao->fetchAll();
        return $campos;
        $this->banco = null;
    }


    public function lista_maximo(){
        try{
            $con = $this->banco = new PDO('sqlite:'. dirname(__DIR__).'/_model/hfox.db');
            $stm = $con->query("SELECT max($this->coluna) FROM $this->tabela LIMIT 1");
           

            $resultado = $stm->fetchAll();
            
            return $resultado;
        
        }catch(PDOException $exc){
            echo $exc->getMessage();
        }finally{
            $con = null;
        }
    }

    public function filtragem(){

        try{
            $con = $this->banco = new PDO('sqlite:'. dirname(__DIR__).'/_model/hfox.db');
            $stm = $con->query("SELECT $this->id, $this->coluna FROM $this->tabela WHERE $this->coluna = '$this->dado' LIMIT 1");
           

            $resultado = $stm->fetchAll();
            
            return $resultado;
        
        }catch(PDOException $exc){
            echo $exc->getMessage();
        }finally{
            $con = null;
        }
        
        
    }

    public function alterar(){

        try{
            $con = $this->banco = new PDO('sqlite:'. dirname(__DIR__).'/_model/hfox.db');
            $stm = $con->query("UPDATE $this->tabela SET $this->coluna = '$this->dado' WHERE $this->coluna = '$this->dado_comparacao'");
            
                
            
           

            //$resultado = $stm->fetchAll();
            
            return $stm;
        
        }catch(PDOException $exc){
            echo $exc->getMessage();
        }finally{
            $con = null;
        }
        
        
    }

    
}
