<?
/**
 * Created by PhpStorm.
 * User: Varfann
 * Date: 04.02.2017
 * Time: 16:56
 */

namespace DoctorNet\Notification;

use DoctorNet\Notification\Provider\Web;
use DoctorNet\Notification\Provider\Email;
use DoctorNet\Notification\Provider\EventLog;

class NotificationTool
{

    /**
     * @param $values
     *
     * @return mixed
     */
    public static function addWebMessage($values) {
        $web = new Web();
        $web->setTemplate('.default', $values);
        $web->send();

        return $values;
    }

    /**
     * @param $values
     *
     * @return mixed
     */
    public static function addLogMessage($values) {
        $log = new EventLog();
        $log->setTemplate('.default', $values);
        $log->send();

        return $values;
    }
}