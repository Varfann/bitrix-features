<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \DoctorNet\Menu\Menu;

if (empty($arResult)) {
    return;
}

$oMenu = new Menu($arResult, $arParams);

$drawMenuLevel = function ($menu = [], $title = '') use ($oMenu) {
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
        $outString .= $oMenu->drawMenuNextLevel($item['CHILDREN'], $item['DEPTH_LEVEL'] + 1, $item['TEXT']);
        $outString .= '</li>';
    }
    $outString .= '</ul>';

    return $outString;
};

$oMenu->setMarkupFunction($drawMenuLevel, 0);
$oMenu->drawMenu();
