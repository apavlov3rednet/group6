<?php

namespace Core\DB;

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/modules/main/Settings.php'); //todo: удалить после автолоадера
require_once($_SERVER['DOCUMENT_ROOT'] . '/core/modules/main/Logs.php'); //todo: удалить после автолоадера

use Core\Main\Settings;
use Core\Main\Logs;
use PDO;
use PDOException;

class Basic {
    public $dbname;
    public $username;
    public $password;
    public $host;
    public $conn;

    public $settings;

    public function __construct (string $dbname = 'default')
    {
        $this->settings = new Settings();
        $arSettings = $this->settings->getDbConnection($dbname);

        $this->dbname = $arSettings['database'];
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
            Logs::add2Log($e->getMessage());
            $this->conn = null;
            return false;
        }
    }

    /**
     * Summary of getList
     * @param string $table
     * @param array $params = [
     *  'select' => ['NAME', 'PASSWORD' ....], // имена выбираемых полей
     *  'filter' => ['GROUP' => 5], //фильтр
     *  'order' => ['SORT' => 'ASC'], //сортировка
     *  'limit' => [
     *      'offset' => 1, //Текущая позиция в выборке
     *      'rows' => 100 //количество выбираемых позиций
     *  ]
     * ]
     * @return array
     */
    public function getList(string $table, array $params = []) : array
    {

        if(!$this->conn)
            return [];

        //Значения по умолчанию
        $filter = [];   //Подготовленный фильтр для запроса в БД
        $execute = [];  //Параметры фильтра
        $limit = 100;   //Количество выбираемых элементов
        $offset = 0;    //Стартовая позиция выборки
        $result = [];   //Результирующий массив

        //Основная выборка из таблицы
        $sql = 'SELECT ';
        $select = (!empty($params['select'])) ? join(', ', $params['select']) : '*';
        $sql .= $select . ' FROM ' . $table; // SELECT 'USER', 'PASSWORD' FROM users

        //Фильтр
        if(!empty($params['filter'])) {
            foreach($params['filter'] as $key => $value) {
                $filter[] = $key . ' = ?';
                $execute[] = $value;
            }
        }

        if(!empty($filter)) {
            $sql .= ' WHERE '. join(', ', $filter); 
            //SELECT 'USER', 'PASSWORD' FROM users WHERE GROUP = ?, AGRE = ?
        }

        //Сортировка
        if(!empty($params['order'])) {
            $key = array_key_first($params['order']);
            $sql .= ' ORDER BY ' . $key . ' ' . $params['order'][$key];
            //SELECT ... FROM users WHERE ... ORDER BY AGE ASC|DESC
        }

        //Применение лимитов и стартовой позиции выборки
        if(!empty($params['limit'])) {
            $limit = (!empty($params['limit']['rows'])) ? $params['limit']['rows'] : $limit;
            $offset = (!empty($params['limit']['offset'])) ? $params['limit']['offset'] : $offset;

            $sql .= ' LIMIT ' . $limit;
            $sql .= ' OFFSET ' . $offset;

            //SELECT ... FROM users WHERE ... ORDER BY AGE ASC|DESC LIMIT 100 OFFSET 1
        }

        echo $sql;

        try {
            $request = $this->conn->prepare($sql);
            $request->execute($execute);
    
            $response = $request->fetchAll(PDO::FETCH_ASSOC);
    
            $result = [];
    
            foreach($response as $row) {
                $result[] = $row;
            }
        }
        catch(PDOException $e) {
            Logs::add2Log($e->getMessage());
        }
       
        return $result;
    }
}