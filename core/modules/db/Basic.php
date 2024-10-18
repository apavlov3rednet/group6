<?php

namespace DB;

use Main\Settings;
use Main\Logs;
use PDO;
use PDOException;

class Basic
{
    public $dbname;
    public $username;
    public $password;
    public $host;
    public $conn;

    public $settings;

    public function __construct(string $dbname = 'default')
    {
        $this->settings = new Settings();
        $arSettings = $this->settings->getDbConnection($dbname);

        $this->dbname = $arSettings['database'];
        $this->username = $arSettings['login'];
        $this->password = $arSettings['password'];
        $this->host = $arSettings['host'];

        $this->connect();
    }

    public function close()
    {
        $this->conn = null;
    }

    public function connect(): bool
    {
        try {
            $this->conn = new PDO(
                "mysql:host=$this->host;dbname=$this->dbname",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return true;
        } catch (PDOException $e) {
            Logs::add2Log($e->getMessage());
            $this->conn = null;
            return false;
        }
    }

    private function prepareFilter(array $arFilter, string &$sql, array &$filter, array &$execute)
    {
        foreach ($arFilter as $key => $value) {
            $filter[] = $key . ' = ?';
            $execute[] = $value;
        }

        if (!empty($filter)) {
            $sql .= ' WHERE ' . join(', ', $filter);
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
    public function getList(string $table, array $params = []): array
    {

        if (!$this->conn)
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
            $this->prepareFilter($params['filter'], $sql, $filter, $execute);
            //SELECT 'USER', 'PASSWORD' FROM users WHERE GROUP = ?, AGRE = ?
        }

        //Сортировка
        if (!empty($params['order'])) {
            $key = array_key_first($params['order']);
            $sql .= ' ORDER BY ' . $key . ' ' . $params['order'][$key];
            //SELECT ... FROM users WHERE ... ORDER BY AGE ASC|DESC
        }

        //Применение лимитов и стартовой позиции выборки
        if (!empty($params['limit'])) {
            $limit = (!empty($params['limit']['rows'])) ? $params['limit']['rows'] : $limit;
            $offset = (!empty($params['limit']['offset'])) ? $params['limit']['offset'] : $offset;

            $sql .= ' LIMIT ' . $limit;
            $sql .= ' OFFSET ' . $offset;

            //SELECT ... FROM users WHERE ... ORDER BY AGE ASC|DESC LIMIT 100 OFFSET 1
        }

        try {
            $request = $this->conn->prepare($sql);
            $request->execute($execute);

            $response = $request->fetchAll(PDO::FETCH_ASSOC);

            $result = [];

            foreach ($response as $row) {
                $result[] = $row;
            }
        } catch (PDOException $e) {
            Logs::add2Log($e->getMessage());
        }
        return $result;
    }

    /**
     * Summary of add
     * @param string $table
     * @param array $arFilter = [
     *  'KEY' => 'VALUE',
     *  'KEY2' => 'VALUE2', ....
     * ]
     * @return void
     */
    public function add(string $table, array $arFields): bool|string
    {
        try {
            //INSERT INTO `users` (`ID`, `LOGIN`, `PASSWORD`) VALUES (:ID, :LOGIN, :PASSWORD);
            $fields = join(', ', array_keys($arFields)); // ID, LOGIN, PASSWORD
            $prepareFields = ':' . join(', :', array_keys($arFields)); // :ID, :LOGIN, :PASSWORD

            $sql = 'INSERT INTO ' . $table . '(' . $fields . ') VALUES (' . $prepareFields . ')';
            //INSERT INTO `users` (`ID`, `LOGIN`, `PASSWORD`) VALUES (:ID, :LOGIN, :PASSWORD);

            $request = $this->conn->prepare($sql);

            foreach($arFields as $key => $value) {
                $request->bindValue(':' . $key, $value);
            }

            if($request->execute()) {
                $id = $this->conn->lastInsertId('ID');
                return $id;
            }
            else {
                Logs::add2Log('Fail add element');
                return false;
            }
        }
        catch(PDOException $e) {
            Logs::add2Log($e->getMessage());
            return false;
        }
    }

    /**
     * Summary of delete
     * @param string $table
     * @param array $where = [
     *  'ID' => 1
     * ]
     * @return bool|string
     */
    public function delete(string $table, array $where): bool
    {
        try {
            $filter = [];
            $execute = [];
            $sql = 'DELETE FROM ' . $table;
            $this->prepareFilter($where, $sql, $filter, $execute);
            $request = $this->conn->prepare($sql);
            if($request->execute()) {
                return true;
            }
            else {
                Logs::add2Log('Error delete');
                return false;
            }   
        }
        catch(PDOException $e) {
            Logs::add2Log($e->getMessage());
            return false;
        }
    }

    public function getById(string $table, string $ID): array
    {
        return $this->getList($table, [
            'filter' => ['ID' => $ID]
        ]);
    }

    public function deleteById(string $table, string $ID): bool
    {
        return $this->delete($table, ['ID' => $ID]);
    }

    public function update(string $table, array $arFields, string $ID): bool
    {
        try {
            //UPDATE users SET name = :name, email = :emeail WHERE ID = :id
            $filter = [];
            $execute = [];
            $arSql = [];
            $sql = 'UPDATE '. $table . ' SET ';
            
            foreach($arFields as $key => $value) {
                $arSql[] = $key .'= :'. $value .'';
            }

            if(count($arSql) > 0) {
                $sql .= join(', ', $arSql);
            }

            $this->prepareFilter(['ID' => $ID], $sql, $filter, $execute);
            
            $request = $this->conn->prepare($sql);

            foreach($arFields as $key => $value) {
                $request->bindValue(':' . $key, $value);
            }

            if($request->execute($execute)) {
                return true;
            }
            else {
                Logs::add2Log('Error update');
                return false;
            }

        }
        catch(PDOException $e) {
            Logs::add2Log($e->getMessage());
            return false;
        }
    }
}
