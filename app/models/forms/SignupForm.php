<?php

namespace app\models\forms;

use kartik\password\StrengthValidator;
use Yii;
use yii\base\Model;
use app\models\User;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $model;
    public $full_name;
    public $username;
    public $email;
    public $country;
    public $phone;
    public $referrer;
    public $password;
    public $repeat_password;
    public $referrer_id;

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['full_name', 'username', 'email', 'password', 'country', 'phone', 'referrer', 'repeat_password'], 'required'],
            ['full_name', 'trim'],
            ['username', 'trim'],
            ['username', 'filter', 'filter' => 'strtolower'],
            ['username', 'match', 'pattern' => '/^([a-z0-9]*)$/', 'message' => Yii::t('site', 'Username invalid: only letters and number allowed')],

            ['username', 'unique', 'targetClass' => User::class, 'message' => Yii::t('site', 'This username has already been taken.')],
            ['username', 'string', 'min' => 4, 'max' => 16],
            ['full_name', 'string', 'max' => 256],

            ['email', 'trim'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],

            ['password', StrengthValidator::class, 'preset' => 'normal', 'userAttribute' => 'username'],
            ['repeat_password', 'compare', 'compareAttribute' => 'password'],

            ['referrer_id', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['referrer_id', 'integer'],
        ];
    }

    public function beforeValidate() {
        if(empty($this->referrer_id)){
            $this->referrer_id = null;
        }
        return parent::beforeValidate();
    }

    public function attributeLabels() {
        return [
            'full_name'       => Yii::t('site', 'Full name'),
            'country'         => Yii::t('site', 'Country'),
            'phone'           => Yii::t('site', 'Phone'),
            'referrer'        => Yii::t('site', 'Your curator'),
            'username'        => Yii::t('site', 'Username'),
            'password'        => Yii::t('site', 'Password'),
            'repeat_password' => Yii::t('site', 'Repeat password'),
            'email'           => Yii::t('site', 'Email'),
            'referrer_id'     => Yii::t('site', 'Referrer'),
        ];
    }

    /**
     * Signs user up.
     *
     * @return bool whether the creating new account was successful and email was sent
     */
    public function signup() {
        if(!$this->validate()){
            return null;
        }

        $user = new User();
        $user->full_name = $this->full_name;
        $user->country = $this->country;
        $user->phone = $this->phone;
        if(in_array($this->username, ['admin', 'login', 'signup'])){
            $this->addError('referrer', Yii::t('site', 'Forbidden username.'));
            return false;
        }
        $user->username = $this->username;
        $user->email = $this->email;
        $user->role = User::find()->count() ? User::ROLE_USER : User::ROLE_ADMIN;
        //$user->status = $user->role == User::ROLE_ADMIN ? User::STATUS_ACTIVE : User::STATUS_INACTIVE;
        $user->status = User::STATUS_ACTIVE;
        $referrer = User::findByUsername($this->referrer, null);
        if(empty($referrer)){
            $this->addError('referrer', Yii::t('site', 'Curator not found.'));
            return false;
        }
        /*
        if(($referrer->status != User::STATUS_ACTIVE) || !($referrer->isActive() || $referrer->isActiveForever())){
            $this->addError('referrer', Yii::t('site', 'Curator as not yet been activated.'));
            return false;
        }
        */
        $user->referrer_id = $referrer->id;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->generateEmailVerificationToken();

        if(!($result = $user->save())){
            $this->addErrors($user->getErrors());
        }else{
            //$this->sendEmail($user);
        }
        return $result;
    }

    /**
     * Sends confirmation email to user
     *
     * @param User $user user model to with email should be send
     *
     * @return bool whether the email was sent
     */
    protected function sendEmail($user) {
        try{
            return Yii::$app
                ->mailer
                ->compose(
                    ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                    ['user' => $user]
                )
                ->setFrom([Yii::$app->mailer->getTransport()->getUsername() => Yii::$app->params['siteName'] . ' robot'])
                ->setTo($this->email)
                ->setSubject(Yii::t('site', 'Account registration') . ' ' . Yii::$app->params['siteName'])
                ->send();
        }catch(\Exception $e){
            if(Yii::$app->has('session')){
                Yii::$app->session->addFlash('error', $e->getMessage());
            }
            return false;
        }
    }
}
