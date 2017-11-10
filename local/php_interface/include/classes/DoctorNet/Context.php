<?

namespace DoctorNet;

use Bitrix\Main\Application;
use Bitrix\Main\Context as HitContext;
use Bitrix\Main\HttpRequest;

class Context
{
    private static $instance;
    private        $pageRouter = null;
    private        $context    = null;
    private        $request    = null;

    /**
     * PageRouter constructor.
     *
     * @global \CMain $APPLICATION
     */
    private function __construct() {
        $this->setPageRouter();
        $this->setContext();
        $this->setRequest();
    }

    private function setPageRouter() {
        $this->pageRouter = PageRouter::getInstance();
    }

    private function setContext() {
        $this->context = Application::getInstance()->getContext();
    }

    /**
     * Set current request
     */
    private function setRequest() {
        $context = $this->getContext();

        $this->request = $context->getRequest();
    }

    /**
     * @return HitContext
     */
    public function getContext() {
        if (!$this->context) {
            $this->setContext();
        }

        return $this->context;
    }

    /**
     * @return $this
     */
    public static function getInstance() {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return PageRouter
     */
    public function getPageRouter() {
        if (!$this->pageRouter) {
            $this->setPageRouter();
        }

        return $this->pageRouter;
    }

    /**
     * @return HttpRequest
     */
    public function getRequest() {
        if (!$this->request) {
            $this->setRequest();
        }

        return $this->request;
    }

}