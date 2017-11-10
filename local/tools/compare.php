<?

define('NO_KEEP_STATISTIC', 'Y');
define('NO_AGENT_STATISTIC', 'Y');
define('PUBLIC_AJAX_MODE', true);
define('NOT_CHECK_PERMISSIONS', true);

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Loader;
use DoctorNet\UserBasket;

$action     = $_REQUEST['action'];
$userBasket = UserBasket::getInstance();

Loader::includeModule('catalog');
Loader::includeModule('sale');
Loader::includeModule('iblock');

$id = (int)$_REQUEST['id'];

switch ($action) {
    case 'add':
        if ($id) {
            $_SESSION['compare'][$id] = null;
        }
        break;
    case 'delete':
        if ($id && array_key_exists($id, $_SESSION['compare'])) {
            unset($_SESSION['compare'][$id]);
        }
        break;
    default:
        $APPLICATION->RestartBuffer();
        $APPLICATION->IncludeComponent('doctornetpro:null', 'functional.compare.list', []);
        die;
        break;
}

echo \Bitrix\Main\Web\Json::encode(['html' => 'сравнить <span>' . count($_SESSION['compare']) . '</span>' . ' товар'
                                              . \DoctorNet\Tools::ending(count($_SESSION['compare']), ['',
                                                                                                       'а',
                                                                                                       'ов'])]);