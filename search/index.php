<?
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');
$APPLICATION->SetTitle('Поиск');
?>
<?$APPLICATION->IncludeComponent(
	'bitrix:search.page', 
	'',
	array(
		'COMPONENT_TEMPLATE' => '',
		'RESTART' => 'Y',
		'NO_WORD_LOGIC' => 'N',
		'CHECK_DATES' => 'N',
		'USE_TITLE_RANK' => 'N',
		'DEFAULT_SORT' => 'rank',
		'FILTER_NAME' => '',
		'SHOW_WHERE' => 'Y',
		'SHOW_WHEN' => 'Y',
		'PAGE_RESULT_COUNT' => '15',
		'AJAX_MODE' => 'Y',
		'AJAX_OPTION_JUMP' => 'N',
		'AJAX_OPTION_STYLE' => 'Y',
		'AJAX_OPTION_HISTORY' => 'N',
		'AJAX_OPTION_ADDITIONAL' => '',
		'CACHE_TYPE' => 'A',
		'CACHE_TIME' => '3600',
		'USE_LANGUAGE_GUESS' => 'Y',
		'USE_SUGGEST' => 'N',
		'DISPLAY_TOP_PAGER' => 'Y',
		'DISPLAY_BOTTOM_PAGER' => 'Y',
		'PAGER_TITLE' => 'Результаты поиска',
		'PAGER_SHOW_ALWAYS' => 'Y',
		'PAGER_TEMPLATE' => ''
	),
	false
);?>
<?require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');