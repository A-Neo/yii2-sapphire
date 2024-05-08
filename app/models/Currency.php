<?php

namespace app\models;

use app\models\behaviors\TranslateBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii2tech\ar\position\PositionBehavior;

/**
 * This is the model class for table "currency".
 *
 * @property int    $id
 * @property string $name
 * @property string $number
 */
class Currency extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'currency';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['name'], 'required'],
            [['name'], 'unique'],
            [['number'], 'string']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id'          => Yii::t('site', 'ID'),
            'name'        => Yii::t('site', 'Title'),
            'number'      => Yii::t('site', 'Text')
        ];
    }

    // public function behaviors() {
    // }

}
