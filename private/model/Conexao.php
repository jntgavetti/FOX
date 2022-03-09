<?php 

class Connection{

    private $host = 'localhost';
    private $dbname = 'fox';
    private $user = 'root';
    private $passwd = '';


    public function connect(){

        try{
            $connection = new PDO(
                "mysql:host=$this->host;dbname=$this->dbname",
                "$this->user",
                "$this->passwd"
            );

            return $connection;

        } catch(PDOException $e){
            echo '<p>'.$e->getMessage().'</p>';
        }
    }
}

?>