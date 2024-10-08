<?php

namespace Core\Main;

class Settings {
    protected $arSettings;

    public function __construct() {
        self::$arSettings = require_once('../.settings.php');
    }

    /** <p>Получение настроек подключения к БД</p>
     * @param string $dbname - по умолчанию 'default'
     * @return mixed
     */
    public function getDbConnection(string $dbname = 'default') : mixed
    {
        return self::$arSettings['connections']['value'][$dbname];
    }
}
