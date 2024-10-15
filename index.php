<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/core/modules/main/Settings.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/core/modules/db/Basic.php');

use \Core\DB\Basic;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
    $result = new Basic();

    $arResult = $result->getList('users', [
        'select' => ['LOGIN'],
        'limit' => ['rows' => 2]
    ]);
    ?>

    <pre>
        <?print_r($arResult)?>
    </pre>
</body>
</html>