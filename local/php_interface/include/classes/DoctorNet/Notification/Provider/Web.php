<?

namespace DoctorNet\Notification\Provider;

use Bitrix\Highloadblock\DataManager;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Entity\ExpressionField;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SystemException;
use DoctorNet\Notification\Notification;
use Larus\Project;
use DoctorNet\Helper\DateHelper;


/**
 * Class Web - provide Limikon web notifications.
 *
 * @package DoctorNet\Notification\Provider
 */
class Web extends Notification
{

    const COMPANY_ADD_TYPE    = 14;
    const COMPANY_UPDATE_TYPE = 11;

    protected static $eventList = ['LARUS_NOTIFY_%',
                                   'USER_%',];

    /**
     * Set notification activity
     *
     * @param array|int $idList
     * @param bool      $isActive
     *
     * @throws SystemException
     */
    public static function setActive($idList, $isActive = false) {
        $entity = self::getEntity();


        $isActive = !$isActive ? 1 : 0;

        if (is_array($idList)) {

            foreach ($idList as $notify) {
                $entity::update($notify, ['UF_NEW_NOTIFICATION' => $isActive]);
            }

        } else {
            $entity::update($idList, ['UF_NEW_NOTIFICATION' => $isActive]);
        }

    }

    /**
     * @param int $id
     *
     * @return void
     * @throws SystemException
     */

    public static function setArchive($id) {
        $id = (int)$id;

        if (empty($id)) {
            return;
        }

        $entity = self::getEntity();

        $entity::update($id, ['UF_ARCHIVE' => 1]);

    }

    /**
     * @param $idList
     *
     * @return void
     * @throws SystemException
     */
    public static function setDelete($id) {
        $entity = self::getEntity();
        $id     = (int)$id;

        $entity::update($id, ['UF_ACTIVE' => 0]);
    }

    /**
     * Get highload block entity
     *
     * @todo refactor this with own highLoad block helpers
     *
     * @return DataManager
     *
     * @throws SystemException
     */
    protected static function getEntity() {
        /* @var \Bitrix\Highloadblock\DataManager $entityClass */
        $entityClass = HighloadBlockTable::compileEntity(self::getHLBlock())->getDataClass();

        if (is_string($entityClass)) {
            $entityClass = trim($entityClass, '\\');
            $entityClass = new $entityClass();
        }

        if (!$entityClass instanceof DataManager) {
            throw new SystemException(Loc::getMessage('NOTIFICATIONS_WRONG_BLOCK_ID'));
        }

        return $entityClass;
    }

    /**
     * Get current highload block
     *
     * @todo refactor this with own highLoad block helpers
     *
     * @return array|false
     *
     * @throws SystemException
     */
    protected static function getHLBlock() {
        $highLoadBlock = HighloadBlockTable::getById(Project::NOTIFICATION_HIGHLOAD_BLOCK_ID)->fetch();

        if (empty($highLoadBlock)) {
            throw new SystemException(Loc::getMessage('NOTIFICATIONS_WRONG_BLOCK_ID'));
        }

        return $highLoadBlock;
    }

    /**
     * Set notification activity
     *
     * @param bool $isActive
     *
     * @global     $USER
     * @throws SystemException
     */
    public static function setActiveAll() {
        $entity = self::getEntity();

        global $USER;

        $entityIdList = $entity::getList(['filter' => ['UF_USER_TO' => $USER->GetID()],
                                          'select' => ['ID']])->fetchAll();

        $isActive = 0;

        foreach ($entityIdList as $id) {
            $entity::update($id['ID'], ['UF_NEW_NOTIFICATION' => $isActive]);
        }
    }

    /**
     * @param $userId
     *
     * @return mixed
     * @throws SystemException
     */
    public static function getActiveCount($userId) {
        $entity = self::getEntity();
        $cnt    = $entity::query()->addSelect(new ExpressionField('CNT', 'COUNT(1)'))->setFilter(['UF_USER_TO'          => $userId,
                                                                                                  'UF_ACTIVE'           => 1,
                                                                                                  'UF_NEW_NOTIFICATION' => 1])->exec()->fetch();

        return $cnt['CNT'];
    }

    /**
     * @param $userId
     * @param $typeId
     *
     * @return mixed
     * @throws SystemException
     */
    public static function getActiveCountWithType($userId, $typeId) {

        $typeId = (int)$typeId;

        if (empty($typeId)) {
            return 0;
        }

        $filter = ['UF_USER_TO'          => $userId,
                   'UF_TYPE'             => $typeId,
                   'UF_ACTIVE'           => 1,
                   'UF_NEW_NOTIFICATION' => 1];

        $entity = self::getEntity();
        $cnt    = $entity::query()->addSelect(new ExpressionField('CNT', 'COUNT(1)'))->setFilter($filter)->exec()->fetch();

        return $cnt['CNT'];
    }

