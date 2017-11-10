<?

namespace DoctorNet\Helper;


class JsonHelper
{
    /**
     * @param string $from
     * @param bool   $assoc
     *
     * @return mixed
     */
    public static function decode($from, $assoc = true) {
        return json_decode($from, $assoc);
    }

    /**
     * @param string $message
     *
     * @die
     */
    public static function printMessage($message) {
        self::printJson(array('error'   => false,
                              'message' => $message));
    }

    /**
     * @param mixed $to
     *
     * @die
     */
    public static function printJson($to) {
        self::printHeader();
        echo self::encode($to);
        die;
    }

    public static function printHeader() {
        header('Content-Type: application/json; charset=utf-8');
    }

    /**
     * @param mixed $to
     *
     * @return string
     */
    public static function encode($to) {
        return json_encode($to);
    }

    /**
     * @param string $error
     *
     * @die
     */
    public static function printError($error) {
        self::printJson(array('error'   => true,
                              'message' => $error));
    }
}