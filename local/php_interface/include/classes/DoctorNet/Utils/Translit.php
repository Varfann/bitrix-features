<?

/**
 * Class Translit
 * Simple transliterate class
 *
 *
 * @author    Dmitry Panychev <panychev@code-craft.ru>
 * @version   1.0
 * @package   CodeCraft
 * @category  Utils
 * @copyright Copyright © 2016, Dmitry Panychev
 */

namespace DoctorNet\Utils;

class Translit {
    private static $transliterateList = [
        'ru' => [
            'from' => 'а,б,в,г,д,е,ё,ж,з,и,й,к,л,м,н,о,п,р,с,т,у,ф,х,ц,ч,ш,щ,ъ,ы,ь,э,ю,я,А,Б,В,Г,Д,Е,Ё,Ж,З,И,Й,К,Л,М,Н,О,П,Р,С,Т,У,Ф,Х,Ц,Ч,Ш,Щ,Ъ,Ы,Ь,Э,Ю,Я',
            'to'   => 'a,b,v,g,d,e,ye,zh,z,i,j,k,l,m,n,o,p,r,s,t,u,f,kh,ts,ch,sh,shch,,y,,e,yu,ya,A,B,V,G,D,E,YE,ZH,Z,I,Y,K,L,M,N,O,P,R,S,T,U,F,KH,TS,CH,SH,SHCH,,Y,,E,YU,YA',
        ],
        'en' => [
            'from' => 'a,b,v,g,d,e,ye,zh,z,i,j,k,l,m,n,o,p,r,s,t,u,f,kh,ts,ch,sh,shch,,y,,e,yu,ya,A,B,V,G,D,E,YE,ZH,Z,I,Y,K,L,M,N,O,P,R,S,T,U,F,KH,TS,CH,SH,SHCH,,Y,,E,YU,YA',
            'to'   => 'а,б,в,г,д,е,ё,ж,з,и,й,к,л,м,н,о,п,р,с,т,у,ф,х,ц,ч,ш,щ,ъ,ы,ь,э,ю,я,А,Б,В,Г,Д,Е,Ё,Ж,З,И,Й,К,Л,М,Н,О,П,Р,С,Т,У,Ф,Х,Ц,Ч,Ш,Щ,Ъ,Ы,Ь,Э,Ю,Я',
        ],
    ];

    /**
     * Transliterate
     *
     * @param string $string
     * @param string $lang
     *
     * @todo From lang To lang
     *
     * @return mixed
     */
    public static function transliterate($string, $lang = 'ru') {
        $result = '';

        $transliterationMatrix = array_combine(explode(',', self::$transliterateList[$lang]['from']),
            explode(',', self::$transliterateList[$lang]['to']));

        $maxLength = 0;
        foreach ($transliterationMatrix as $k => $v) {
            $maxLength = strlen($k) > $maxLength ? strlen($k) : $maxLength;
        }

        for ($i = 0; $i < strlen($string);) {
            $step  = 1;
            $found = false;

            for ($l = $maxLength; $l > 0; $l--) {
                $char = substr($string, $i, $l);

                if ($transliterationMatrix[$char]) {
                    $step = $l;
                    $result .= $transliterationMatrix[$char];
                    $found = true;

                    break;
                }
            }

            if (!$found) {
                $result .= $char;
            }

            $i += $step;
        }

        return $result;
    }
}