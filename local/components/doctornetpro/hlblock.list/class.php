<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Highloadblock\HighloadBlockTable as HLBT;
use Bitrix\Main\Loader;
use DoctorNet\Helper;
use Bitrix\Main\SystemException;
use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;

class HLBlockList extends CBitrixComponent
{
    public function onIncludeComponentLang() {
        $this->includeComponentLang(basename(__FILE__));
        Loc::loadMessages(__FILE__);
    }

    private $paramList = [];

    public function onPrepareComponentParams(&$paramList) {
        $paramList['CACHE_TIME'] = isset($paramList['CACHE_TIME']) ? $paramList['CACHE_TIME'] : 36000000;
        $paramList['HLBLOCK_ID'] = (int)$paramList['HLBLOCK_ID'];
        $paramList['SORT_BY']    = isset($paramList['SORT_BY']) ? $paramList['SORT_BY'] : 'ID';
        $paramList['SORT_ORDER'] = isset($paramList['SORT_ORDER']) ? $paramList['SORT_ORDER'] : 'DESC';
        $this->paramList         = $paramList;
    }

    private function checkComponent() {
        if (!Loader::includeModule('highloadblock')) {
            $this->AbortResultCache();
            ShowError(Loc::getMessage('HLBLOCK_MODULE_NOT_INSTALLED'));

            return false;
        }

        return true;
    }

    private function prepareFilter() {
        $filter = [];

        return $filter;
    }

    private function prepareSort() {
        $sort = [$this->paramList['SORT_BY'] => $this->paramList['SORT_ORDER'],];

        return $sort;
    }

    public function getEntityList($hlBlockId) {
        $entity = Helper::getHlBlockEntityObject($hlBlockId);
        $params = ['select' => ['*'],
                   'order'  => $this->prepareSort(),
                   'filter' => $this->prepareFilter(),];

        return $entity->getList($params)->fetchAll();
    }

    public function executeComponent() {
        if ($this->StartResultCache()) {
            if (!$this->checkComponent()) {
                return;
            }
            $entity = $this->getEntityList($this->paramList['HLBLOCK_ID']);
            $this->arResult = $entity;
        }
        
        $this->includeComponentTemplate();

        return $this->arResult;
    }
}