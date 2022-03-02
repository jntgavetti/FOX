<?php
class InterfaceService
{

    private $connection;


    public function __construct(Connection $connection)
    {
        $this->connection = $connection->connect();
    }

    public function list()
    {
        $query = "
        SELECT
            f.nome, f.tipo, f.funcao, f.mac, f.status, GROUP_CONCAT(DISTINCT i4.ipv4 SEPARATOR ',') ipv4, GROUP_CONCAT(DISTINCT i6.ipv6 SEPARATOR ',') ipv6
        FROM
            interfaces f
        LEFT JOIN
            ipv4 i4
        ON
            f.nome = i4.interface
        LEFT JOIN
            ipv6 i6
        ON
            f.nome = i6.interface
        GROUP BY f.nome;
        
        ";
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function count()
    {
    }

    public function create()
    {
    }

    public function update()
    {
    }

    public function delete()
    {
    }
}
