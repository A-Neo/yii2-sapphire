<?php

namespace app\models\forms;

use app\models\User;
use yii\base\Model;
use Yii;

class ProfileSettingForm extends Model
{

    public $promote1;
    public $promote2;

    public function rules() {
        return [
            [['full_name', 'country', 'birth_date',], 'required'],
        ];
    }

    public function __construct($config = []) {
        $user = Yii::$app->user->identity;
        $this->setAttributes($user->getAttributes());
        parent::__construct($config);
    }

    public function attributeLabels() {
        return [
            'full_name'  => Yii::t('site', 'Full name'),
            'country'    => Yii::t('site', 'Country'),
            'birth_date' => Yii::t('site', 'Birth date'),
        ];
    }

    public function save() {
        if($this->validate()){
            $user = Yii::$app->user->identity;
            $user->setAttributes($this->getAttributes());
            if(!$user->save(true, ['full_name', 'country', 'birth_date', 'updated_at'])){
                $this->addErrors($user->getErrors());
                return false;
            }
            return true;
        }
        return false;
    }

}