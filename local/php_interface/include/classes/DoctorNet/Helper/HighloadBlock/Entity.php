<?

namespace DoctorNet\Helper\HighloadBlock;

use DoctorNet\Helper\D7Table;
use DoctorNet\Helper\HighloadBlock\HighloadBlock;

class Entity
{
    use D7Table;

    /**
     * @param       $hlBlockId
     * @param array $filter
     * @param array $select
     * @param array $order
     * @param int   $limit
     * @param int   $offset
     * @param array $runtime
     * @param bool  $countTotal
     *
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     */
    public static function getEntityList($hlBlockId, array $filter = [], array $select = ['*'], array $order = ['ID' => 'ASC'], $limit = 0, $offset = 0, array $runtime = [], $countTotal = true) {
        /**
         * @var \Bitrix\Main\Entity\DataManager $entity
         * @var \Bitrix\Main\Entity\Result      $result
         */

        if (!(int)$hlBlockId) {
            $hlBlockId = HighloadBlock::getHlBlockIdByName($hlBlockId);
        }

        $entity = HighloadBlock::getHlBlockEntityObject($hlBlockId);

        $params                = self::prepareParameters($filter, $select, $order, $limit, [], $offset, $runtime);
        $params['count_total'] = $countTotal;

        return $entity->getList($params)->fetchAll();
    }

    /**
     * @param $hlBlockId
     * @param $dataList
     *
     * @return bool|string
     */
    public static function entityListUpdate($hlBlockId, $dataList) {
        $error = '';

        foreach ($dataList as $data) {
            $xmlId  = $data['UF_XML_ID'];
            $result = self::entityUpdate($hlBlockId, $xmlId, $data);

            if (!$result->isSuccess()) {
                $error .= $result->getErrorMessages();
            }
        }

        return strlen($error) ? $error : false;
    }

    /**
     * @param $hlBlockId
     * @param $xmlId
     * @param $data
     *
     * @return \Bitrix\Main\Entity\Result
     */
    public static function entityUpdateByXmlId($hlBlockId, $xmlId, $data) {
        /**
         * @var \Bitrix\Main\Entity\DataManager $entity
         * @var \Bitrix\Main\Entity\Result      $result
         */
        $entityData = self::getEntityByXmlId($xmlId, $hlBlockId);

        return self::entityUpdate($hlBlockId, $entityData, $data);
    }

    /**
     * @param $hlBlockId
     * @param $id
     * @param $data
     *
     * @return \Bitrix\Main\Entity\Result
     */
    public static function entityUpdateById($hlBlockId, $id, $data) {
        /**
         * @var \Bitrix\Main\Entity\DataManager $entity
         * @var \Bitrix\Main\Entity\Result      $result
         */
        $entityData = self::getEntity($hlBlockId, ['ID' => $id]);

        return self::entityUpdate($hlBlockId, $entityData, $data);
    }

    /**
     * @param int   $hlBlockId  ID of HighloadBlock.
     * @param array $entityData Existed Entity data.
     * @param array $data       Data to update
     *
     * @return \Bitrix\Main\Entity\Result
     */
    private static function entityUpdate($hlBlockId, $entityData, $data) {
        /**
         * @var \Bitrix\Main\Entity\DataManager $entity
         * @var \Bitrix\Main\Entity\Result      $result
         */
        $entity = HighloadBlock::getHlBlockEntityObject($hlBlockId);

        if ($entityData) {
            // Элемент существует. Обновим поля.
            foreach ($entityData as $key => $value) {
                if (strtoupper($key) != 'ID' && isset($data[$key])) {
                    $entityData[$key] = $data[$key];
                }
            }
            $result = $entity->update($entityData['ID'], $entityData);
        } else {
            // Элемента не существует. Создадим.
            $result = $entity->add($data);
        }

        return $result;
    }

    /**
     * @param $xmlId
     * @param $hlBlockId
     *
     * @return array
     */
    public static function getEntityByXmlId($xmlId, $hlBlockId) {
        return self::getEntity($hlBlockId, ['UF_XML_ID' => $xmlId], ['*'], ['ID' => 'ASC'], 1);
    }

    /**
     * @param       $hlBlockId
     * @param array $filter
     * @param array $select
     * @param array $order
     * @param int   $limit
     * @param int   $offset
     * @param array $runtime
     * @param bool  $countTotal
     *
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     */
    public static function getEntity($hlBlockId, array $filter = [], array $select = ['*'], array $order = ['ID' => 'ASC'], $limit = 0, $offset = 0, array $runtime = [], $countTotal = true) {
        /**
         * @var \Bitrix\Main\Entity\DataManager $entity
         * @var \Bitrix\Main\Entity\Result      $result
         */
        if (!(int)$hlBlockId) {
            $hlBlockId = HighloadBlock::getHlBlockIdByName($hlBlockId);
        }

        $entity = HighloadBlock::getHlBlockEntityObject($hlBlockId);

        $params                = self::prepareParameters($filter, $select, $order, $limit, [], $offset, $runtime);
        $params['count_total'] = $countTotal;

        return $entity->getList($params)->fetch();
    }

}