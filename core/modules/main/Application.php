<?php

namespace Main;

use Exception;

//Реализует парадигму Singltone

class Application {
    public function __construct() {
    }

    /**
     * Summary of getCurPage
     * @return string
     */
    static public function getCurPage() {
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * Summary of includeComponent
     * @param string $name - exmpl, news.list
     * @param string $template - exmpl, default
     * @param array $parameters = [
     *  
     * ]
     * @return void
     */
    static public function includeComponent(string $name, string $template = '', array $parameters = []) {
        //Подготовка основного пути до компонента
        if(!$name)
            throw new Exception("template name must be announced");
            
        $componentPath = $_SERVER['DOCUMENT_ROOT'] . '/core/components/' . $name;

        //Подготовка пути до шаблона компонента
        if($template === '')
            $template = 'default';

        $templatePath = $componentPath . '/templates/' . $template;

        //Подключаем дополнительные функции к компоненту
        if(file_exists($componentPath . '/function.php')) {
            require $componentPath . '/function.php';
        }

        //подключение параметров компонента
        if(file_exists($templatePath . '/.parameters.php')) {
            $arParams = require $templatePath . '/.parameters.php';
        }

        //Подключаем дополнительные функции к компоненту
        if(file_exists($componentPath . '/component.php')) {
            require $componentPath . '/component.php';
        }
    }
}