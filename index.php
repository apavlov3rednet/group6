<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/core/header.php');

use \DB\Basic;
?>


<?php
$result = new Basic();

$arResult = $result->getList('users', [
    'select' => ['LOGIN'],
    'limit' => ['rows' => 2]
]);
?>

<pre>
        <? print_r($arResult) ?>
    </pre>

<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/core/footer.php');
