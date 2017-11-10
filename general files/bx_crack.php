<?
// продлим демо до до 01.01.2038
$dbStr = 'FVgQeWYUBgQtCUVcDxcGCgsTAQ==';
$defineStr = '<'.'?define("TEMPORARY_CACHE", "ARtudgYHb2MMdQgebRtmG24A");?'.'>';

// удаляет рекурсивно папку со всем содержимым
function removeDirectory($dir) {
	if ($objs = glob($dir."/*")) {
		foreach($objs as $obj) {
			is_dir($obj) ? removeDirectory($obj) : unlink($obj);
		}
	}
rmdir($dir);
}

require $_SERVER['DOCUMENT_ROOT'].'/bitrix/php_interface/dbconn.php';
/*
 * @var string $DBType
 * @var string $DBHost
 * @var string $DBName
 * @var string $DBLogin
 * @var string $DBPassword
*/

$dsn = $DBType.':host='.$DBHost.';dbname='.$DBName.';charset=utf8';
$opt = array(
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
);
$pdo = new PDO($dsn, $DBLogin, $DBPassword, $opt);
$pdo->exec("set names utf8");
$pdo->exec("UPDATE `b_option` SET `VALUE` = '".$dbStr."' WHERE `NAME`='admin_passwordh'");

file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/admin/define.php', $defineStr);

removeDirectory($_SERVER['DOCUMENT_ROOT'].'/bitrix/managed_cache');
mkdir($_SERVER['DOCUMENT_ROOT'].'/bitrix/managed_cache', BX_DIR_PERMISSIONS);

die('OK');
