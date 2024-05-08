<?php

namespace app\models\forms;

use app\models\User;
use kartik\password\StrengthValidator;
use yii\base\Model;
use Yii;

class ProfilePasswordForm extends Model
{

    public $old_password;
    public $new_password;
    public $repeat_password;

    public function rules() {
        return [
            [['old_password', 'new_password', 'repeat_password'], 'required'],
            [['old_password'], 'validatePassword'],
            [['new_password'], StrengthValidator::class, 'preset' => 'normal', 'usernameValue' => Yii::$app->user->identity ? Yii::$app->user->identity->username : ''],
            [['repeat_password'], 'compare', 'compareAttribute' => 'new_password'],
        ];
    }

    public function attributeLabels() {
        return [
            'old_password'    => Yii::t('site', 'Old password'),
            'new_password'    => Yii::t('site', 'New password'),
            'repeat_password' => Yii::t('site', 'Repeat password'),
        ];
    }

    public function validatePassword($attribute, $params) {
        $user = Yii::$app->user->identity;
        if (!$user) {
            $this->addError('old_password', Yii::t('site', 'User not found'));
            return false;
        }
        if (!$user->validatePassword($this->$attribute)) {
            $this->addError('old_password', Yii::t('site', 'Password invalid'));
            return false;
        }
        return true;
    }

    public function save() {
        if ($this->validate()) {
            $user = Yii::$app->user->identity;
            $user->setPassword($this->new_password);
            if (!$user->save(true, ['password', 'updated_at'])) {
                $this->addErrors($user->getErrors());
                return false;
            }
            return true;
        }
        return false;
    }
}