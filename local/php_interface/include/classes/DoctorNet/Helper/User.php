<?

namespace DoctorNet\Helper;

use Bitrix\Main\UserTable;

/**
 * Class User - User helper, D7
 *
 * @package DoctorNet\Helper
 */
class User
{
    use D7Table;

    /**
     * @param       $id
     * @param array $select
     *
     * @return array
     */
    public static function getUserById($id, $select = []) {
        return array_shift(self::getUserList(['ID' => $id], $select));
    }

    /**
     * @param       $filter
     * @param array $select
     * @param array $sort
     * @param int   $limit
     * @param array $group
     * @param array $offset
     * @param array $runtime
     *
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     */
    public static function getUserList($filter, $select = [], $sort = [], $limit = 0, $group = [], $offset = [], $runtime = []) {
        $parameters = self::prepareParameters($filter, $select, $sort, $limit, $group, $offset, $runtime);

        return UserTable::getList($parameters)->fetchAll();
    }
}