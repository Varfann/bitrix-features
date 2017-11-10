<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;
use Bitrix\Highloadblock\HighloadBlockTable as HLBT;

Loc::loadLanguageFile(__FILE__);

if (!CModule::IncludeModule('highloadblock')) {
    return;
}

$params = ['select'      => ['ID','NAME',],
           'filter'      => [],
           'order'       => ['ID' => 'ASC'],];

$hlBlock = HLBT::getList($params);

while ($item = $hlBlock->Fetch()) {
    $hlBlockList[$item['ID']] = $item['NAME'];
}

$sort       = array('ASC'  => Loc::getMessage('CP_ASC'),
                    'DESC' => Loc::getMessage('CP_DESC'));
$sortFields = array('ID'      => Loc::getMessage('CP_FID'),
                    'UF_NAME' => Loc::getMessage('CP_FNAME'),
                    'UF_DATE' => Loc::getMessage('CP_FACT'),);

$arComponentParameters = array('GROUPS'     => array(),
                               'PARAMETERS' => array('HLBLOCK_ID'   => array('PARENT'            => 'BASE',
                                                                             'NAME'              => Loc::getMessage('CP_HLBLOCK_ID'),
                                                                             'TYPE'              => 'LIST',
                                                                             'ADDITIONAL_VALUES' => 'Y',
                                                                             'VALUES'            => $hlBlockList,
                                                                             'REFRESH'           => 'Y',),
                                                     'SORT_BY'      => array('PARENT'            => 'DATA_SOURCE',
                                                                             'NAME'              => Loc::getMessage('CP_HLBORD'),
                                                                             'TYPE'              => 'LIST',
                                                                             'DEFAULT'           => 'ACTIVE_FROM',
                                                                             'VALUES'            => $sortFields,
                                                                             'ADDITIONAL_VALUES' => 'Y',),
                                                     'SORT_ORDER'   => array('PARENT'            => 'DATA_SOURCE',
                                                                             'NAME'              => Loc::getMessage('CP_HLBBY'),
                                                                             'TYPE'              => 'LIST',
                                                                             'DEFAULT'           => 'DESC',
                                                                             'VALUES'            => $sort,
                                                                             'ADDITIONAL_VALUES' => 'Y',),
                                                     'CACHE_TIME'   => array('DEFAULT' => 36000000),

                               ),);