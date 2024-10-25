<?php
require_once __DIR__ . '/autoloader.php';

use Main\Basic;

$curPage = Basic::getCurPage();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=$attrs['TITLE']?></title>
    <meta name="keywords" content="<?=$attrs['KEYWORDS']?>">
    <meta name="description" content="<?=$attrs['DESCRIPTION']?>">
</head>
<body>