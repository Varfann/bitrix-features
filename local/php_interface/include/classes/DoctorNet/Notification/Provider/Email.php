<?

namespace DoctorNet\Notification\Provider;

use DoctorNet\Notification\Notification;

/**
 * Class Email - provide Limikon Email notifications
 *
 * @package DoctorNet\Notification\Provider
 */
class Email extends Notification
{
    public function send() {
        $by             = 'id';
        $order          = 'asc';
        $filter         = ['ID' => implode(' | ', $this->idList),];
        $userCollection = \CUser::GetList($by, $order, $filter);

        while ($user = $userCollection->Fetch()) {
            $this->values['EMAIL'] = $user['EMAIL'];
            \CEvent::Send($this->template, $user['LID'], $this->values, 'Y');
        }

        unset($this->values['EMAIL']);

        return true;
    }

}