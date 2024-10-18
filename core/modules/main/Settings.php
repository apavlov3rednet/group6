<?php

namespace Main;

class Settings {
    private $arSettings;

    public function __construct() {
        $this->arSettings = require_once($_SERVER['DOCUMENT_ROOT'] . '/core/.settings.php');
    }

    /** <p>Получение настроек подключения к БД</p>
     * @param string $dbname - по умолчанию 'default'
     * @return mixed
     */
    public function getDbConnection(string $dbname = 'default')
    {
        if(is_array($this->arSettings['connections']['value'][$dbname]))
            return $this->arSettings['connections']['value'][$dbname];
    }
}
