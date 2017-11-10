<?

namespace DoctorNet\Helper\HighloadBlock;

use Bitrix\Main\Loader;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Entity\Base;
use Bitrix\Main\SystemException;
use DoctorNet\Helper\D7Table;
use DoctorNet\Helper\UserType;

Loader::includeModule('highloadblock');

class HighloadBlock
{
    use D7Table;
    
    /**
     * @param $data
     * @param $fields
     *
     * @return bool
     * @throws SystemException
     */
    public static function hlBlockAdd($data, $fields) {
        $newHLBlockId = HighloadBlockTable::add($data)->getId();

        foreach ($fields as $field) {
            self::addHlBlockField($newHLBlockId, $field);
        }

        return $newHLBlockId;
    }

    /**
     * @param       $hlBlockId
     * @param array $propData
     */
    public static function addHlBlockField($hlBlockId, $propData) {
        $enumerationList       = [];
        $propData['ENTITY_ID'] = 'HLBLOCK_' . $hlBlockId;

        $userTypeEntity = new \CUserTypeEntity();

        if ($propData['LIST'] && $propData['USER_TYPE_ID'] == 'enumeration') {
            $enumerationList = $propData['LIST'];
            unset($propData['LIST']);
        }

        $fieldId = $userTypeEntity->Add($propData);

        if (!$fieldId) {
            global $APPLICATION;

            $APPLICATION->ThrowException('Ошибка установки highload-блока');
        }

        if ($fieldId && $enumerationList) {
            $userFieldEnum = new \CUserFieldEnum();

            $userFieldEnum->SetEnumValues($fieldId, $enumerationList);
        }
    }

    /**
     * @param string|int $id
     */
    public static function hlBlockDelete($id) {
        if (!(int)$id) {
            $id = self::getHlBlockIdByName($id);
        }

        if ((int)$id) {
            HighloadBlockTable::delete($id);
        }
    }

    /**
     * @param string $hlBlockName
     *
     * @return bool
     * @throws \Bitrix\Main\ArgumentException
     */
    public static function getHlBlockIdByName($hlBlockName) {
        if (!$hlBlockName) {
            return false;
        }

        $filter = ['=NAME' => $hlBlockName];
        $select = ['ID'];
        $limit  = 1;

        $params = self::prepareParameters($filter, $select, [], $limit);

        $hlBlock = HighloadBlockTable::getList($params)->fetch();

        return $hlBlock['ID'];
    }

    /**
     * @param string $hlBlockId
     *
     * @return bool
     * @throws \Bitrix\Main\ArgumentException
     */
    public static function getHlBlockNameById($hlBlockId) {

        if (empty($hlBlockId)) {
            return false;
        }

        $filter = ['=ID' => $hlBlockId];
        $select = ['NAME'];
        $limit  = 1;

        $params = self::prepareParameters($filter, $select, [], $limit);

        $hlBlock = HighloadBlockTable::getList($params)->fetch();

        return $hlBlock['NAME'];
    }

    /**
     * Getting all fields from HighLoad IBlock
     *
     * @param $hlBlockId
     *
     * @throws SystemException
     *
     * @return array
     */
    public static function getListHlBlockFields($hlBlockId) {

        $hlBlockId = intval($hlBlockId);

        if ($hlBlockId == 0) {
            throw new SystemException('HighLoad block Id not defined');
        }

        $fieldsList = [];

        $filter             = ['ID' => $hlBlockId];
        $hlBlocksCollection = self::getHighloadBlockCollection($filter);

        foreach ($hlBlocksCollection->getFields() as $field) {
            $fieldsList[] = $field;
        }

        return $fieldsList;
    }

    public static function getHighloadBlockCollection($filter, $limit = 1) {
        $select = ['*'];
        $order  = ['ID' => 'ASC'];

        $params                = self::prepareParameters($filter, $select, $order, $limit);
        $params['count_total'] = true;

        return HighloadBlockTable::getList($params);
    }


    /**
     * @param int $hlBlockId
     *
     * @return array
     * @throws SystemException
     * @throws \Bitrix\Main\ArgumentException
     */
    public static function getHlBlockItems($hlBlockId, $filter = []) {
        $hlBlockId = (int)$hlBlockId;

        $items = [];

        if ($hlBlockId == 0) {
            throw new SystemException('HighLoad block Id not defined');
        }

        $hlBlocksCollection = HighloadBlockTable::getById($hlBlockId)->fetch();
        $entity             = HighloadBlockTable::compileEntity($hlBlocksCollection);

        $entityCode      = $entity->getDataClass();
        $itemsCollection = $entityCode::getList(['select' => ['*'],
                                                 'order'  => ['ID' => 'ASC'],
                                                 'filter' => $filter]);

        while ($item = $itemsCollection->fetch()) {
            $items[] = $item;
        }

        return $items;

    }

