<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use \DoctorNet\Tools\File;

foreach ($arResult as &$item) {
    $item['UF_PHOTO'] = File::resizeImage($item['UF_PHOTO'],220,300);
    $item['UF_PICTURE'] = File::resizeImage($item['UF_PICTURE'],80,70);
}