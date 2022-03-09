<?php
class InterfaceService
{

    private $connection;
    private $iface;


    public function __construct(Connection $connection, Iface $iface)
    {
        $this->connection = $connection->connect();
        $this->iface = $iface;
    }

    public function list()
    {


        $query = "
        SELECT 
            a.interface, a.tipo, a.funcao, a.mac, a.status, i4.ipv4, i6.ipv6, a.interface_pai
        FROM 
            interfaces a
        LEFT JOIN
            interfaces b
        ON
            a.interface_pai LIKE b.interface+'%'
        LEFT JOIN 
            ipv4 i4
        ON  i4.interface = a.interface
        LEFT JOIN 
            ipv6 i6
        ON  i6.interface = a.interface
        ORDER BY LENGTH(b.interface);";


        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        
        $query = "
            SELECT 
                tipo
            FROM 
                interfaces a
            WHERE 
                a.interface = ?;
        ";
        
        $stmt = $this->connection->prepare($query);
        $stmt->bindValue(1, $this->iface->__get('iface'));
        $stmt->execute();
        $tipo = $stmt->fetch(PDO::FETCH_ASSOC);

        
         if($tipo['tipo'] == 'virtual'){
            $query = 
            "
                DELETE 
                FROM ipv4
                WHERE interface = ?;
                DELETE
                FROM interfaces
                WHERE interface = ?
            ";

            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $this->iface->__get('iface'));
            $stmt->bindValue(2, $this->iface->__get('iface'));
            return $stmt->execute();
        } 
    }
}
