<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc, Bitrix\Main\Page\Asset;

$dir = __DIR__;
$dir = substr($dir, strlen($_SERVER['DOCUMENT_ROOT']));

// The code below does not works in admin section for unknown reasons
// $asset = Asset::getInstance();
// $asset->addCss($dir.'/style.css', true);

$APPLICATION->SetAdditionalCSS($dir . '/style.css');

$defaultServerName = $_SERVER['SERVER_NAME'];
if ($_SERVER['SERVER_PORT'] != 80) {
    $defaultServerName .= ':' . $_SERVER['SERVER_PORT'];
}
$siteName = \Bitrix\Main\Config\Option::get('main', 'server_name', $defaultServerName);
unset($defaultServerName);

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/classes/general/checklist.php');
$checklist = new CCheckList;
$isStarted = $checklist->started;
if ($isStarted == true) {
    $stat = $checklist->GetSectionStat();
} else {
    $reports = CCheckListResult::GetList([], ['REPORT' => 'Y']);
    if ($reports) {
        $report     = $reports->Fetch();
        $reportData = new CCheckList($report['ID']);
        $reportInfo = $reportData->GetReportInfo();
        $stat       = $reportInfo['STAT'];
    }
}

Loc::loadMessages(__FILE__);

if (!$isStarted && isset($report) && is_array($report)) { ?>
    <table class="dnet-gadget_info_table">
        <tr>
            <td><span class="dnet-gadget-caption"><?=Loc::getMessage('GD_DNET_INFO_SITE_CREATOR')?>:</span></td>
            <td>
                <a href="http://doctornet.pro" class="dnet-gadget-font" target="_blank">
                    Digital Production<br />
                    <span class="dnet-gadget-tag">&lt;<span class="dnet-gadget-marker">+</span>&gt;</span>
                    Doctornet.<span class="dnet-gadget-marker">pro</span>
                </a>
            </td>

            <td rowspan="2"><?=Loc::getMessage('GD_DNET_INFO_RESPONSIBLE')?>:</td>
            <td rowspan="2">
                <span class="dnet-gadget-font"><?=$report['TESTER'];?></span>
            </td>
        </tr>

        <tr>
            <td><span class="dnet-gadget-caption"><?=Loc::getMessage('GD_DNET_INFO_ADDRESS')?>:</span></td>
            <td>
                <a href="http://<?=$siteName?>"
                   class="dnet-gadget-font dnet-gadget-link"
                   target="_blank"><?=$siteName?></a>
            </td>
        </tr>

        <tr>
            <td><span class="dnet-gadget-caption"><?=Loc::getMessage('GD_DNET_INFO_WEBSITE_PUT')?>:</span></td>
            <td>
                <span class="dnet-gadget-font"><? $date = explode(' ', $report["DATE_CREATE"]); echo $date[0]; ?></span>
            </td>
            <td><span class="dnet-gadget-caption"><?=Loc::getMessage('GD_DNET_INFO_EMAIL')?>:</span></td>
            <td>
                <a href="mailto:<?=$report["EMAIL"]?>"
                   class="dnet-gadget-font dnet-gadget-link"
                   target="_blank"><?=$report["EMAIL"]?></a>
            </td>
        </tr>
        <tr>
            <td colspan="4">
                <hr>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="vertical-align: middle;">
                <a href="http://doctornet.pro" class="dnet-gadget-logo" target="_blank"></a>
            </td>
            <td colspan="2">
                <span class="dnet-gadget-font"><?=Loc::getMessage('GD_DNET_INFO_VENDOR_DESCRIPTION')?></span>
            </td>
        </tr>
    </table>
<? } else { ?>
    <table class="dnet-gadget_info_table">
        <tr>
            <td><span class="dnet-gadget-caption"><?=Loc::getMessage('GD_DNET_INFO_SITE_CREATOR')?>:</span></td>
            <td colspan="3">
                <a href="http://doctornet.pro" class="dnet-gadget-font" target="_blank">
                    Digital Production<br />
                    <span class="dnet-gadget-tag">&lt;<span class="dnet-gadget-marker">+</span>&gt;</span>
                    Doctornet.<span class="dnet-gadget-marker">pro</span>
                </a>
            </td>
        </tr>

        <tr>
            <td><span class="dnet-gadget-caption"><?=Loc::getMessage('GD_DNET_INFO_ADDRESS')?>:</span></td>
            <td>
                <a href="http://<?=$siteName?>"
                   class="dnet-gadget-font dnet-gadget-link"
                   target="_blank"><?=$siteName?></a>
            </td>
            <td><span class="dnet-gadget-caption"><?=Loc::getMessage('GD_DNET_INFO_EMAIL')?>:</span></td>
            <td>
                <a href="mailto:info@doctornet.pro"
                   class="dnet-gadget-font dnet-gadget-link"
                   target="_blank">info@doctornet.pro</a>
            </td>
        </tr>
        <tr>
            <td colspan="4">
                <hr>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="vertical-align: middle;">
                <a href="http://doctornet.pro" class="dnet-gadget-logo" target="_blank"></a>
            </td>
            <td colspan="2">
                <span class="dnet-gadget-font"><?=Loc::getMessage('GD_DNET_INFO_VENDOR_DESCRIPTION')?></span>
            </td>
        </tr>
    </table>
    <?
}
