<?php

namespace app\components;

use Yii;

class PhpMessageSource extends \yii\i18n\PhpMessageSource
{

    public function loadMessages($category, $language) {
        $messages = parent::loadMessages($category, $language);
        if ($category = 'site') {
            if ($language == 'en') {
                $messages['.Language.'] = 'English';
            } else {
                $messages['.Language.'] = Yii::t('site', '.Language.');
            }
        }
        return $messages;
    }

}