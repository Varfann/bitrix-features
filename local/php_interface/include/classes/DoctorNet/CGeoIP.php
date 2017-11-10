<?
namespace DoctorNet;

class
    // CGeoIP::GetLocationContact
CGeoIP
{

    public static $ip;
    public static $geoIp;
    public static $geoIb;
    public static $geoRes;

    function Init() {
        self::$ip = $_SERVER['REMOTE_ADDR'];
        // self::$ip = Bitrix\Main\Application::getInstance()->getContext()->getServer()->get('REMOTE_ADDR');
        self::$ip = $_SERVER['HTTP_X_REAL_IP'];


    }

    function ParseXML($text) {

        if (strlen($text) > 0) {
            require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/classes/general/xml.php");
            $objXML = new \CDataXML();
            $res    = $objXML->LoadString($text);
            if ($res !== false) {
                $arRes = $objXML->GetArray();
            }
        }

        $arRes = current($arRes);
        $arRes = $arRes["#"];
        $arRes = current($arRes);

        $ar = Array();

        foreach ($arRes as $key => $arVal) {
            foreach ($arVal["#"] as $title => $Tval) {
                $ar[$key][$title] = $Tval["0"]["#"];
            }
        }

        return ($ar[0]);

    }

    public static function GetGeoData($ip = "") {
        if (empty($ip)) {

            self::Init();

            $ip = self::$ip;
        }


        if (!self::$geoIp) {


            $obCACHE       = new \CPHPCache;
            $iCACHE_TIME   = 3600;
            $strCACHE_ID   = md5($ip);
            $strCACHE_PATH = "/doctornet/getgeodata";

            if (isset($_REQUEST['clear_cache']) && $_REQUEST['clear_cache'] == "Y") {
                $obCACHE->Clean($strCACHE_ID, $strCACHE_PATH);
            }

            if ($iCACHE_TIME > 0 && $obCACHE->InitCache($iCACHE_TIME, $strCACHE_ID, $strCACHE_PATH)) {
                $arrRESULT = $obCACHE->GetVars();
                $arData    = $arrRESULT['arData'];

                //                         echo "<script>console.log('cahe');</script>";
                //                         echo "<script>console.log(".json_encode($arData).");</script>";

            }


            if (!is_array($arData) || !count($arData)) {
                $obCACHE->Clean($strCACHE_ID, $strCACHE_PATH);

                //                    echo "<script>console.log('no cahe');</script>";
                // $text = file_get_contents("http://ipgeobase.ru:7020/geo?ip=" . $ip);

                // $text = iconv("windows-1251", SITE_CHARSET, $text);
                // $arData = self::ParseXML($text);


                if ($ch = curl_init()) {
                    $url     = "http://ip-api.com/json/" . $ip;
                    $headers = array("Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
                                     "Accept-Encoding: 	gzip, deflate",
                                     "Accept-Language:	force;ru",
                                     "Host:	ip-api.com",
                                     "Referer: http://ip-api.com/",
                                     "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:45.0) Gecko/20100101 Firefox/45.0",);
                    $ch      = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 1);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                    $data = curl_exec($ch);


                    $arData = json_decode($data, true);

                    if (curl_errno($ch)) {


                    } else {

                    }

                    curl_close($ch);
                }


                //                    echo "<script>console.log(".json_encode($arData).");</script>";

                $obCACHE->StartDataCache($iCACHE_TIME, $strCACHE_ID, $strCACHE_PATH);
                $obCACHE->EndDataCache(array("arData" => $arData));
            }


            // echo "<script>console.log(".json_encode($arData).");</script>";

            if ($arData['city'] == '') {

                $arData = Array("inetnum"  => "217.25.224.0 - 217.25.239.255",
                                "country"  => "RU",
                                "city"     => "Воронеж",
                                "region"   => "Воронеж",
                                "district" => "Воронежская область",
                                "lat"      => 51.661535,
                                "lng"      => 39.200287);
            }

            self::$geoIp = $arData;
        }

        return self::$geoIp;
    }

    public static function SetLocationData($id = 0) {

        if (!self::$geoIp['city']) {
            self::GetGeoData();
        }

        $arFiltr = array('IBLOCK_ID' => IBLOCK_CITIES,);

        if ($id > 0) {
            $arFiltr['ID'] = $id;
        } else {
            $arFiltr['NAME'] = self::$geoIp['city'];
        }

        $obCACHE     = new \CPHPCache;
        $iCACHE_TIME = 3600 * 12;

        $strCACHE_ID   = md5(serialize($arFiltr));
        $strCACHE_PATH = "/doctornet/getgeodataIB";

        if (isset($_REQUEST['clear_cache']) && $_REQUEST['clear_cache'] == "Y") {
            $obCACHE->Clean($strCACHE_ID, $strCACHE_PATH);
        }

        if ($iCACHE_TIME > 0 && $obCACHE->InitCache($iCACHE_TIME, $strCACHE_ID, $strCACHE_PATH)) {
            $arrRESULT    = $obCACHE->GetVars();
            $arIblockData = $arrRESULT['arIblockData'];
        }

        if (!is_array($arIblockData) || !count($arIblockData)) {

            if (\CModule::IncludeModule("iblock")) {
                $res = \CIBlockElement::GetList(Array('SORT' => 'ASC'), $arFiltr, false, false, array('ID',
                                                                                                      'NAME',
                                                                                                      'CODE',
                                                                                                      'PROPERTY_RELATIVE_LOCATION'));

                if ($ob = $res->Fetch()) {
                    $arIblockData = $ob;
                }
            }
            $obCACHE->StartDataCache($iCACHE_TIME, $strCACHE_ID, $strCACHE_PATH);
            $obCACHE->EndDataCache(array("arIblockData" => $arIblockData));
        }

        self::$geoIb = $arIblockData;


        $arResult = array('ELEM_ID_LOC'   => self::$geoIb['ID'],
                          'ELEM_NAME_LOC' => self::$geoIb['NAME'],
                          'ID_LOC'        => self::$geoIb['PROPERTY_RELATIVE_LOCATION_VALUE'],);

        if ($id > 0 && $arResult['ELEM_ID_LOC'] && $arResult['ELEM_NAME_LOC']) {
            $arResult['IS_SET'] = 'Y';
        }
        if ($arResult['ELEM_ID_LOC'] && $arResult['ELEM_NAME_LOC']) {
            $_SESSION['GEO'] = $arResult;
        } else {
            unset($_SESSION['GEO']);
        }


    }

    public static function GetLocationDataHandler() {

        $city_id = \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->getQuery("city_id");
        if (intval($city_id) > 0) {
            \DoctorNet\CGeoIP::SetLocationData($city_id);
        }

        $r = self::GetLocationData();

        if ($city_id) {
            global $APPLICATION;
            $page = $APPLICATION->GetCurPageParam('', array("city_id"));
            LocalRedirect($page);
        }
    }

    public static function GetLocationData() {

        if (!self::$geoRes) {
            self::$geoRes = $_SESSION['GEO'];
        }
        if (!self::$geoRes) {
            self::SetLocationData();
        }
        if (!self::$geoRes) {
            self::$geoRes = $_SESSION['GEO'];
        }

        return self::$geoRes;
    }

    public static function GetLocationContact() {

        $r = self::GetLocationData();

        $arFiltr = array('IBLOCK_ID'                  => IBLOCK_CONTACT,
                         'PROPERTY_RELATIVE_LOCATION' => $r['ELEM_ID_LOC'],);


        $obCACHE     = new CPHPCache;
        $iCACHE_TIME = 3600 * 12;

        $strCACHE_ID   = md5(serialize($arFiltr));
        $strCACHE_PATH = "/doctornet/getgeodataCONT";

        if (isset($_REQUEST['clear_cache']) && $_REQUEST['clear_cache'] == "Y") {
            $obCACHE->Clean($strCACHE_ID, $strCACHE_PATH);
        }

        if ($iCACHE_TIME > 0 && $obCACHE->InitCache($iCACHE_TIME, $strCACHE_ID, $strCACHE_PATH)) {
            $arrRESULT    = $obCACHE->GetVars();
            $arIblockData = $arrRESULT['arIblockData'];
        }

        if (!is_array($arIblockData) || !count($arIblockData)) {

            if (\CModule::IncludeModule("iblock")) {
                $res = \CIBlockElement::GetList(Array('SORT' => 'ASC'), $arFiltr, false, false, array('ID',
                                                                                                      'NAME',
                                                                                                      'CODE',
                                                                                                      'PROPERTY_PHONES'));

                if ($ob = $res->Fetch()) {
                    $arIblockData = $ob;
                }
            }
            $obCACHE->StartDataCache($iCACHE_TIME, $strCACHE_ID, $strCACHE_PATH);
            $obCACHE->EndDataCache(array("arIblockData" => $arIblockData));
        }

        return $arIblockData;

    }


}

?>