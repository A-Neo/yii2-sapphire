<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "emerald_users".
 *
 * @property int $id
 * @property int $id_table
 * @property int $id_ref
 * @property int $id_user
 * @property int $created_at
 * @property int $updated_at
 */
class EmeraldUsers extends \yii\db\ActiveRecord
{
    private $_username;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'emerald_users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_table', 'id_ref', 'id_user', 'created_at', 'updated_at'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_table' => 'Id Table',
            'id_ref' => 'Id Ref',
            'id_user' => 'Id User',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => time(),
            ],
        ];
    }

    public function getUsername()
    {
        if ($this->_username === null) {
            $user = User::findOne(['id' => $this->id_user]);
            if ($user) {
                $this->_username = $user->username;
            } else {
                $this->_username = 'Unnamed';
            }
        }
        return $this->_username;
    }


}
