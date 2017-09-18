<?

/**
 * Class Tools/File
 *
 * @author    Dmitry Panychev <panychev@code-craft.ru>
 * @version   1.0
 * @package   CodeCraft
 * @category  Tools
 * @copyright Copyright © 2016, Dmitry Panychev
 */

namespace DoctorNet\Tools;

class File {
    /**
     * Fast image resize
     *
     * @param int $fileId
     * @param int $width
     * @param int $height
     * @param int $type
     *
     * @return string
     */
    public static function resizeImage($fileId, $width, $height, $type = BX_RESIZE_IMAGE_PROPORTIONAL_ALT) {
        $file = \CFile::ResizeImageGet($fileId, ['width'  => $width,
                                                 'height' => $height], $type, false, false, false, 98);

        return $file['src'];
    }
}