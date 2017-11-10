<?

namespace DoctorNet\Helper;

use Bitrix\Highloadblock\HighloadBlockTable;

/**
 * Trait D7Table
 *
 * @package DoctorNet\Helper
 */

trait D7Table
{
    /**
     * Simple prepare parameters to ORM select
     *
     * @param array $filter
     * @param array $select
     * @param array $sort
     * @param int   $limit
     * @param array $group
     * @param array $offset
     * @param array $runtime
     *
     * @return array
     */
    public static function prepareParameters($filter = [], $select = [], $sort = [], $limit = 0, $group = [], $offset = [], $runtime = []) {
        $parameters = [];

        if ($filter) {
            $parameters['filter'] = $filter;
        }

        if ($select) {
            $parameters['select'] = $select;
        }

        if ($sort) {
            $parameters['order'] = $sort;
        }

        if ($limit) {
            $parameters['limit'] = $limit;
        }

        if ($group) {
            $parameters['group'] = $group;
        }

        if ($offset) {
            $parameters['offset'] = $offset;
        }

        if ($runtime) {
            $parameters['runtime'] = $runtime;
        }

        return $parameters;
    }

    /**
     * Method to get list of elements.
     *
     * @param int $hlBlockId Highloadblock ID
     * @params array $filter Filter array
     *
     * @return array
     */
    public static function defaultGetList($hlBlockId, array $params = []) {
        $filter = $params['filter'] ?: [];
        $select = $params['select'] ?: [];
        $order  = $params['order'] ?: [];
        $limit  = $params['limit'] ?: 0;

        if (isset($filter)
            || isset($select)
            || isset($order)
            || isset($limit)
        ) {
            return HighloadBlock\Entity::getEntityList($hlBlockId, $filter, $select, $order, $limit);
        }

        return HighloadBlock\Entity::getEntityList($hlBlockId, $params);
    }
}