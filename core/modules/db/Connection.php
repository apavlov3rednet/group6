<?php

namespace Core\DB;

use Core\Main\Settings;
use PDO;
use PDOException;

class Connection {
    private $dbname;
    private $settings;
    private $conn;

    public function __constructor (string $dbname = 'default'): void
    {
        $this->dbname = $dbname;
        $arSettings = new Settings();
        $this->settings = $arSettings->getDbConnection($this->dbname);
    }

    public function connect() {
        try {
            $this->conn = new PDO(`
                mysql:host=$this->settings["host"];dbname=$this->settings["database"]`, 
                $this->settings["user"], 
                $this->settings["password"]
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return true;
        }
        catch(PDOException $e) {
            echo 'Connection failed:' . $e->getMessage();
            return false;
        }
    }
}