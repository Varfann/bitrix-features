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

class PageRouter
{
    //Константы для показа элементов в head-page
    const FEED_BACK_IN_HEAD_PAGE      = 'FEED_BACK_IN_HEAD_PAGE';
    const BALANCE_IN_HEAD_PAGE        = 'BALANCE_IN_HEAD_PAGE';
    const TAB_MENU_IN_HEAD_PAGE       = 'TAB_MENU_IN_HEAD_PAGE';
    const MENU_IN_HEAD_PAGE           = 'MENU_IN_HEAD_PAGE';
    const TITLE_TAB_MENU_IN_HEAD_PAGE = 'TITLE_TAB_MENU_IN_HEAD_PAGE';

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
    public function isSearchResultPage() {
        return preg_match('~^/search/~', $this->page);
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
    public function isFavoritePage() {
        return $this->getPage() == '/personal/favorite/';
    }

    /**
     * @return string
     */
    public function getPage() {
        return $this->page;
    }

    /**
     * @param string $add
     * @param array  $kill
     *
     * @return string
     */
    public function getPageWithParam($add = '', array $kill = []) {
        global $APPLICATION;

        return $APPLICATION->GetCurPageParam($add, $kill);
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
     * @param $sectionName
     *
     * @return bool
     */
    public function isSectionPage($sectionName) {
        return preg_match('~^/' . $sectionName . '/~', $this->page) > 0;
    }

    /**
     * @return bool
     */
    public function hasContentBlock() {
        return $this->isSectionPage('personal/auth')
               || $this->isSectionPage('personal/account')
               || $this->isSectionPage('about/news')
               || $this->isSectionPage('personal/process')
               || $this->isSectionPage('personal/favorites')
               || $this->isSectionPage('personal/pay')
               || $this->isAbout()
               || $this->isNews()
               || $this->isNewsDetail()
               || $this->isNewAdvertConfirmPage()
               || $this->isNewAdvertSection()
               || $this->isSectionPage('services');
    }

    /**
     * @return bool
     */
    public function isAbout() {
        return $this->getPage() == '/about/';
    }

    /**
     * @return bool
     */
    public function isNews() {
        return $this->isSubSection('about/news') || $this->isSectionIndex('about/news');
    }

    /**
     * @return bool
     */
    public function isNewsDetail() {
        return $this->isDetailPage('about/news');
    }

    /**
     * @return bool
     */
    public function isNewAdvertConfirmPage() {
        return $this->isSectionPage('adverts/editor') && isset($_REQUEST['confirm']);
    }

    /**
     * @return bool
     */
    public function isNewAdvertSection() {
        return $this->isSectionPage('adverts/editor') && !isset($_REQUEST['confirm']);
    }

    /**
     * @return bool
     */
    public function hasCatalogMenu() {
        return $this->isIndex();
        /*|| ($this->isAdverts() && !$this->isNewAdvertSection()
            && !$this->isNewAdvertConfirmPage())*/
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
    public function isAdverts() {
        return $this->isSectionPage('adverts');
    }

    /**
     * @return bool
     */
    public function hasAdvertFilter() {
        return $this->isIndex();
    }

    /**
     * @return bool
     */
    public function isNewAdvertPage() {
        return $this->getPage() == '/adverts/editor/';
    }

    /**
     * @return string
     */
    public function getContentClass() {
        if ($this->isNewAdvertSection()) {
            return 'js-submit-page';
        } elseif ($this->isSectionPage('services')) {
            return 'p-content js-my-service-page';
        } elseif ($this->isSectionPage('personal/process')) {
            return 'p-content js-my-ads';
        }

        return 'p-content';
    }

    /**
     * Check content block level 2.
     *
     * @return bool
     */
    public function hasContentBlockLevel2() {
        return $this->isSectionPage('faq')
               || $this->isSectionPage('about/news')
               || $this->isAbout()
               || $this->isNews()
               || $this->isNewsDetail()
               || $this->isNewAdvertConfirmPage()
               || $this->isNewAdvertSection()
               || $this->isSectionPage('services');
    }

    /**
     * @return bool
     */
    public function hasContentForm() {
        return $this->isNewAdvertSection();
    }

    /**
     * Returns class for content level 2.
     *
     * @return string
     */
    public function getContentBlockLevel2Class() {
        $class = '';

        if ($this->isSectionPage('faq')
            || $this->isSectionPage('services')
        ) {
            $class = 'js-scroll-list';
        } elseif ($this->isNewAdvertSection()) {
            $class = 'js-form';
        } elseif ($this->isNewAdvertConfirmPage()) {
            $class = 'b-profile';
        } elseif ($this->isNews()) {
            $class = 'b-catalog-cards';
        } elseif ($this->isNewsDetail()) {
            $class = 'b-news-items';
        } elseif ($this->isSectionPage('about/news')) {
            $class = 'b-catalog-cards';
        }

        return $class;
    }

    /**
     * @return string
     */
    public function getHeadBlockClass() {
        $class = '';

        if ($this->isSectionPage('personal/chat')) {
            $class = 'js-messages-page';
        }

        return $class;
    }

    /**
     * Check content block level 2.
     *
     * @return bool
     */
    public function hasContentBlockLevel3() {
        return $this->isSectionPage('faq')
               || $this->isSectionPage('personal/account')
               || $this->isSectionPage('personal/pay')
               || $this->isSectionPage('about/news')
               || $this->isNews()
               || $this->isNewsDetail();
    }

    /**
     * Returns class for content level 2.
     *
     * @return string
     */
    public function getContentBlockLevel3Class() {

        return 'container';
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
     * @param $sectionName
     *
     * @return bool
     */
    public function isSection($sectionName) {
        return ($this->isSubSection($sectionName) || $this->isSectionPage($sectionName)
                || $this->isDetailPage($sectionName));
    }

    /**
     * @param $sectionName
     *
     * @return bool
     */
    public function isSubSection($sectionName) {
        return preg_match('~^/' . $sectionName . '/[^/]+/$~', $this->page) > 0;
    }

    /**
     * @param $sectionName
     *
     * @return bool
     */
    public function isDetailPage($sectionName) {
        return preg_match('~^/' . $sectionName . '/[^/]+/[^/]+/$~', $this->page) > 0;
    }

    /**
     * @param $sectionName
     *
     * @return bool
     */
    public function isSectionIndex($sectionName) {
        return preg_match('~^/' . $sectionName . '/$~', $this->page) > 0;
    }

    /**
     * Example of getter
     *
     * @return string
     */
    public function getBodyClass() {
        return !($this->isDetailPage('review') || $this->isDetailPage('news')) ? 'page-body' : '';
    }

    /**
     *  Check page have head-page block.
     *
     * @return
     */
    public function hasHeadPageBlock() {
        return $this->isSectionPage('faq')
               || $this->isSectionPage('services')
               || $this->isSectionPage('personal/account')
               || $this->isSectionPage('personal/chat')
               || $this->isSectionPage('about/news')
               || $this->isNewAdvertSection()
               || $this->isNews()
               || $this->isNewsDetail()
               || $this->isSectionPage('personal/favorites');
    }

    /**
     * Check that tab menu exists.
     *
     * @return bool
     */
    public function hasTabMenu() {
        return $this->isSectionPage('faq')
               || $this->isSectionPage('personal/account');
    }

    /**
     * @return bool
     */
    public function hasHeadPersonalProccess() {
        return $this->isSectionPage('personal/process');
    }

    /**
     * Get tab menu template.
     *
     * @return string.
     */
    public function getTabMenuTemplate() {
        return 'template.header.tab-menu';
    }

    public function getHeadpageElement() {
        if ($this->isSectionPage('services')
            || $this->isSectionPage('personal/account')
        ) {
            return self::BALANCE_IN_HEAD_PAGE;
        } elseif ($this->isSectionPage('faq')) {
            return self::FEED_BACK_IN_HEAD_PAGE;
        } elseif ($this->isSectionPage('personal/chat')) {
            return self::TAB_MENU_IN_HEAD_PAGE;
        } elseif ($this->isNewAdvertSection()) {
            return self::MENU_IN_HEAD_PAGE;
        } elseif ($this->isSectionPage('personal/favorites')) {
            return self::TITLE_TAB_MENU_IN_HEAD_PAGE;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function hasPageTopBanner() {
        return ($this->isIndex());
    }

    /**
     * @return bool
     */
    public function hasPageBreadcrumb() {
        return (($this->isAdverts() && !$this->isNewAdvertSection() && !$this->isNewAdvertConfirmPage())
                || $this->isNewsDetail());
    }

    private function __clone() {
    }
}