<?php


namespace app\components;

use Yii;

class TextProcessor
{

    /**
     * @param string $text
     * @return string
     */
    public static function process($text) {
        if (!empty(Yii::$app->params['TextProcessors'])) {
            foreach (Yii::$app->params['TextProcessors'] as $textProcessor) {
                $text = call_user_func([$textProcessor, 'process'], $text);
            }
        }
        return $text;
    }

}