    /**
     * Get notification list to user by user id. Separate by ACTIVE/INACTIVE.
     *
     * @param      $userId
     * @param bool $filterByPeriod
     * @param int  $recordCount
     * @param string $pagerTemplate
     *
     * @return array
     * @throws SystemException
     */
    public static function getListByUserId($userId, $filterByPeriod = false, $recordCount = 0, $pagerTemplate = '') {
        $entity = self::getEntity();

        $select = ['*'];
        $order  = ['UF_DATE_CREATE' => 'DESC'];
        $filter = ['UF_USER_TO' => $userId,
                   'UF_ACTIVE'  => 1];

        if ($filterByPeriod == 'Y') {
            $dateHelper = new DateHelper();
            $dateFilter = $dateHelper->getPeriodFilter('UF_DATE_CREATE');
            $filter     = array_merge($filter, $dateFilter);
        }

        $resultCollection = new \CDBResult($entity::query()->setSelect($select)->setFilter($filter)->setOrder($order)->exec());
        $notifications    = ['ACTIVE'   => [],
                             'INACTIVE' => [],];
    
        if ($recordCount > 0) {
            $resultCollection->NavStart($recordCount, false);
        }

        $text = self::getTextMessages();

        while ($row = $resultCollection->GetNext()) {
            $row['UF_DATE_CREATE'] = $row['UF_DATE_CREATE']->toString();
            $row['UF_TITLE']       = trim($row['UF_TITLE']);

            $unserialized                            = @unserialize(html_entity_decode($row['~UF_MESSAGE']));
            $unserialized['values']['#SERVER_NAME#'] = $_SERVER['SERVER_NAME'];

            if (is_array($unserialized) && isset($text[$unserialized['template']])) {
                if (is_array($unserialized['values']) && !empty($unserialized['values'])) {
                    $search  = [];
                    $replace = [];

                    foreach ($unserialized['values'] as $key => $value) {
                        $search[]  = '#' . trim($key, " #\t\n\r\0\x0B") . '#';
                        $replace[] = trim($value);
                    }

                    $row['UF_MESSAGE'] = str_replace($search, $replace, $text[$unserialized['template']]);
                } else {
                    $row['UF_MESSAGE'] = $text[$unserialized['template']];
                }
            } elseif (is_array($unserialized) && $unserialized['template']) {
                $row['UF_MESSAGE'] = $unserialized['template'];
            }

            if ($row['UF_NEW_NOTIFICATION'] > 0) {
                $notifications['ACTIVE'][] = $row;
            } else {
                $notifications['INACTIVE'][] = $row;
            }
        }

        $pagination = $resultCollection->GetPageNavString('', $pagerTemplate, false);
        
        $result['NOTIFICATIONS'] = $notifications;
        $result['PAGINATION'] = $pagination;
        
        return $result;
    }

    /**
     * Get templates to web messages. We use mail templates.
     *
     * @return array
     */
    protected static function getTextMessages() {
        $by     = 'ID';
        $order  = 'ASC';
        $filter = ['LID'        => SITE_ID,
                   'EVENT_NAME' => self::$eventList];

        $text             = [];
        $eventsCollection = \CEventMessage::GetList($by, $order, $filter);

        while ($event = $eventsCollection->GetNext()) {
            $text[$event['EVENT_NAME']] = $event['~MESSAGE'];
        }

        return $text;
    }

    /**
     * Send web notifications
     *
     * @return bool
     *
     * @throws SystemException
     */
    public function send() {
        $fromUserId = (int)$this->values['UF_USER_FROM'];

        $row = ['UF_XML_ID'           => uniqid(),
                'UF_NEW_NOTIFICATION' => 1,
                'UF_ACTIVE'           => 1,
                'UF_MESSAGE'          => $this->values['UF_MESSAGE'],
                'UF_TITLE'            => $this->values['UF_TITLE'],
                'UF_DATE_CREATE'      => ConvertTimeStamp(time(), 'FULL'),
                'UF_USER_TO'          => $this->values['UF_USER_TO'],
                'UF_USER_FROM'        => $fromUserId ? $fromUserId : Project::NOTIFICATION_DEFAULT_USER,
                'UF_TYPE'             => $this->values['UF_TYPE']];

        self::getEntity()->add($row);

        return true;
    }
}
