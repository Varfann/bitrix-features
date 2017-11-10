<?

/**
 * @author    Roman Shershnev <readytoban@gmail.com>, Dmitry Panychev <panychev@code-craft.ru>
 * @version   1.1
 * @package   CodeCraft
 * @category  Bitrix, empty module
 * @copyright Copyright © 2015,2016, Roman Shershnev, Dmitry Panychev
 */

use Bitrix\Main\Localization\Loc, Bitrix\Main\Config\Option, Bitrix\Main\EventManager, Bitrix\Main\Application, Bitrix\Main\IO\Directory;

Loc::loadMessages(__FILE__);

class doctornetpro_null extends CModule {

    public static $MODULE_ID = "doctornetpro.null";
    public static $MODULE_VERSION;
    public static $MODULE_VERSION_DATE;
    public static $MODULE_NAME;
    public static $MODULE_DESCRIPTION;
    public static $MODULE_CSS;

    public static $PARTNER_NAME;
    public static $PARTNER_URI;

    public function doctornetpro_null() {
        $arModuleVersion = array();

        include(__DIR__ . '/version.php');

        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
            $this->MODULE_VERSION      = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

        $this->PARTNER_NAME       = Loc::getMessage('DOCTORNET_PARTNER');
        $this->PARTNER_URI        = Loc::getMessage('DOCTORNET_PARTNER_URI');
        $this->MODULE_NAME        = Loc::getMessage('DOCTORNET_NULL_INSTALL_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('DOCTORNET_NULL_INSTALL_DESCRIPTION');
    }

    public function InstallFiles($arParams = array()) {

        CopyDirFiles(__DIR__ . '/components', Application::getDocumentRoot() . BX_ROOT . '/components', true, true);

        return true;
    }

    public static function UnInstallFiles() {
        Directory::deleteDirectory(Application::getDocumentRoot() . BX_ROOT . '/components/doctornetpro/null');

        return true;
    }

    public function InstallEvents() {
        EventManager::getInstance()
                    ->registerEventHandler('main', 'OnProlog', self::$MODULE_ID, 'DoctorNet\\Null\\testHandler',
                        'testHandler');

        return true;
    }

    public static function UnInstallEvents() {
        EventManager::getInstance()
                    ->unRegisterEventHandler('main', 'OnProlog', self::$MODULE_ID, 'DoctorNet\\Null\\testHandler',
                        'testHandler');
    }

    public function DoInstall() {
        global $APPLICATION;

        self::InstallFiles();

        self::InstallEvents();

        RegisterModule(self::$MODULE_ID);

        $APPLICATION->IncludeAdminFile(Loc::getMessage('DOCTORNET_NULL_INSTALL_TITLE',
            ['#MODULE#', self::$MODULE_NAME]), __DIR__ . '/step.php');
    }

    public static function DoUninstall() {
        global $APPLICATION;

        Option::delete(self::$MODULE_ID);

        self::UnInstallFiles();

        self::UnInstallEvents();

        UnRegisterModule(self::$MODULE_ID);

        $APPLICATION->IncludeAdminFile(Loc::getMessage('DOCTORNET_NULL_UNINSTALL_TITLE',
            ['#MODULE#', self::$MODULE_NAME]), __DIR__ . '/unstep.php');
    }
}