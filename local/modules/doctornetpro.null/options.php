<?

/**
 * @author    Roman Shershnev <readytoban@gmail.com>, Dmitry Panychev <panychev@code-craft.ru>
 * @version   1.1
 * @package   CodeCraft
 * @category  Bitrix, empty module
 * @copyright Copyright © 2015, 2016, Roman Shershnev, Dmitry Panychev
 *
 * Bitrix vars
 * @global CUser     $USER
 * @global CMain     $APPLICATION
 * @global CDatabase $DB
 */

use Bitrix\Main\Localization\Loc, Bitrix\Main\Config\Option;

$module_id = 'doctornetpro.null';
$mid       = $_REQUEST['mid'];
IncludeModuleLangFile(__FILE__);

if (!$USER->CanDoOperation('view_other_settings') && !$USER->CanDoOperation('edit_other_settings')) {
    $APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));
}

$CAT_RIGHT = $APPLICATION->GetGroupRight($module_id);

if (check_bitrix_sessid() && $CAT_RIGHT == 'W') {
    if (isset($_POST['null'])) {
        $_POST['null'] = htmlspecialcharsbx($_POST['null']);
        Option::set($module_id, 'null', $_POST['null']);
    }
}

if ($CAT_RIGHT >= 'R') {
    
    include_once($GLOBALS['DOCUMENT_ROOT'] . '/bitrix/modules/' . $module_id . '/include.php');
    
    $arTabs = array(
        array(
            'DIV'   => 'edit1',
            'TAB'   => Loc::getMessage('MAIN_TAB_SET'),
            'ICON'  => 'doctornetpro_null_settings',
            'TITLE' => Loc::getMessage('MAIN_TAB_TITLE_SET'),
        ),
        array(
            'DIV'   => 'edit2',
            'TAB'   => Loc::getMessage('MAIN_TAB_RIGHTS'),
            'ICON'  => 'doctornetpro_null_rights',
            'TITLE' => Loc::getMessage('MAIN_TAB_TITLE_RIGHTS'),
        ),
    );
    
    $tabControl = new \CAdminTabControl('tabControl', $arTabs);
    
    $tabControl->Begin();
    ?>
    <form method="POST"
          action="<?= $APPLICATION->GetCurPage() ?>?mid=<?= htmlspecialchars($mid) ?>&lang=<?= LANG ?>">
        <?= bitrix_sessid_post(); ?>
        <? $tabControl->BeginNextTab(); // SETTINGS?>
        <tr class="heading">
            <td colspan="2"><b><?= Loc::getMessage("DOCTORNET_NULL_OPT") ?></b></td>
        </tr>
        <tr>
            <td width="50%" class="adm-detail-content-cell-l">
                <label for="null"><?= Loc::getMessage('DOCTORNET_NULL_NULL') ?>:</label>
            </td>
            <td width="50%" class="adm-detail-content-cell-r">
                <input type="text"
                       size="30"
                       maxlength="255"
                       name="null"
                       value="<?= Option::get($module_id, 'null', '') ?>">
            </td>
        </tr>
        <? $tabControl->BeginNextTab(); // RIGHTS?>
        <? require_once($_SERVER['DOCUMENT_ROOT'] . BX_ROOT  . '/modules/main/admin/group_rights.php'); ?>
        <? $tabControl->Buttons(); ?>
        <input type="submit" <? if ($CAT_RIGHT < 'W')
            echo "disabled" ?> name="Update" value="<?= Loc::getMessage('MAIN_SAVE') ?>">
        <input type="hidden" name="Update" value="Y">
        <input type="reset" name="reset" value="<?= Loc::getMessage('MAIN_RESET') ?>">
    </form>
    <? $tabControl->End();
    
}