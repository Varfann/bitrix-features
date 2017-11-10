<?

namespace DoctorNet\Notification;

class NotificationManager implements NotificationInterface
{
    const NOTIFICATION_WEB   = 0;
    const NOTIFICATION_EMAIL = 1;
    const NOTIFICATION_SMS   = 2;
    const NOTIFICATION_LOG   = 3;

    const NOTIFICATION_STATUS_COMPLAIN = 9;

    private $providerList = [];

    /**
     * NotificationManager constructor.
     *
     * @param array $providerList
     */
    public function __construct(array $providerList = [self::NOTIFICATION_WEB,
                                                       self::NOTIFICATION_EMAIL,
                                                       self::NOTIFICATION_SMS,
                                                       self::NOTIFICATION_LOG]) {
        $this->setProviderList($providerList);
    }

    /**
     * Set provider list
     *
     * @param array $providerList
     */
    public function setProviderList(array $providerList) {
        foreach ($providerList as $providerType) {
            $this->addProvider($providerType);
        }
    }

    /**
     * Add provider from it type
     *
     * @param int $providerType
     */
    public function addProvider($providerType) {
        switch ($providerType) {
            case self::NOTIFICATION_WEB:
                $this->providerList[self::NOTIFICATION_WEB] = new Provider\Web();
                break;
            case self::NOTIFICATION_EMAIL:
                $this->providerList[self::NOTIFICATION_EMAIL] = new Provider\Email();
                break;
            case self::NOTIFICATION_SMS:
                $this->providerList[self::NOTIFICATION_SMS] = new Provider\Sms();
                break;
            case self::NOTIFICATION_LOG:
                $this->providerList[self::NOTIFICATION_LOG] = new Provider\EventLog();
                break;
        }
    }

    /**
     * Full execute - set recipient, template and send notify
     *
     * @param       $idList
     * @param       $template
     * @param array $values
     */
    public function execute($idList, $template, array $values = []) {
        $this->setRecipient($idList)->setTemplate($template, $values)->send();
    }

    /**
     * @return $this
     */
    public function send() {
        /**
         * @var Notification $provider
         */
        foreach ($this->providerList as $provider) {
            $provider->send();
        }

        return $this;
    }

    /**
     * @param mixed $template
     * @param array $values
     *
     * @return $this
     */
    public function setTemplate($template, array $values = []) {
        /**
         * @var Notification $provider
         */
        foreach ($this->providerList as $provider) {
            $provider->setTemplate($template, $values);
        }

        return $this;
    }

    /**
     * @param array|int $idList
     *
     * @return $this
     */
    public function setRecipient($idList) {
        /**
         * @var Notification $provider
         */
        foreach ($this->providerList as $provider) {
            $provider->setRecipient($idList);
        }

        return $this;
    }

    /**
     * Remove provider through it type
     *
     * @param int $providerType
     */
    public function removeProvider($providerType) {
        if (isset($this->providerList[$providerType])) {
            unset($this->providerList[$providerType]);
        }
    }

    /**
     * Clear current provider list
     */
    public function clearProviderList() {
        $this->providerList = [];
    }
}