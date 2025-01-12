<?php

class OracleDb
{
    private PDO $conn;
    private static ?OracleDb $instance = null;

    private function __construct()
    {
        try {
            // Conexão com o $oracle usando PDO
            $this->conn = new PDO("oci:dbname=//localhost:1521/XE", "system", "root");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            throw new \Exception("Connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance(): OracleDb
    {
        if (self::$instance === null) {
            self::$instance = new OracleDb();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->conn;
    }

    private function query(string $query, array $params): \PDOStatement
    {
        try {
            $statement = $this->conn->prepare($query);
            $statement->execute($params);
            return $statement;
        } catch (\PDOException $e) {
            throw new \Exception("Query failed: " . $e->getMessage());
        }
    }

    public function select(string $query, array $params = [], bool $isAssoc = false): array
    {
        $result_query = $this->query($query, $params);

        // Se for para retornar como um array associativo
        if ($isAssoc) {
            return $result_query->fetchAll(PDO::FETCH_ASSOC);
        }

        // Se for para retornar como objeto, converta as chaves para minúsculas
        $results = $result_query->fetchAll(PDO::FETCH_OBJ);
        $lowercaseResults = [];

        foreach ($results as $obj) {
            $lowercaseObj = new stdClass();
            foreach ($obj as $key => $value) {
                // Converte o nome da propriedade para minúsculo
                $lowercaseObj->{strtolower($key)} = $value;
            }
            $lowercaseResults[] = $lowercaseObj;
        }

        return $lowercaseResults;
    }

    public function selectOne(string $query, array $params = []): object|false
    {
        $result_query = $this->query($query, $params);
        $result = $result_query->fetch(PDO::FETCH_OBJ);

        if ($result) {
            // Converte as propriedades para minúsculas
            $lowercaseResult = new stdClass();
            foreach ($result as $key => $value) {
                $lowercaseResult->{strtolower($key)} = $value;
            }
            return $lowercaseResult;
        }

        return false;
    }

    public function update(string $query, array $params = []): int
    {
        $result_query = $this->query($query, $params);
        return $result_query->rowCount();
    }

    public function insert(string $query, array $params = []): void
    {
        $this->query($query, $params);
    }

    public function delete(string $query, array $params): int
    {
        $result_query = $this->query($query, $params);
        return $result_query->rowCount();
    }

    public function beginTransaction(): void
    {
        $this->conn->beginTransaction();
    }

    public function endTransaction(): void
    {
        $this->conn->commit();
    }

    public function rollback(): void
    {
        $this->conn->rollback();
    }

    public function count(string $query, array $params): int
    {
        $result_query = $this->select($query, $params);
        return count($result_query);
    }

    public function exists(string $query, array $params): bool
    {
        return !!$this->selectOne($query, $params);
    }

    // Método para buscar a chave primária
    public function getPrimaryKey($table_name)
    {
        $query = "SELECT column_name FROM all_tab_columns WHERE table_name = UPPER('$table_name') AND column_name = 'ID'";
        $primary = $this->select($query);
        return $primary[0]->column_name ?? null;
    }
}
