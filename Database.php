<?php

class Database {
    //connection
    public $conn;

    /**
     * Constructor for Database class
     * 
     * @param array $config
     */
    public function __construct($config) {
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE =>PDO::FETCH_OBJ
        ];

        try {
            $this->conn = new PDO($dsn, $config['username'], $config['password'], $options);

        } catch (PDOException $e){
            throw new Exception("Database connection failed: {$e->getMessage()}");
        }
    }

    /**
     * Query the databse
     * 
     * @param string $query
     * 
     * @return PDOStatement
     * @throws PDOException
     */
    public function query($query) {
        try {
            // $conn is built as a pdo instance in the contructor
            $sth = $this->conn->prepare($query);
            $sth->execute();
            return $sth;
        } catch (PDOException $e) {
            throw new Exception("Query failed to execute: {$e->getMessage()}");
        }
    }


}