<?php
/*
|------------------------------------------------------------
| Modèle Role
|------------------------------------------------------------
*/
require_once __DIR__ . '/../config/db.php';

class Role
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function listerTous()
    {
        $stmt = $this->pdo->query("SELECT * FROM Role ORDER BY id_Role ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM Role WHERE id_Role = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
