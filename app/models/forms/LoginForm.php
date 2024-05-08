<?php

namespace app\models\forms;

use app\models\User;
use yii\base\Model;
use Yii;

/**
 * Login form
 */
class LoginForm extends Model
{

    public $username;
    public $password;
    public $rememberMe = true;

    private $_user;

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            [['username', 'password'], 'trim'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
            ['username', 'validateUser'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'username'   => Yii::t('site', 'Username'),
            'password'   => Yii::t('site', 'Password'),
            'rememberMe' => Yii::t('site', 'Remember me'),
        ];
    }


    public function validateUser($attribute, $params) {
        $user = $this->getUser();
        if ($user) {
            if ($user->status == User::STATUS_BLOCKED) {
                $this->addError($attribute, Yii::t('site', 'Account is blocked. Please contact with administrator if you dont know the reason.'));
            }
            if ($user->status == User::STATUS_INACTIVE) {
                $this->addError($attribute, Yii::t('site', 'Account is inactive. Please verify your email.'));
            }
        }
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params) {
        $user = $this->getUser();
        if (!$user || !$user->validatePassword($this->password)) {
            $this->addError($attribute, Yii::t('site', 'Incorrect username or password.'));
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login() {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        }

        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser() {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->username, null);
        }
        return $this->_user;
    }
}