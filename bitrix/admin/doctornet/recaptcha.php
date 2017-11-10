<? require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');

use \Bitrix\Main\Application;
use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

//get permission for main module
$permission = $APPLICATION->GetGroupRight('main');
if ($permission == 'D') {
    $APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));
}

$APPLICATION->SetTitle('ReCaptcha');
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');

$request = Application::getInstance()->getContext()->getRequest();

$recaptcha       = new \DoctorNet\RecaptchaGoogle;
$recaptchaSubmit = $request->getPost("recaptcha_submit");
$secretKey       = $request->getPost("secret_key");
$publicKey       = $request->getPost("public_key");

if ($recaptchaSubmit == 'y') {

    $recaptcha->setPublicKey($publicKey);
    $recaptcha->setSecretKey($secretKey);

}
?>
    <form name="recaptcha" method="post" action="<?= $request->getRequestedPage(); ?>">
        <input type="hidden" name="recaptcha_submit" value="y" />
        <div class="keys">
            <p>
                Ключ сайта ( он будет добавлен в HTML-код сайта);
            </p>
            <input type="text" size="50" name="public_key" value="<?= $recaptcha->getPublicKey(); ?>" />
            <p>
                Секретный ключ (Этот ключ нужен для связи между вашим сайтом и Google. Никому его не сообщайте.)
            </p>

            <input type="text" size="50" name="secret_key" value="<?= $recaptcha->getSecretKey(); ?>" />

            <p><input type="submit" value="Применить" /></p>

            <p>
            <h2>Информация по использованию:</h2>
            </p>
            <p>
                Вставьте этот фрагмент перед закрывающим тегом <&#047;hеаd> в HTML-коде: <br>
                <strong>
                    <span style="background-color: #ffffff;padding:5px;line-height:35px;">
                        \Doctornet\RecaptchaGoogle::getGoogleCaptchaJs();
                    </span>
                </strong>
            </p>
            ---------------------------------------------------------------
            <p>
                Вставьте код : <br>
                <strong>
                    <span style="background-color: #ffffff;padding:5px;line-height:35px;"><?= '\Doctornet\RecaptchaGoogle::getHtmlForCaptchaForm();' ?></span>
                </strong>
                <br>
                в конце объекта &#060;form> (там, где нужно разместить виджет reCAPTCHA).
            </p>
            ---------------------------------------------------------------
            <p>
                Для проверки капчи на бэкенде следует вызывать:<br>
                <strong>
                    <span style="background-color: #ffffff;padding:5px;line-height:35px;">
                        \Doctornet\RecaptchaGoogle::validationResponse($_REQUEST['g-recaptcha-response']);
                    </span>
                </strong>
                <br>
                Функция возвращает 'true' или 'false' в зависимости от результата валидации капчи.
            </p>
        </div>
    </form>
<? require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php');