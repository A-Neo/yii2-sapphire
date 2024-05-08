<?php

namespace app\models\forms;

use Yii;
use yii\base\Model;
use app\models\User;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model
{

    public $email;
    public $username;

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            ['email', 'trim'],
            ['username', 'trim'],
            ['email', 'email'],
            ['email', 'exist',
             'targetClass' => User::class,
             'filter'      => ['status' => [User::STATUS_ACTIVE]],
             'message'     => Yii::t('site', 'There is no user with this email address.'),
            ],
            ['username', 'exist',
             'targetClass' => User::class,
             'filter'      => ['status' => [User::STATUS_ACTIVE]],
             'message'     => Yii::t('site', 'There is no user with this username.'),
            ],
        ];
    }

    public function attributeLabels() {
        return [
            'email'    => Yii::t('site', 'Email'),
            'username' => Yii::t('site', 'Username'),
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return bool whether the email was send
     */
    public function sendEmail() {
        /* @var $user User */
        $user = null;
        if($this->email){
            $user = User::findByEmail($this->email);
        }else if($this->username){
            $user = User::findByUsername($this->username);
        }
        if(!$user){
            return false;
        }
        if(empty($this->email)){
            $this->email = $user->email;
        }
        if(!User::isPasswordResetTokenValid($user->password_reset_token)){
            $user->generatePasswordResetToken();
            if(!$user->save()){
                return false;
            }
        }
        try{
            return Yii::$app
                ->mailer
                ->compose(
                    ['html' => 'passwordResetToken-html', 'text' => 'passwordResetToken-text'],
                    ['user' => $user]
                )
                ->setFrom([Yii::$app->mailer->getTransport()->getUsername() => Yii::$app->params['siteName'] . ' robot'])
                ->setTo($user->email)
                ->setSubject(Yii::t('site', 'Password reset for') . ' ' . Yii::$app->params['siteName'])
                ->send();
        }catch(\Exception $e){
            if(Yii::$app->has('session')){
                Yii::$app->session->addFlash('error', $e->getMessage());
            }
            return false;
        }
    }
}
