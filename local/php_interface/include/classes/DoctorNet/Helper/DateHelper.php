<?
/**
 * Created by PhpStorm.
 * User: Varfann
 * Date: 27.11.2016
 * Time: 18:20
 */

namespace DoctorNet\Helper;

use DoctorNet\Context;

class DateHelper
{

    public function __construct() {
    }

    /**
     * @return bool|string
     */
    private function getFullCurrentDate() {
        return date('d.m.Y');
    }

    /**
     * @return bool|string
     */
    private function getCurrentYear() {
        return date('Y');
    }

    /**
     * @return bool|string
     */
    private function getCurrentMonth() {
        return date('m');
    }

    /**
     * @return bool|string
     */
    private function getCurrentMonthDaysCount() {
        return date('t');
    }

    private function getCurrentWeekDay() {
        return date('N');
    }

    /**
     * Returns filter for day range
     *
     * @param $periodBefore
     * @param $periodAfter
     *
     * @return array
     */
    private function getDayFilter($periodBefore, $periodAfter) {
        $filter                = [];
        $filter[$periodBefore] = $this->getFullCurrentDate() . ' 23:59:59';
        $filter[$periodAfter]  = $this->getFullCurrentDate() . ' 00:00:00';

        return $filter;
    }

    /**
     * Returns filter for week range
     *
     * @param $periodBefore
     * @param $periodAfter
     *
     * @return array
     */
    private function getWeekFilter($periodBefore, $periodAfter) {
        $filter        = [];
        $curTimestamp  = MakeTimeStamp($this->getFullCurrentDate());
        $curWeekDay    = $this->getCurrentWeekDay();
        $beginWeekDate = ConvertTimeStamp($curTimestamp - 24 * 60 * 60 * ($curWeekDay - 1));
        $endWeekDate   = ConvertTimeStamp($curTimestamp + 24 * 60 * 60 * (7 - $curWeekDay));

        $filter[$periodBefore] = $endWeekDate . ' 23:59:59';
        $filter[$periodAfter]  = $beginWeekDate . ' 00:00:00';

        return $filter;
    }

    /**
     * Returns filter for month range
     *
     * @param $periodBefore
     * @param $periodAfter
     *
     * @return array
     */
    private function getMonthFilter($periodBefore, $periodAfter) {
        $filter                = [];
        $filter[$periodBefore] = $this->getCurrentMonthDaysCount() . '.' . $this->getCurrentMonth() . '.'
                                 . $this->getCurrentYear() . ' 23:59:59';
        $filter[$periodAfter]  = '01.' . $this->getCurrentMonth() . '.' . $this->getCurrentYear() . ' 00:00:00';

        return $filter;
    }

    /**
     * Returns filter for year range
     *
     * @param $periodBefore
     * @param $periodAfter
     *
     * @return array
     */
    private function getYearFilter($periodBefore, $periodAfter) {
        $filter                = [];
        $filter[$periodBefore] = '31.12.' . $this->getCurrentYear() . ' 23:59:59';
        $filter[$periodAfter]  = '01.01.' . $this->getCurrentYear() . ' 00:00:00';

        return $filter;
    }

    /**
     * @param $dateFieldCode
     *
     * @return array
     */
    public function getPeriodFilter($dateFieldCode, $useTimestampX = false) {
        if ($useTimestampX) {
            $periodBefore = $dateFieldCode . '_2';
            $periodAfter  = $dateFieldCode . '_1';
        } else {
            $periodBefore = '<=' . $dateFieldCode;
            $periodAfter  = '>=' . $dateFieldCode;
        }
        $request = Context::getInstance()->getRequest();
        switch ($request['period']) {
            case 'week' :
                $filter = $this->getWeekFilter($periodBefore, $periodAfter);
                break;
            case 'month' :
                $filter = $this->getMonthFilter($periodBefore, $periodAfter);
                break;
            case 'year' :
                $filter = $this->getYearFilter($periodBefore, $periodAfter);
                break;
            default :
                $filter = $this->getDayFilter($periodBefore, $periodAfter);
        }

        return $filter;
    }
}