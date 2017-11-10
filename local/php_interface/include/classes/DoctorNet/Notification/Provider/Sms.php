<?

namespace DoctorNet\Notification\Provider;

use Bitrix\Main\Diag\Debug;
use DoctorNet\Integration\Sms\Unisender;
use DoctorNet\Notification\Notification;

/**
 * Class Sms - provided sms notifications from unisender
 *
 * @package DoctorNet\Notification\Provider
 */
class Sms extends Notification
{
    const SMS_FROM = 'LARUS.RU';

    /**
     * @return bool
     */
    public function send() {

        $sms  = new Unisender();
        $text = $this->prepareText();
        $to   = $this->getPhoneList();

        try {
            $sms->send(self::SMS_FROM, $to, $text);
        } catch (\Exception $e) {
            Debug::dumpToFile($e->getMessage());
        }

        return true;
    }

    /**
     * @return string
     */
    public function prepareText() {
        $textList = self::getTextMessages();

        $search  = [];
        $replace = [];

        foreach ($this->values as $key => $value) {
            $search[]  = '#' . trim($key, " #\t\n\r\0\x0B") . '#';
            $replace[] = trim($value);
        }

        return str_replace($search, $replace, $textList[$this->template]);
    }

    /**
     * @return array
     */
    private function getTextMessages() {
        $by     = 'ID';
        $order  = 'ASC';
        $filter = ['LID'        => SITE_ID,
                   'EVENT_NAME' => ['LIMIKON_NOTIFY_%']];

        $text = [];

        $eventCollection = \CEventMessage::GetList($by, $order, $filter);

        while ($event = $eventCollection->GetNext()) {
            $text[$event['EVENT_NAME']] = $event['~MESSAGE'];
        }

        return $text;
    }

    /**
     * @return array
     */
    private function getPhoneList() {
        $phoneList      = [];
        $userCollection = $this->getUserCollection();

        while ($u = $userCollection->Fetch()) {
            $phoneList[] = $u['PERSONAL_PHONE'];
        }

        return $phoneList;
    }

    /**
     * @return \CDBResult
     */
    private function getUserCollection() {
        $by     = 'id';
        $order  = 'asc';
        $filter = ['ID' => implode(' | ', $this->idList),];

        return \CUser::GetList($by, $order, $filter);
    }

}