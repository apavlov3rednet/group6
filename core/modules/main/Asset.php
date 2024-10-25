<?php

namespace Main;

class Asset {

    static public function addExternalCss(string $css) {   
        if(file_exists($css)) {
            echo '<link rel="stylesheet" href="' . $css .'"/>';
        }
    }

    static public function addExternalJs(string $js, array $params = []) {
        if(file_exists($js)) {
            $defer = (isset($params['defer']) && $params['defer'] == true) ? 'defer' : '';
            $async = (isset($params['async']) && $params['async'] == true) ? 'async' : '';

            echo '<script src="' . $js . '" ' . $defer .' '. $async . '></script>';
        }
    }

    static public function addHeadString(string $string) {

    }
}