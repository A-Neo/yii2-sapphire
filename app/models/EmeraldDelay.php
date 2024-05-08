<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "emerald_delay".
 *
 * @property int $id
 * @property int $id_ref
 * @property int $id_user
 * @property int $level
 * @property int $date_end
 */
class EmeraldDelay extends \yii\db\ActiveRecord
{
    private $_userLogin = null;
    private $_user = null;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'emerald_delay';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_ref', 'id_user', 'level', 'date_end'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_ref' => 'Id Ref',
            'id_user' => 'Id User',
            'level' => 'Level',
            'date_end' => 'Date End',
        ];
    }

    public function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findOne(['id' => $this->id_user]);
            if (!$this->_user) {
                $this->_user = new User();
                $this->_user->username = 'John Doe';
            }
        }

        return $this->_user;
    }
    public function getUserLogin()
    {
        if ($this->_userLogin === null) {
            $this->_userLogin = $this->getUser()->username;
        }

        return $this->_userLogin;
    }

}
