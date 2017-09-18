<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use DoctorNet\Decorator\Form;

$arResult['FORM_ATTRIBUTES'] = ['method'  => 'POST',
                                'name'    => $arResult['WEB_FORM_NAME'],
                                'enctype' => 'multipart/form-data'];

$arResult['FORM_ACTION']     = $_SERVER['REQUEST_URI'];

foreach ($arResult['QUESTIONS'] as $name => &$question) {
    switch ($question['STRUCTURE'][0]['FIELD_TYPE']) {
        case 'dropdown':
        case 'radio':
            $questionId = $name;
            break;
        case 'checkbox':
        case 'multiselect':
            $questionId = $name . '[]';
            break;
        default:
            $questionId = $question['STRUCTURE'][0]['ID'];
            break;
    }

    /*Condition for Doctornet's phone field with front-end validation*/
    if (strpos($question['STRUCTURE'][0]['FIELD_PARAM'], 'js-phone-input')) {
        $fieldType = 'phone';
    } else {
        $fieldType = $question['STRUCTURE'][0]['FIELD_TYPE'];
    }

    $question['ATTRIBUTES']['TYPE']       = $fieldType;
    $question['ATTRIBUTES']['NAME']       = 'form_' . $question['STRUCTURE'][0]['FIELD_TYPE'] . '_' . $questionId;
    $question['ATTRIBUTES']['REQUIRED']   = $question['REQUIRED'] == 'Y' ? 'required' : '';
    $question['ATTRIBUTES']['ADDITIONAL'] = [$question['STRUCTURE'][0]['FIELD_PARAM'],
                                             $question['ATTRIBUTES']['REQUIRED']];

    switch ($question['ATTRIBUTES']['TYPE']) {
        case 'checkbox': {
            $attributes        = $question['ATTRIBUTES']['ADDITIONAL'];
            $attributes['id']  = $question['STRUCTURE'][0]['ID'];
            $question['FIELD'] = Form::checkbox($question['ATTRIBUTES']['NAME'], $question['STRUCTURE'][0]['ID'], false, $attributes);
        }
            break;
        case 'radio': {
            $question['FIELD'] = '';
            foreach ($question['STRUCTURE'] as $key => $radio) {
                $attributes       = [0 => $radio['FIELD_PARAM']];
                $attributes['id'] = $radio['ID'];
                $question['FIELD'] .= Form::radio($question['ATTRIBUTES']['NAME'], $radio['ID'], false, $attributes);
                $question['FIELD'] .= Form::label($radio['ID'], $key + 1);
                if ($key < count($question['STRUCTURE']) - 1) {
                    $question['FIELD'] .= '<br>';
                }
            }

        }
            break;
        case 'multiselect': {
            $options = [];
            foreach ($question['STRUCTURE'] as $option) {
                $options[$option['ID']] = $option['MESSAGE'];
            }
            $attributes        = $question['ATTRIBUTES']['ADDITIONAL'];
            $attributes[]      = 'multiple';
            $attributes['id']  = $question['ATTRIBUTES']['NAME'];
            $question['FIELD'] = Form::select($question['ATTRIBUTES']['NAME'], $options, null, $attributes);
        }
            break;
        case 'dropdown': {
            $options = [];
            foreach ($question['STRUCTURE'] as $option) {
                $options[$option['ID']] = $option['MESSAGE'];
            }
            $attributes        = $question['ATTRIBUTES']['ADDITIONAL'];
            $attributes['id']  = $question['ATTRIBUTES']['NAME'];
            $question['FIELD'] = Form::select($question['ATTRIBUTES']['NAME'], $options, null, $attributes);
        }
            break;
        case 'date': {
            $question['FIELD'] = CForm::GetDateField($question['STRUCTURE'][0]['ID'], $question['ATTRIBUTES']['NAME'], $_REQUEST[$question['ATTRIBUTES']['NAME']], '', $question['STRUCTURE'][0]['FIELD_PARAM']
                                                                                                                                                                       . $question['ATTRIBUTES']['REQUIRED']);
        }
            break;
        case 'hidden':
            $question['FIELD'] = Form::hidden($question['ATTRIBUTES']['NAME'], $_REQUEST[$question['ATTRIBUTES']['NAME']], $question['ATTRIBUTES']['ADDITIONAL']);
            break;
        case 'password':
            $question['FIELD'] = Form::password($question['ATTRIBUTES']['NAME'], '', $question['ATTRIBUTES']['ADDITIONAL']);
            break;
        case 'file':
            $question['FIELD'] = Form::file($question['ATTRIBUTES']['NAME'], $question['ATTRIBUTES']['ADDITIONAL']);
            break;
        case 'textarea':
            $question['FIELD'] = Form::textarea($question['ATTRIBUTES']['NAME'], $_REQUEST[$question['ATTRIBUTES']['NAME']], $question['ATTRIBUTES']['ADDITIONAL']);
            break;
        case 'image':
            $question['FIELD'] = Form::image($question['ATTRIBUTES']['NAME'], $_REQUEST[$question['ATTRIBUTES']['NAME']], $question['ATTRIBUTES']['ADDITIONAL']);
            break;
        default:
            $question['FIELD'] = Form::input($question['ATTRIBUTES']['NAME'], $_REQUEST[$question['ATTRIBUTES']['NAME']], $question['ATTRIBUTES']['ADDITIONAL']);
    }
}
