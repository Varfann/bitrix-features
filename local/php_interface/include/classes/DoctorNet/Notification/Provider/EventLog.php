<?
/**
 * Created by PhpStorm.
 * User: Varfann
 * Date: 04.12.2016
 * Time: 21:27
 */

namespace DoctorNet\Notification\Provider;

use DoctorNet\Notification\Notification;

class EventLog extends Notification
{

    /**
     *
     * $this->values['ITEM_ID'] - contains id of company
     *
     */
    public function send() {
        $eventLog = new \CEventLog();
        $fields   = ['SEVERITY'      => 'SECURITY',
                     'MODULE_ID'     => 'main',
                     'AUDIT_TYPE_ID' => $this->values['AUDIT_TYPE_ID'],
                     'ITEM_ID'       => $this->values['ITEM_ID'],
                     'USER_ID'       => $this->values['USER_ID'],
                     'DESCRIPTION'   => $this->values['DESCRIPTION']];
        $eventLog->Add($fields);
    }

}