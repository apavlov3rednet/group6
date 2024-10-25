<?php

use Main\Application;

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/header.php');
?>

<?php Application::includeComponent('news.list', 'default', [
    'TABLE_NAME' => 'users'
])?>

<?php Application::includeComponent('news.detail', 'default', [
    'TABLE_NAME' => 'users',
    'ELEMENT_ID' => 2
])?>


<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/core/footer.php');
