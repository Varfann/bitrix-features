<?

/**
 * @author    Roman Shershnev <readytoban@gmail.com>
 * @version   1.1
 * @package   CodeCraft
 * @category  Bitrix, empty module
 * @copyright Copyright © 2015, 2016, Roman Shershnev
 *
 * Bitrix vars
 * 
 * @global string $DBType
 *
 */

global $DBType;

$classList = array(
    'DoctorNet\\Null\\Simple' => 'classes/general/simple.php',
);

\Bitrix\Main\Loader::registerAutoLoadClasses('doctornetpro.null', $classList);