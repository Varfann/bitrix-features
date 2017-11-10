<?

namespace DoctorNet\Helpers;

use Bitrix\Main\Loader, Bitrix\Highloadblock as HL;

Loader::includeModule('highloadblock');

/**
 * Class DirectoryHelper
 *
 * Extended directory getters
 *
 * @author    Dmitry Panychev <panychev@code-craft.ru>
 * @version   1.0
 * @package   CodeCraft
 * @category  Bitrix, HighLoad
 * @copyright Copyright © 2016, Dmitry Panychev
 */
class DirectoryHelper extends \CIBlockPropertyDirectory {

    /**
     * Returns entity data.
     *
     * @param string $tableName       HL table name.
     * @param array  $listDescription Params for getList.
     *
     * @return array
     */
    private static function getEntityFieldsByFilter($tableName, $listDescription = []) {
        $result    = [];
        $tableName = (string)$tableName;
        if (!is_array($listDescription)) {
            $listDescription = [];
        }
        if (!empty($tableName)) {
            if (!isset(self::$hlblockCache[$tableName])) {
                self::$hlblockCache[$tableName] = HL\HighloadBlockTable::getList([
                    'select' => [
                        'TABLE_NAME',
                        'NAME',
                        'ID',
                    ],
                    'filter' => ['=TABLE_NAME' => $tableName],
                ])->fetch();
            }
            if (!empty(self::$hlblockCache[$tableName])) {
                $entity          = HL\HighloadBlockTable::compileEntity(self::$hlblockCache[$tableName]);
                $entityDataClass = $entity->getDataClass();
                if (!isset(self::$directoryMap[$tableName])) {
                    self::$directoryMap[$tableName] = $entityDataClass::getEntity()->getFields();
                }
                
                if (!isset(self::$directoryMap[$tableName]['UF_XML_ID'])) {
                    return $result;
                }
                
                $nameExist = isset(self::$directoryMap[$tableName]['UF_NAME']);
                if (!$nameExist) {
                    $listDescription['select'] = [
                        'UF_XML_ID',
                        'ID',
                    ];
                }
                $fileExists = isset(self::$directoryMap[$tableName]['UF_FILE']);
                if ($fileExists) {
                    $listDescription['select'][] = 'UF_FILE';
                }
                
                $sortExist                = isset(self::$directoryMap[$tableName]['UF_SORT']);
                $listDescription['order'] = [];
                if ($sortExist) {
                    $listDescription['order']['UF_SORT'] = 'ASC';
                    $listDescription['select'][]         = 'UF_SORT';
                }
                if ($nameExist) {
                    $listDescription['order']['UF_NAME'] = 'ASC';
                } else {
                    $listDescription['order']['UF_XML_ID'] = 'ASC';
                }
                $listDescription['order']['ID'] = 'ASC';
                
                $rsData = $entityDataClass::getList($listDescription);
                while ($arData = $rsData->fetch()) {
                    if (!$nameExist) {
                        $arData['UF_NAME'] = $arData['UF_XML_ID'];
                    }
                    $arData['SORT'] = ($sortExist ? $arData['UF_SORT'] : $arData['ID']);
                    $result[]       = $arData;
                }
                unset($arData, $rsData);
            }
        }
        
        return $result;
    }
    
    /**
     * Returns data for smart filter.
     *
     * @param array $propertyList Property description.
     * @param array $value      Current value.
     *
     * @return false|array
     */
    public static function GetExtendedValue($propertyList, $value) {
        if (!$value) {
            return false;
        }
        
        if (empty($propertyList['USER_TYPE_SETTINGS']['TABLE_NAME'])) {
            return false;
        }
        
        $tableName = $propertyList['USER_TYPE_SETTINGS']['TABLE_NAME'];
        if (!isset(self::$arItemCache[$tableName])) {
            self::$arItemCache[$tableName] = [];
        }
        
        if (!isset(self::$arItemCache[$tableName][$value])) {
            $arData = self::getEntityFieldsByFilter($propertyList['USER_TYPE_SETTINGS']['TABLE_NAME'], [
                'select' => [
                    'UF_XML_ID',
                    'UF_DESCRIPTION',
                    'UF_NAME',
                ],
                'filter' => ['=UF_XML_ID' => $value],
            ]);
            
            if (!empty($arData)) {
                $arData = current($arData);
                if (isset($arData['UF_XML_ID']) && $arData['UF_XML_ID'] == $value) {
                    $arData['VALUE'] = $arData['UF_NAME'];
                    if (isset($arData['UF_FILE'])) {
                        $arData['FILE_ID'] = $arData['UF_FILE'];
                    }
                    self::$arItemCache[$tableName][$value] = $arData;
                }
            }
        }
        
        if (isset(self::$arItemCache[$tableName][$value])) {
            return self::$arItemCache[$tableName][$value];
        }
        
        return false;
    }
}