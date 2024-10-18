<?php

namespace Main;

use DB\Basic as DB;

class Basic {
    static public function getCurPage() {
        return $_SERVER['REQUEST_URI'];
    }

    static public function getPageAttributes($page) {
        $request = new DB();
        $page = $request->getList('attributes', [
            'filter' => ['PAGE' => $page]
        ])[0];
        //$request->close();
        return $page;
    }
}