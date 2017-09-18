<?

/**
 * Class PageRouter
 * Asset wrapper to Bitrix
 *
 * @use \CMain, \CUser
 *
 * @author    Dmitry Panychev <panychev@code-craft.ru>
 * @version   1.0
 * @package   CodeCraft
 * @category  Asset
 * @copyright Copyright © 2016, Dmitry Panychev
 */

namespace DoctorNet;

class PageRouter {

    private static $instance;
    private        $page;

    /**
     * PageRouter constructor.
     *
     * @global \CMain $APPLICATION
     */
    private function __construct() {
        global $APPLICATION;
        
        $this->page = $APPLICATION->GetCurPage();
    }
    
    private function __clone() {
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
     * @return string
     */
    public function getPage() {
        return $this->page;
    }

    /**
     * @param string $id
     * @param string $value
     * @param array  $options
     */
    public function setPageProperty($id, $value, $options = []) {
        global $APPLICATION;

        $APPLICATION->SetPageProperty($id, $value, $options);
    }

    /**
     * @return bool
     */
    public function isIndex() {
        return $this->getPage() == '/';
    }
    
    /**
     * @return bool
     */
    public function isSearchResultPage() {
        return preg_match('~^/search/~', $this->page);
    }
    
    /**
     * @param $sectionName
     *
     * @return bool
     */
    public function isDetailPage($sectionName) {
        return preg_match('~^/' . $sectionName . '/[^/]+/[^/]+/~', $this->page) > 0;
    }
    
    /**
     * @param $sectionName
     *
     * @return bool
     */
    public function isSectionPage($sectionName) {
        return preg_match('~^/' . $sectionName . '/~', $this->page) > 0;
    }
    
    /**
     * @param $sectionName
     *
     * @return bool
     */
    public function isSubSection($sectionName) {
        return preg_match('~^/' . $sectionName . '/[^/]+/~', $this->page) > 0;
    }
    
    /**
     * @param $sectionName
     *
     * @return bool
     */
    public function isSection($sectionName) {
        return ($this->isSubSection($sectionName) || $this->isSectionPage($sectionName)
                || $this->isDetailPage($sectionName));
    }
    
    /**
     * @return bool
     */
    public function is404() {
        return (defined('ERROR_404') && ERROR_404 == 'Y' || $this->page == '/404.php');
    }
    
    /**
     * @return bool
     */
    public function isCatalog() {
        return preg_match('~^/catalog/~', $this->page) > 0;
    }
    
    /**
     * @return bool
     */
    public function isFavoritePage() {
        return $this->getPage() == '/personal/favorite/';
    }
    
    /**
     * @return bool
     */
    public function isCartPage() {
        return $this->getPage() == '/personal/cart/' || $this->isOrderPage();
    }
    
    /**
     * @return bool
     */
    public function isOrderPage() {
        return $this->getPage() == '/personal/order/';
    }

    /**
     * @global \CUser $USER
     *
     * @return bool
     */
    public function isPersonalPage() {
        global $USER;
        
        return ($USER->IsAuthorized() && $this->isSectionPage('personal/account'));
    }

    /**
     * Example of check
     *
     * @return bool
     */
    public function hasPageBreadcrumbs() {
        return !$this->isSection('new');
    }

    /**
     * Example of getter
     *
     * @return string
     */
    public function getBodyClass() {
        return !($this->isDetailPage('review') || $this->isDetailPage('news')) ? 'page-body' : '';
    }
}