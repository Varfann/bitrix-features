<?

use Bitrix\Main\EventManager;

$eventManager = EventManager::getInstance();

$eventManager->addEventHandler('main', 'OnBuildGlobalMenu', ['\\DoctorNet\\Handlers\\Main', 'addReviewMenu']);