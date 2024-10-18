<?php

spl_autoload_register(function($class) 
{
    $baseDir = $_SERVER['DOCUMENT_ROOT'] . '/core/modules/';

    $relativeClass = str_replace('\\','/', $class); // Core/DB/Basic;

    $file = $baseDir . $relativeClass .'.php';

    if (file_exists($file)) {
        require $file;
    }
});