<?

namespace DoctorNet\Notification;

abstract class Notification implements NotificationInterface
{

    protected $template   = '';
    protected $templateId = 0;
    protected $values     = [];
    protected $idList     = [];

    /**
     * Sends a notification
     *
     * @return bool
     */
    abstract public function send();

    /**
     * @param array|int $idList User Ids to send a notification
     *
     * @return $this
     */
    final public function setRecipient($idList) {
        if (!is_array($idList)) {
            $idList = [$idList];
        }

        $this->idList = $idList;

        return $this;
    }

    /**
     * @param mixed $template   Event ID or template text
     * @param array $values     array of $key => $value to replace into template text
     * @param int   $templateId Template ID
     *
     * @return $this
     */
    final public function setTemplate($template, array $values = [], $templateId = 0) {
        $this->template   = $template;
        $this->templateId = $templateId;
        $this->values     = $values;

        return $this;
    }

}