<?php

namespace Core\DB;

use Core\Main\Settings;
use PDO;
use PDOException;

class Basic {
    private $dbname;
    private $username;
    private $password;
    private $host;
    private $conn;

    public function __constructor (string $dbname = 'default'): void
    {
        $this->dbname = $dbname;
        $arSettings = Settings::getDbConnection($dbname);
        $this->username = $arSettings['login'];
        $this->password = $arSettings['password'];
        $this->host = $arSettings['host'];

        $this->connect();
    }

    public function connect() : bool
    {
        try {
            $this->conn = new PDO(
                "mysql:host=$this->host;dbname=$this->dbname", 
                $this->username, 
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return true;
        }
        catch(PDOException $e) {
            \Core\Main\Logs::add2Log($e->getMessage());
            $this->conn = null;
            return false;
        }
    }

    public function getList(string $table, array $params) : array
    {
        $values = join(', ', array_keys($params)) ?? '*';

        $sql = 'SELECT ' . $values . ' FROM ' . $table;

        $execute = [];
        $sqlWhere = [];

        /**
         * [
         *  'USER' => '',
         *  'GROUP' => 'admin'
         * ]
         */

        foreach($params as $key => $value) {
            if(!empty($value)) {
                $sqlWhere[] = $key .' = ?';
                $execute[] = $value;
            }
        }

        if(!empty($sqlWhere)) { 
            $sql .= ' WHERE '. join(', ', $sqlWhere);
        }

        $request = $this->conn->prepare($sql);

        $request->execute($execute);

        $response = $request->fetchAll(PDO::FETCH_ASSOC);

        $result = [];

        foreach($response as $row) {
            $result[] = $row;
        }

        return $result;
    }
}