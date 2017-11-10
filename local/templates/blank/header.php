<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use DoctorNet\PageRouter, DoctorNet\CurrentAsset;

$pageRouter = PageRouter::getInstance();
CurrentAsset::setProjectAsset();

$pageRouter->setPageProperty('robots', 'noindex');

?><!DOCTYPE html>
<html lang="<?=LANGUAGE_ID?>">
<head>
    <?$APPLICATION->ShowHead()?>
    <title><?$APPLICATION->ShowTitle();?></title>
</head>
<body>
<?$APPLICATION->ShowPanel();?>
