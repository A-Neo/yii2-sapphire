<?php

namespace app\models\forms;

use app\models\User;
use kartik\password\StrengthValidator;
use yii\base\Model;
use Yii;

class ProfileFinPasswordForm extends Model
{

    public $old_password;
    public $new_password;
    public $repeat_password;
    public $code;

    public $user;

    public function rules() {
        return [
            [['new_password', 'repeat_password'], 'required'],
            [['old_password', 'code'], 'required', 'when' => function ($model) {
                return !empty($this->user->fin_password);
            }],
            [['old_password'], 'validatePassword'],
            [['code'], 'validateCode'],
            [['new_password'], 'string', 'min' => 4],
            [['new_password'], 'match', 'pattern' => '/^[0-9]+$/', 'message' => Yii::t('site', 'Only numbers allowed')],
            [['repeat_password'], 'compare', 'compareAttribute' => 'new_password'],
        ];
    }

    public function attributeLabels() {
        return [
            'old_password'    => Yii::t('site', 'Old password'),
            'new_password'    => Yii::t('site', 'New password'),
            'repeat_password' => Yii::t('site', 'Repeat password'),
            'code'            => Yii::t('site', 'Confirmation code'),
        ];
    }

    public function init() {
        $this->user = Yii::$app->user->identity;
        if(empty($this->user->password_reset_token) || !is_numeric($this->user->password_reset_token)){
            $this->generateNewCode();
        }
        parent::init();
    }

    public function validateCode($attribute, $params) {
        if($this->user->password_reset_token != $this->$attribute){
            $this->addError($attribute, Yii::t('site', 'Confirmation code invalid'));
            return false;
        }
        return true;
    }

    public function validatePassword($attribute, $params) {
        if(!$this->user){
            $this->addError($attribute, Yii::t('site', 'User not found'));
            return false;
        }
        if(!$this->user->validatePassword($this->$attribute, 'fin_password')){
            $this->addError($attribute, Yii::t('site', 'Password invalid'));
            return false;
        }
        return true;
    }

    public function save() {
        if($this->validate()){
            $user = Yii::$app->user->identity;
            $user->setPassword($this->new_password, 'fin_password');
            $user->password_reset_token = null;
            if(!$user->save(true, ['fin_password', 'password_reset_token', 'updated_at'])){
                $this->addErrors($user->getErrors());
                return false;
            }
            return true;
        }
        return false;
    }

    public function generateNewCode() {
        $this->user->password_reset_token = mt_rand(100000, 999999);
        $this->user->save(false, ['password_reset_token']);
        try{
            return Yii::$app
                ->mailer
                ->compose(
                    ['html' => 'finPasswordResetCode-html', 'text' => 'finPasswordResetCode-text'],
                    ['user' => $this->user]
                )
                ->setFrom([Yii::$app->mailer->getTransport()->getUsername() => Yii::$app->params['siteName'] . ' robot'])
                ->setTo($this->user->email)
                ->setSubject(Yii::t('site', 'Financial password reset code for') . ' ' . Yii::$app->params['siteName'])
                ->send();
        }catch(\Exception $e){
            if(Yii::$app->has('session')){
                Yii::$app->session->addFlash('error', $e->getMessage());
            }
            return false;
        }
    }
}