<?

namespace DoctorNet\HighloadBlock\Helper\Exception;

use Bitrix\Main\SystemException;

class UnknownException extends SystemException
{

    public function __construct($message = "", $code = 0, $file = "", $line = 0, \Exception $previous = null) {
        parent::__construct($message, $code, $file, $line, $previous);
    }

}