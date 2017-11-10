<?

namespace DoctorNet\Notification;

interface NotificationInterface {

    /**
     * Sends a notification
     *
     * @return bool
     */
    public function send();

    /**
     * @param mixed $template Template ID or template text
     * @param array $values   array of $key => $value to replace into template text
     *
     * @return $this
     */
    public function setTemplate($template, array $values = []);

    /**
     * @param array|int $ids User Ids to send a notification
     *
     * @return $this
     */
    public function setRecipient($ids);
}