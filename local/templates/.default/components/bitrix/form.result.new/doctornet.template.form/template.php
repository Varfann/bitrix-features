<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use DoctorNet\Decorator\Form;

$arResult['FORM_ATTRIBUTES'][] = 'class="f-form js-form"';
?>

<div class="col-group">
    <div class="request-for-consultation-inner clearfix">
        <div class="js-form-container col-mb-3 col-mb-offset-1">
            <?= Form::open($arResult['FORM_ACTION'], $arResult['FORM_ATTRIBUTES']); ?>
            <?= bitrix_sessid_post(); ?>
            <?= Form::hidden('WEB_FORM_ID', $arParams['WEB_FORM_ID']); ?>
            <? foreach ($arResult['QUESTIONS'] as $fieldCode => $question) {
                if ($question['ATTRIBUTES']['TYPE'] != 'hidden') {
                    echo Form::label($question['ATTRIBUTES']['NAME'], $question['CAPTION']) . ' ';
                } ?>
                <div class="f-line js-scroll-fade-in">
                    <div class="f-field">
                        <?= $question['FIELD']; ?>
                    </div>
                </div>
            <? } ?>

            <div class="f-line js-scroll-fade-in">
                <div class="f-field">
                    <?= Form::submit('web_form_submit', $arResult['arForm']['BUTTON']) ?>
                </div>
            </div>

            <?= Form::hidden('web_form_apply', 'Y'); ?>
            <?= Form::close(); ?>
        </div>
    </div>
</div>

