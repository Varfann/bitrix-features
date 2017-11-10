<?

namespace DoctorNet\Helper;

class UserType {

    /**
     * Simple get user enumeration field list
     *
     * @param int   $userFieldId
     * @param array $sort
     * @param array $filter
     *
     * @return array
     */
    public static function getEnumerationFieldValueList($userFieldId, array $sort = [], array $filter = []) {
        $result = [];

        $valueCollection = \CUserFieldEnum::GetList($sort, array_merge(['USER_FIELD_ID' => $userFieldId], $filter));

        while ($value = $valueCollection->Fetch()) {
            $result[$value['XML_ID']] = $value;
        }

        return $result;
    }
}