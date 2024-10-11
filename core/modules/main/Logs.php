<?php

namespace Core\Main;

define('CONST_LOG_FILE', $_SERVER['DOCUMENT_ROOT'] . '/core/logs.log');

abstract class Logs
{
    static public function add2Log($log) {
        //fopen, fwrite, fclose

        file_put_contents(CONST_LOG_FILE, print_r($log));
    }
}