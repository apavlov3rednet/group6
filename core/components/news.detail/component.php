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

$arParams['CACHE_TIME'] = (isset($arParams['CACHE_TIME'])) ? $arParams['CACHE_TIME'] : $cache_flags['value']['config_options'];
$arParams['CACHE_ACTIVE'] = (isset($arParams['CACHE_ACTIVE'])) ? $arParams['CACHE_ACTIVE'] : 'N';
$arParams['TABLE_NAME'] = (isset($arParams['TABLE_NAME'])) ? $arParams['TABLE_NAME'] : 'news';
$arParams['QUERY_PARAMS'] = (isset($arParams['QUERY_PARAMS'])) ? $arParams['QUERY_PARAMS'] : [];

$currentPage = Application::getCurPage();

$nameCacheFile = md5($currentPage) . '.html';
$directory = $cache_flags['value']['cache_position'] . 'components/news.detail/';
$cacheFile = $directory . $nameCacheFile;

if(!is_dir($directory)) {
    mkdir($directory, 0777, true);
}


//Если файл с кешом существует
if(file_exists($cacheFile) && $arParams['CACHE_ACTIVE'] === 'Y') {
    //Если время жизни кеша относительно создания файла не истекло
    if((time() - $arParams['CACHE_TIME']) < filemtime($cacheFile)) {
        echo file_get_contents($cacheFile);
        exit;
    }
}

//Стартуем создание кеша
ob_start();

$ob = new Basic();
$arResult = $ob->getById($arParams['TABLE_NAME'], $arParams['ELEMENT_ID'])[0];

if(file_exists($templatePath . '/result_modifer.php')) {
    require $templatePath . '/result_modifer.php';
}

if(file_exists($templatePath . '/template.php')) {
    require $templatePath . '/template.php';
}

$handle = fopen($cacheFile,'w');
fwrite($handle, ob_get_contents());
fclose($handle);
ob_end_flush(); // выводим в браузере