<?

/**
 * Class Tools
 *
 * @author    Roman Shershnev <readytoban@gmail.com>, Dmitry Panychev <panychev@code-craft.ru>
 * @version   1.2
 * @package   CodeCraft
 * @category  Tools
 * @copyright Copyright © 2015,2016, Roman Shershnev, Dmitry Panychev
 */

namespace DoctorNet;

class Tools {

    /**
     * @param int   $number
     * @param array $endingList
     *
     * @return string
     */
    public static function ending($number, $endingList) {
        $number = $number % 100;
        if ($number >= 11 && $number <= 19) {
            $ending = $endingList[2];
        } else {
            $i = $number % 10;
            switch ($i) {
                case (1):
                    $ending = $endingList[0];
                    break;
                case (2):
                case (3):
                case (4):
                    $ending = $endingList[1];
                    break;
                default:
                    $ending = $endingList[2];
            }
        }

        return $ending;
    }

    /**
     * Check to ajax call
     *
     * @return bool
     */
    public static function isAjaxCall() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    /**
     * @param string $price
     *
     * @return int
     */
    public static function priceToInt($price) {
        return (int)preg_replace('~\D~', '', trim($price));
    }
}