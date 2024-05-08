<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\caching\TagDependency;

/**
 * This is the model class for table "{{%payout}}".
 *
 * @property int        $id
 * @property int|null   $balance_id
 * @property int|null   $user_id
 * @property int|null   $history_id
 * @property float|null $amount
 * @property int        $status
 * @property string     $comment
 * @property int        $created_at
 * @property int        $updated_at
 * @property string     $wallet
 * @property string     $username
 *
 * @property Balance    $balance
 * @property History    $history
 * @property User       $user
 */
class Payout extends \yii\db\ActiveRecord
{

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE   = 1;
    const STATUS_REJECT   = -1;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '{{%payout}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['balance_id', 'user_id', 'history_id', 'status', 'comission', 'created_at', 'updated_at'], 'integer'],
            [['amount'], 'number'],
            [['comment'], 'string'],
            [['wallet_type'], 'string'],
            [['status'], 'default', 'value' => self::STATUS_INACTIVE],
            [['status'], 'in', 'range' => [self::STATUS_INACTIVE, self::STATUS_ACTIVE, self::STATUS_REJECT]],
            [['balance_id'], 'exist', 'skipOnError' => true, 'targetClass' => Balance::class, 'targetAttribute' => ['balance_id' => 'id']],
            [['history_id'], 'exist', 'skipOnError' => true, 'targetClass' => History::class, 'targetAttribute' => ['history_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id'         => Yii::t('site', 'ID'),
            'balance_id' => Yii::t('site', 'Balance'),
            'user_id'    => Yii::t('site', 'User'),
            'history_id' => Yii::t('site', 'History'),
            'amount'     => Yii::t('site', 'Sum'),
            'status'     => Yii::t('site', 'Status'),
            'comment'    => Yii::t('site', 'Comment'),
            'created_at' => Yii::t('site', 'Created'),
            'updated_at' => Yii::t('site', 'Updated'),
        ];
    }

    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);
        TagDependency::invalidate(Yii::$app->cache, ['payouts', 'payout-' . $this->id]);
    }

    public function behaviors() {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * Gets query for [[Balance]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBalance() {
        return $this->hasOne(Balance::className(), ['id' => 'balance_id']);
    }

    /**
     * Gets query for [[History]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHistory() {
        return $this->hasOne(History::className(), ['id' => 'history_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getStatusName() {
        return $this->getStatusesList()[$this->status];
    }

    public function getStatusesList() {
        return [
            self::STATUS_INACTIVE => Yii::t('site', 'Сonsideration'),
            self::STATUS_ACTIVE   => Yii::t('site', 'Successfully'),
            self::STATUS_REJECT   => Yii::t('site', 'Rejected'),
        ];
    }

}
