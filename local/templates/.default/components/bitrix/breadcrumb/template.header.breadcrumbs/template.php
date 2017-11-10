<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

if (!$arResult) {
    return '';
}

$position = 0;

$result = '<div class="g-row">
    <div class="g-col">
        <div class="b-breadcrumbs">
            <span class="signpost">' . Loc::getMessage('BCMB_YOU_ARE_HERE') . '</span>
            <ol class="items-container" itemscope="" itemtype="http://schema.org/BreadcrumbList">';

foreach ($arResult as $item) {
    $result .= '<li class="item" itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
    <a class="text g-link-s" href="/" title="' . $item['TITLE'] . '" itemprop="item">
        <span itemprop="name">' . $item['TITLE'] . '</span>
    </a>
    <i class="g-icon icon-arrow-right"></i>
    <meta itemprop="position" content="' . ($position++) . '">
</li>';
}

$result .= '</ol>
        </div>
    </div>
</div>';

return $result;