    /**
     * Getting all items from HighLoad block
     *
     * @param $hlBlockId
     *
     * @throws SystemException
     *
     * @return string json
     */
    public static function getListHlBlockItems($hlBlockId) {
        $hlBlockId = intval($hlBlockId);

        $items = [];

        if ($hlBlockId == 0) {
            throw new SystemException('HighLoad block Id not defined');
        }

        $filter             = ['ID' => $hlBlockId];
        $hlBlocksCollection = self::getHighloadBlockCollection($filter);

        $entity = HighloadBlockTable::compileEntity($hlBlocksCollection);

        $entityCode = $entity->getDataClass();

        $itemsCollection = $entityCode::getList(array('select' => array('*'),
                                                      'order'  => array('ID' => 'ASC'),
                                                      'filter' => array(),));

        while ($item = $itemsCollection->fetch()) {
            $items[] = $item;
        }
        $result = json_encode($items);

        return $result;
    }

    /**
     * @param bool $name
     * @param bool $tableName
     *
     * @return bool
     * @throws SystemException
     * @throws \Bitrix\Main\ArgumentException
     */
    public static function hasHlBlock($name = false, $tableName = false) {
        if (!strlen($name) && !strlen($tableName)) {
            throw new SystemException('HighLoad block Name and Table Name not defined');
        }

        if ($name) {
            $filter['NAME'] = $name;
        } else {
            $filter['TABLE_NAME'] = $tableName;
        }

        $hlBlocksCollection = self::getHighloadBlockCollection($filter);

        return $hlBlocksCollection->getSelectedRowsCount() > 0;
    }

    /**
     * @param $hlBlockId
     *
     * @return Base
     */
    public static function getHlBlockEntityObject($hlBlockId) {
        $entityTable     = self::getHlBlockTableObject($hlBlockId);
        $entityDataClass = $entityTable->getDataClass();
        $entity          = new $entityDataClass();

        return $entity;
    }

    /**
     * @param $hlBlockId
     *
     * @return Base
     * @throws SystemException
     */
    public static function getHlBlockTableObject($hlBlockId) {
        $hlBlock     = HighloadBlockTable::getById($hlBlockId)->fetch();
        $entityTable = HighloadBlockTable::compileEntity($hlBlock);

        return $entityTable;
    }

    /**
     * @param string $highLoadBlockName
     * @param string $propertyCode
     *
     * @throws \Exception
     *
     * @return array
     */
    public static function getFieldEnumerationPropertyList($highLoadBlockName, $propertyCode) {
        $type = 'HLBLOCK_' . self::getHlBlockIdByName($highLoadBlockName);

        $entity = \CUserTypeEntity::GetList([], ['ENTITY_ID'  => $type,
                                                 'FIELD_NAME' => $propertyCode])->Fetch();

        if (!$entity['ID']) {
            throw new \Exception('Unknown entity');
        }

        return UserType::getEnumerationFieldValueList($entity['ID']);
    }

    /**
     * @param $userTypeId
     *
     * @return mixed
     */
    protected static function _addDefaultSettings($userTypeId) {
        global $USER_FIELD_MANAGER;

        $userType = $USER_FIELD_MANAGER->GetUserType($userTypeId);

        $userType = new $userType['CLASS_NAME']();

        $settings = $userType->PrepareSettings([]); // it can have more parameters, that one

        return $settings;
    }

    /**
     * @param $propData
     * @param $requiredFields
     *
     * @return bool
     * @throws \Exception
     */
    protected static function _checkPropData($propData, $requiredFields) {
        foreach ($requiredFields as $requiredField) {
            if (!$propData[$requiredField]) {
                throw new \Exception(sprintf('Required field %s is missing', $requiredField));
            }

            if ($requiredField == 'FIELD_NAME' && substr($propData[$requiredField], 0, 3) !== 'UF_') {
                throw new \Exception('"FIELD_NAME" must start with "UF_"');
            }
        }

        return true;
    }
}