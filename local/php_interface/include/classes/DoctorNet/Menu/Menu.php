<?php
/**
 * Abstract class Menu
 * Provides recursive menu
 *
 * @author    Roman Shershnev <readytoban@gmail.com>
 * @version   1.0
 * @package   CodeCraft
 * @category  Menu
 * @copyright Copyright © 2015, Roman Shershnev
 */

namespace DoctorNet\Menu;

class Menu {

    protected $menu     = [];
    protected $maxDepth = 4;

    protected $callable = [];

    /**
     * @param array $arResult
     * @param array $arParams
     */
    public function __construct($arResult = [], $arParams = []) {
        $this->init($arResult, $arParams);
        $this->_setDefaultMarkupFunction();
    }

    /**
     * @param array $arResult
     * @param array $arParams
     *
     * @return $this
     */
    public function init($arResult = [], $arParams = []) {
        $this->_setMenu($arResult)->_setParams($arParams);

        return $this;
    }

    private function _setMenu($arResult) {
        $this->menu = self::_makeTree(self::_resetMenuIndexes($arResult));

        return $this;
    }

    private function _setParams($arParams) {
        if (isset($arParams['MAX_LEVEL'])) {
            $this->maxDepth = $arParams['MAX_LEVEL'];
        }

        return $this;
    }

    private static function _resetMenuIndexes($menu) {
        $result = [];
        foreach ($menu as $index => $item) {
            $result[] = $item;
        }

        return $result;
    }

    /**
     * Recursive makes tree from standard Bitrix $arResult
     *
     * @param array $inputMenu
     * @param int   $parentIndex
     *
     * @return array
     */
    private static function _makeTree($inputMenu = [], $parentIndex = 0) {
        if (!isSet($inputMenu[$parentIndex])) {
            return $inputMenu;
        } else {
            if ($inputMenu[$parentIndex]['IS_PARENT']) {
                $parentDepth = $inputMenu[$parentIndex]['DEPTH_LEVEL'];
                $index       = $parentIndex + 1;
                $childMenu   = [];
                while (isset($inputMenu[$index]) && $inputMenu[$index]['DEPTH_LEVEL'] > $parentDepth) {
                    $childMenu[] = $inputMenu[$index];
                    unset($inputMenu[$index]);
                    $index++;
                }
                unset($index);
                $childMenu                           = self::_makeTree($childMenu);
                $inputMenu[$parentIndex]['CHILDREN'] = $childMenu;
                unset($childMenu);
            }
            $inputMenu = self::_resetMenuIndexes($inputMenu);

            return self::_makeTree($inputMenu, $parentIndex + 1);
        }
    }

    /**
     *  Callback that returns default menu markup
     */
    protected function _setDefaultMarkupFunction() {
        $this->callable[0] = function($menu = [], $title = '') {
            if (empty($menu)) {
                return '';
            }

            $outString = '<ul>';
            foreach ($menu as $index => $item) {

                $class = [];
                if (!isSet($arResult[$index - 1])) {
                    $class[] = 'first';
                } elseif (!isSet($arResult[$index + 1])) {
                    $class[] = 'last';
                };
                if ($item["SELECTED"]) {
                    $class[] = 'selected';
                };

                $outString .= '<li';
                if (!empty($class)) {
                    $outString .= ' class="' . implode($class, ' ') . '""';
                }
                $outString .= '>';
                $outString .= '<a href="' . $item['LINK'] . '"';
                // if (!empty($class)) {
                //     $outString .= ' class="' . implode($class, ' ') . '""';
                // }
                $outString .= '>';
                $outString .= $item['TEXT'];
                $outString .= '</a>';
                $outString .= $this->_drawMenuNextLevel($item['CHILDREN'], $item['DEPTH_LEVEL'] + 1, $item['TEXT']);
                $outString .= '</li>';
            }
            $outString .= '</ul>';

            return $outString;
        };
    }

    /**
     * @param callable $func
     * @param int|null $level
     *
     * @return $this
     */public function setMarkupFunction(callable $func, $level = null) {
        if (!is_null($level) && intval($level) >= 0) {
            $this->callable[$level] = $func;
        } else {
            $this->callable[] = $func;
        }

        return $this;
    }

    /**
     * Prints Menu
     *
     * @return $this
     */
    public function drawMenu() {
        echo $this->_drawMenuNextLevel($this->menu);

        return $this;
    }

    /**
     * @param array  $menu
     * @param int    $depth
     * @param string $title
     *
     * @return string
     */
    public function drawMenuNextLevel($menu = [], $depth = 1, $title = '') {
        // @todo do it better
        return $this->_drawMenuNextLevel($menu, $depth, $title);
    }

    /**
     * @param array  $menu
     * @param int    $depth
     * @param string $title
     *
     * @return string
     */
    protected function _drawMenuNextLevel($menu = [], $depth = 1, $title = '') {
        if ($depth > $this->maxDepth || $depth < 1) {
            return '';
        }

        if (isset($this->callable[$depth])) {
            return $this->callable[$depth]($menu, $title);
        } else {
            return $this->callable[0]($menu, $title);
        }
    }

}