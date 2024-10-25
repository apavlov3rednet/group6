<?php

/**
 * @var array $arParams;
 * @var array $arResult;
 * @var string $templatePath;
 * @var string $componentPath;
 * @var object $ob;
 */
?>

<h2>Пользователи</h2>

<div class="user-list">
    <?php if(!empty($arResult['ITEMS'])):?>
        <?php foreach($arResult['ITEMS'] as $key => $arItem):?>
            <div class="user-list-el">
                <div class="user-list-el-id"><?=$arItem['ID']?></div>
                <div class="user-list-el-login"><?=$arItem['LOGIN']?></div>
            </div>
        <?php endforeach;?>
    <?php endif;?>
</div>

<?php if($arParams['SHOW_PAGER'] == 'Y'):?>
    <div class="pager">
        <?php for($i = 1; $i <= $arResult['COUNT_PAGE']; $i++):?>
            <a href="?page=<?=$i?>"><?=$i?></a>
        <?php endfor;?>
    </div>
<?php endif;?>

