<?

/**
 * Class Tools/IBlock
 *
 * @author    Dmitry Panychev <panychev@code-craft.ru>
 * @version   1.0
 * @package   CodeCraft
 * @category  Tools
 * @copyright Copyright © 2016, Dmitry Panychev
 */


namespace DoctorNet\Tools;

use \DoctorNet\Helpers\DirectoryHelper;

class Tools {
    /**
     * Simple \CIBlockElement::GetList wrapper
     *
     * @param array      $filter
     * @param array      $select
     * @param array      $sort
     * @param array|bool $pagination
     * @param array|bool $group
     * @param bool       $useGetNextElement
     *
     * @return mixed|array
     */
    public static function getIBlockElementList(array $filter, $select = [], $sort = [], $pagination = false, $group = false, $useGetNextElement = false) {
        $result = [];

        $elementCollection = \CIBlockElement::GetList($sort, $filter, $group, $pagination, $select);

        if ($group !== false) {
            $result = $elementCollection;
        } else {
            if ($useGetNextElement) {
                while ($elementEntity = $elementCollection->GetNextElement()) {
                    $element               = $elementEntity->GetFields();
                    $element['PROPERTIES'] = $elementEntity->GetProperties();

                    foreach ($element['PROPERTIES'] as $index => $item) {
                        if ($item['USER_TYPE'] == 'directory') {
                            $value = DirectoryHelper::GetExtendedValue($item, $item['VALUE']);

                            $element['DISPLAY_PROPERTIES'][$index] = $value;
                        }
                    }

                    $result[] = $element;
                }
            } else {
                while ($element = $elementCollection->GetNext()) {
                    $result[] = $element;
                }
            }
        }

        return $result;
    }
    
    /**
     * Get full IBlock element data by id
     * 
     * @param int $id
     *
     * @return array
     */
    public static function getElementByElementId($id) {
        $element = [];
        
        if ((int)$id) {
            $elementEntity = \CIBlockElement::GetByID($id)->GetNextElement();
            
            if ($elementEntity) {
                $element               = $elementEntity->GetFields();
                $element['PROPERTIES'] = $elementEntity->GetProperties();
            }
        }
        
        return $element;
    }
}