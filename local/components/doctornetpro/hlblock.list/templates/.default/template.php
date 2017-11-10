<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

?>
<? foreach ($arResult as $item) { ?>
    <p><img src="<?=$item['UF_PHOTO']?>"/></p>
    <p><img src="<?=$item['UF_PICTURE']?>"/></p>
    <p><?= $item['UF_NAME']; ?></p>
    <p><?= $item['UF_POSITION']; ?></p>
    <p><?= $item['UF_INFORMATION']; ?></p>
    <p><?= $item['UF_OPINION']; ?></p>
<? } ?>