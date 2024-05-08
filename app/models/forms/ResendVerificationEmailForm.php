<?php

namespace app\models\forms;

use Yii;
use app\models\User;
use yii\base\Model;

class ResendVerificationEmailForm extends Model
{
    /**
     * @var string
     */
    public $email;

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
             'targetClass' => User::class,
             'filter'      => ['status' => User::STATUS_INACTIVE],
             'message'     => Yii::t('site', 'There is no user with this email address.'),
            ],
        ];
    }

    public function attributeLabels() {
        return [
            'email' => Yii::t('site', 'Email'),
        ];
    }

    /**
     * Sends confirmation email to user
     *
     * @return bool whether the email was sent
     */
    public function sendEmail() {
        $user = User::findOne([
            'email'  => $this->email,
            'status' => User::STATUS_INACTIVE,
        ]);
        if($user === null){
            return false;
        }
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
