<?php

/**
 * @var array $arParams;
 * @var array $arResult;
 * @var string $templatePath;
 * @var string $componentPath;
 */

use DB\Basic;
use Main\Asset;
use Main\Settings;
use Main\Application;

//Подключение стилей
if(file_exists($templatePath . '/style.min.css')) {
    Asset::addExternalCss($templatePath . '/style.min.css');
}
else {
    Asset::addExternalCss($templatePath . '/style.css');
}

//Подключение скриптов
if(file_exists($templatePath . '/script.min.js')) {
    Asset::addExternalJs($templatePath . '/script.min.js');
}
else {
    Asset::addExternalJs($templatePath . '/script.js');
}

//Подготовка параметров компонента
$settings = new Settings();
$cache_flags = $settings->getCacheParams();

$arParams['CACHE_TIME'] = ($arParams['CACHE_TIME']) ? $arParams['CACHE_TIME'] : $cache_flags['value']['config_options'];
$arParams['TABLE_NAME'] = ($arParams['TABLE_NAME']) ? $arParams['TABLE_NAME'] : 'news';
$arParams['COUNT_ELEMENTS'] = ($arParams['COUNT_ELEMENTS']) ? $arParams['COUNT_ELEMENTS'] : 20;
$arParams['QUERY_PARAMS'] = ($arParams['QUERY_PARAMS']) ? $arParams['QUERY_PARAMS'] : [];

//Текущая страница и стартовая позиция запроса
$offset = ($_GET['page']) ? (intval($_GET['page']) * $arParams['COUNT_ELEMENTS'] + 1) : 1;
$currentPage = Application::getCurPage();

$nameCacheFile = md5($currentPage) . '.html';
$cacheFile = $cache_flags['value']['cache_position'] . $nameCacheFile;

//Если файл с кешом существует
if(file_exists($cacheFile)) {
    //Если время жизни кеша относительно создания файла не истекло
    if((time() - $arParams['CACHE_TIME']) < filemtime($cacheFile)) {
        echo file_get_contents($cacheFile);
        exit;
    }
}

//Стартуем создание кеша
ob_start();

$ob = new Basic();
$arResult = $ob->getList($arParams['TABLE_NAME'], $arParams['QUERY_PARAMS']);

require $templatePath . '/template.php';

$handle = fopen($cacheFile,'w');
fwrite($handle, ob_get_contents());
fclose($handle);
ob_end_flush(); // выводим в браузере