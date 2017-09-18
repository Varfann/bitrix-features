<?

/**
 * Class RecaptchaGoogle
 * Class with methods for decorators google recaptcha
 * https://www.google.com/recaptcha/admin#site/
 *
 * @author    Alexey Panov <alexeykapanov@gmail.com>
 * @version   0.1
 * @package   DoctorNet
 * @category  Validation
 * @copyright Copyright © 2016, Alexey Panov
 */

namespace DoctorNet;

use Curl\Curl, \Curl\MultiCurl;

/**
 * Class RecaptchaGoogle
 * Class with methods for
 * decorators google recaptcha
 *
 * @package DoctorNet
 */
class RecaptchaGoogle {

    const   URL_RECAPTCHA_SCRIPT_INCLUDE       = 'https://www.google.com/recaptcha/api.js';
    const   URL_RECAPTCHA_SCRIPT_SEND_RESPONSE = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * @param $key
     *
     * @return bool
     */
    public function setSecretKey($key) {
        $key = trim($key);

        if (!empty($key)) {
            \Bitrix\Main\Config\Option::set('recapcha', 'recaptcha_secret_key', $key);

            return true;
        } else {
            return false;
        }

    }

    /**
     * @return string
     * @throws \Bitrix\Main\ArgumentNullException
     */
    public function getSecretKey() {
        return \Bitrix\Main\Config\Option::get('recapcha', 'recaptcha_secret_key');
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public function setPublicKey($key) {
        $key = trim($key);

        if (!empty($key)) {
            \Bitrix\Main\Config\Option::set('recapcha', 'recaptcha_public_key', $key);

            return true;
        } else {
            return false;
        }
    }

    /**
     * @return string
     * @throws \Bitrix\Main\ArgumentNullException
     */
    public function getPublicKey() {
        return \Bitrix\Main\Config\Option::get('recapcha', 'recaptcha_public_key');
    }

    public static function getGoogleCaptchaJs() {
        $asset = \Bitrix\Main\Page\Asset::getInstance();
        $asset->addJs(self::URL_RECAPTCHA_SCRIPT_INCLUDE);
    }

    public static function getHtmlForCaptchaForm() {
        echo "<div class='g-recaptcha' data-sitekey='" . self::getPublicKey() . "'></div>";
    }

    /**
     * @param $recaptchaResponse
     *
     * @return mixed
     */
    static public function validationResponse($recaptchaResponse) {
        $curl = new Curl();
        $curl->get(self::URL_RECAPTCHA_SCRIPT_SEND_RESPONSE . '?secret=' . self::getSecretKey() . '&response='
                   . $recaptchaResponse);
        $response = $curl->response;

        return $response->success;

    }
}