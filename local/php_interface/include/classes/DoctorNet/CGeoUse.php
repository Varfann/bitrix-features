<?

namespace DoctorNet;

class CGeoUse
{
    public static function isBot(&$botname = '') {
        return false;

        $bots = ['rambler',
                 'googlebot',
                 'aport',
                 'yahoo',
                 'msnbot',
                 'turtle',
                 'mail.ru',
                 'omsktele',
                 'yetibot',
                 'picsearch',
                 'sape.bot',
                 'sape_context',
                 'gigabot',
                 'snapbot',
                 'alexa.com',
                 'megadownload.net',
                 'askpeter.info',
                 'igde.ru',
                 'ask.com',
                 'qwartabot',
                 'yanga.co.uk',
                 'scoutjet',
                 'similarpages',
                 'oozbot',
                 'shrinktheweb.com',
                 'aboutusbot',
                 'followsite.com',
                 'dataparksearch',
                 'google-sitemaps',
                 'appEngine-google',
                 'feedfetcher-google',
                 'liveinternet.ru',
                 'xml-sitemaps.com',
                 'agama',
                 'metadatalabs.com',
                 'h1.hrn.ru',
                 'googlealert.com',
                 'seo-rus.com',
                 'yaDirectBot',
                 'yandeG',
                 'yandex',
                 'yandexSomething',
                 'Copyscape.com',
                 'AdsBot-Google',
                 'domaintools.com',
                 'Nigma.ru',
                 'bing.com',
                 'dotnetdotcom'];

        foreach ($bots as $bot) {
            if (stripos($_SERVER['HTTP_USER_AGENT'], $bot) !== false) {
                $botname = $bot;

                return true;
            }
        }

        return false;
    }

    public static function getCurCityCode() {
        $city = self::getCurCityInfo();

        return $city['CODE'];
    }

    public static function getCurCityInfo() {
        if (\CModule::IncludeModule('iblock')) {
            $city = \CIBlockElement::GetList([], ['ID' => self::getCurCity()], false, false, ['NAME',
                                                                                              'CODE',
                                                                                              'PROPERTY_CITY_FEEDBACK_EMAIL'])->Fetch();
        } else {
            $city = $_SESSION['GEO']['ELEM_NAME_LOC'];
        }

        return $city;
    }

    public static function getCurCity() {

        $arCurCity['ID'] = '';

        if (empty($_COOKIE['CITY_ID'])) {
            //$ar = CGeoIP::GetGeoData();
            //$arCurCity['ID'] = $ar['city'];
            $ar              = CGeoIP::GetLocationData();
            $arCurCity['ID'] = intval($ar['ELEM_ID_LOC']);
        } else {
            $arCurCity['ID'] = $_COOKIE['CITY_ID'];
        }

        $arCitiesListId = self::getCitiesListId();

        if (!in_array($arCurCity['ID'], $arCitiesListId)) {
            $arCurCity['ID'] = CITY_DEFAULT_ID;
        }

        return $arCurCity['ID'];
    }

    public static function getCitiesListId() {

        $arCitiesListId = array();
        $arCitiesList   = self::getCitiesList();
        foreach ($arCitiesList as $city) {
            $arCitiesListId[] = $city['ID'];
        }

        return $arCitiesListId;
    }

    public static function getCitiesList() {

        $arCitiesList = array();
        if (\CModule::IncludeModule('iblock')) {
            $res = \CIBlockElement::GetList(Array('NAME' => 'ASC'), Array('IBLOCK_ID'   => IntVal(IBLOCK_CITIES),
                                                                          'ACTIVE_DATE' => 'Y',
                                                                          'ACTIVE'      => 'Y',), false, false, Array('ID',
                                                                                                                      'NAME',));
            while ($ob = $res->GetNextElement()) {
                $arFields       = $ob->GetFields();
                $arCitiesList[] = $arFields;
            }
        }

        return $arCitiesList;
    }

    public static function getCurCityName() {
        $city = self::getCurCityInfo();

        return $city['NAME'];
    }

    public static function isCitySet() {
        if (!PageRouter::getInstance()->isIndex() || self::isBot()) {
            //self::setCity();
        }

        return isset($_COOKIE['CITY_ID']);
    }

    /**
     * @param int $cityId
     */
    public static function setCity($cityId = 0) {
        if (!$cityId) {
            $cityId = self::getCurCity();
        }

        setcookie('CITY_ID', $cityId, time() + 60 * 60 * 24 * 30, '/');
        $_COOKIE['CITY_ID'] = $cityId;
    }

    public static function redirectFromCityChoose() {
        if ($_COOKIE['CITY_ID']) {
            LocalRedirect('/');
        }
    }

    public static function redirectToCityChoose($pageToRedirect) {
        if (!PageRouter::getInstance()->isIndex() || self::isBot()) {
            self::setCity();
        } elseif (!(isset($_COOKIE['CITY_ID']) || $_SERVER['REQUEST_URI'] == $pageToRedirect)) {
            LocalRedirect($pageToRedirect);
        }
    }

    public static function getCurCityEmail() {
        $city = self::getCurCityInfo();

        return $city['PROPERTY_CITY_FEEDBACK_EMAIL_VALUE'];
    }
}