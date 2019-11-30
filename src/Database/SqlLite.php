<?php

namespace App\Database;

class SqlLite
{
    /**
     * PDO object
     * @var \PDO
     */
    private $pdo;

    public function __construct($config)
    {
        if ($this->pdo === null) {
            $dbPath = ".db/{$config['database']['db_name']}.db";
            $this->pdo = new \PDO("sqlite:$dbPath");
        }
    }

    public function getPDO()
    {
        return $this->pdo;
    }

    public function select($query, $bindings = [])
    {
        $stmt = $this->pdo->prepare($query);
        $this->applyBindings($stmt, $bindings);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function execute($statement, $bindings = [])
    {
        $stmt = $this->pdo->prepare($statement);
        $this->applyBindings($stmt, $bindings);

        return $stmt->execute();
    }

    private function applyBindings($stmt, $bindings = [])
    {
        if ($stmt === false) {
            throw new \Exception(json_encode($this->pdo->errorInfo()));
        }

        foreach ($bindings as $binding => $value) {
            $stmt->bindValue(":{$binding}", $value);
        }
    }
}