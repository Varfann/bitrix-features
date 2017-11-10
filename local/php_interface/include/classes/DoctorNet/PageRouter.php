<?

/**
 * Class PageRouter
 * Asset wrapper to Bitrix
 *
 * @use \CMain, \CUser, \CSite
 *
 * @authors    Dmitry Panychev <panychev@code-craft.ru>
 * @version    1.1
 * @package    CodeCraft
 * @category   Asset
 * @copyright  Copyright � 2016, Dmitry Panychev
 */

namespace DoctorNet;

class PageRouter
{
    private static $instance;
    private        $page, $siteId, $dir;

    private function __construct() {
        global $APPLICATION;

        $this->page   = $APPLICATION->GetCurPage();
        $this->dir    = $APPLICATION->GetCurDir();
        $this->siteId = SITE_ID;
    }

    /**
     * @return PageRouter
     */
    public static function getInstance() {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Check page and authorize and redirect to internal/external site part
     */
    public function checkAuthorizedAndRedirect() {
        global $USER;

        if ($USER->IsAuthorized() && $this->isIndex() && !$_POST['bxajaxid']) {
            LocalRedirect('/personal/desktop/');
        } elseif (!$USER->IsAuthorized() && !$this->isIndex()) {
            LocalRedirect('/');
        }
    }

    /**
     * @return bool
     */
    public function isIndex() {
        return $this->getPage() == '/' || $this->getPage() == '/index.php';
    }

    /**
     * @return bool
     */
    public function isAdminPage() {
        return $this->isSection('bitrix');
    }

    /**
     * @return string
     */
    public function getPage() {
        return $this->page;
    }

    /**
     * @return string
     */
    public function getSiteId() {
        return $this->siteId;
    }

    public function getPreviousPage() {
        return getenv("HTTP_REFERER");
    }

    /**
     * @return bool
     */
    public function isMainSiteIndex() {
        return $this->isMainSite() && $this->isIndex();
    }

    /**
     * @return bool
     */
    public function isMainSite() {
        return ($this->siteId == $this->getMainSiteId());
    }

    /**
     * @return mixed
     */
    public function getMainSiteId() {
        $def    = 'def';
        $order  = 'desc';
        $filter = ['DEFAULT' => 'Y'];
        $site   = \CSite::GetList($def, $order, $filter)->Fetch();

        return $site['ID'];
    }

    /**
     * @return string
     */
    public function getDir() {
        return $this->dir;
    }

    /**
     * @param $pagePath
     *
     * @return bool
     */
    public function isPage($pagePath) {
        return $this->getPage() == $pagePath;
    }

    /**
     * @return bool
     */
    public function is404() {
        return (defined('ERROR_404') && ERROR_404 == 'Y' || $this->page == '/404.php');
    }

    /**
     * @global \CUser $USER
     *
     * @return bool
     */
    public function isPersonalPage() {
        global $USER;

        return ($USER->IsAuthorized() && $this->isSectionPage('personal/desctop'));
    }

    /**
     * @param $pageName
     *
     * @return bool
     */
    public function isSectionPage($pageName) {
        return preg_match('~^/' . $pageName . '/$~', $this->page) > 0
               || preg_match('~^/' . $pageName . '/index.php$~', $this->page) > 0;
    }

    /**
     * @return bool
     */

    public function isCatalog() {
        return $this->isSection('catalog');
    }

    /**
     * @param $sectionName
     *
     * @return bool
     */
    public function isSection($sectionName) {
        return preg_match('~^/' . $sectionName . '/~', $this->page) > 0;
    }

    /**
     * @return bool
     */
    public function isSearchResultPage() {
        return $this->isSection('search');
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
        $class = '';
        if ($this->isDealsSection()) {
            $class = ' lkpage-body__inner--w1360';
        }

        return $class;
    }

    /**
     * @param $sectionName
     *
     * @return bool
     * @TODO using of iBlock helper, when it's done
     * This is the temporary solution for checking of detail page.
     * Value of "$preg" depends on depth level of current section.
     */
    public function isDetailPage($sectionName) {

        if ($sectionName == '') {
            $preg = '/[^/]+/[^/]+/~';
        } else {
            $preg = '/[^/]+/~';
        }

        return preg_match('~^/' . $sectionName . $preg, $this->page) > 0;
    }

    /**
     * @return bool
     */
    public function isSearch() {
        return $this->isSection('search');
    }

    /**
     * @return bool
     */
    public function hasSearchForm() {
        return $this->isSearch() || $this->isMarketplaces();
    }

    /**
     * @param     $sectionName
     * @param int $level
     *
     * @return bool
     */
    public function isSubSectionPage($sectionName, $level = 0) {
        $template = '~^/' . $sectionName . '/([^/]+/)';

        if ($level) {
            $template .= '{1,' . (int)$level . '}';
        } else {
            $template .= '+';
        }

        $template .= '$~';

        return preg_match($template, $this->page) > 0;
    }
}