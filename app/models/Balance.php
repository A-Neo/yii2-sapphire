<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%balance}}".
 *
 * @property int        $id
 * @property int|null   $type
 * @property int|null   $table
 * @property int|null   $manual
 * @property int|null   $from_activation_id
 * @property int|null   $to_activation_id
 * @property int|null   $from_user_id
 * @property int|null   $to_user_id
 * @property int|null   $history_id
 * @property float|null $from_amount
 * @property float|null $to_amount
 * @property float|null $from_sapphire
 * @property float|null $to_sapphire
 * @property int        $status
 * @property string     $comment
 * @property int        $created_at
 * @property int        $updated_at
 *
 * @property User       $fromUser
 * @property History    $history
 * @property User       $toUser
 */
class Balance extends \yii\db\ActiveRecord
{

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE   = 1;
    const STATUS_WAITING  = -1;

    const TYPE_TRANSFER     = 0; // Внутренний перевод   (+/-)
    const TYPE_REFILL       = 1; // Пополнение  (+)
    const TYPE_PAYOUT       = 2; // Вывод (-)
    const TYPE_ACTIVATION   = 3; // Активация (-)
    const TYPE_CHARGING     = 4; // Начисление на счет (+)
    const TYPE_PROMOTION    = 5; // Продвижение (-)
    const TYPE_ACCUMULATION = 6; // Накопление (+)
    const TYPE_TOUR         = 7; // Заявка на тур (-)
    const TYPE_TRAVEL       = 8; // Начисление на баланс travel

    const TYPE_T_ACTIVE     = 9; // Активация стола Travel

    const TYPE_E_ACTIVE     = 10; // Активация стола Emerald
    const TYPE_EMERALD     = 11; // Начисление на баланс Emerald
    const TYPE_EMERALD_PASSIVE = 12; // Начисление на баланс Emerald (пассивный доход)

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '{{%balance}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['type', 'manual', 'from_activation_id', 'to_activation_id', 'from_user_id', 'to_user_id', 'from_sapphire', 'to_sapphire', 'history_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['from_amount', 'table', 'to_amount'], 'number'],
            [['manual'], 'default', 'value' => 0],
            [['comment'], 'string'],
            [['type'], 'default', 'value' => self::TYPE_TRANSFER],
            [['type'], 'in', 'range' => [self::TYPE_TRANSFER, self::TYPE_REFILL, self::TYPE_PAYOUT, self::TYPE_ACTIVATION, self::TYPE_CHARGING, self::TYPE_PROMOTION, self::TYPE_ACCUMULATION, self::TYPE_TOUR, self::TYPE_TRAVEL, self::TYPE_E_ACTIVE, self::TYPE_EMERALD, self::TYPE_EMERALD_PASSIVE]],
            [['status'], 'default', 'value' => self::STATUS_INACTIVE],
            [['status'], 'in', 'range' => [self::STATUS_INACTIVE, self::STATUS_ACTIVE, self::STATUS_WAITING]],
            [['from_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['from_user_id' => 'id']],
            [['history_id'], 'exist', 'skipOnError' => true, 'targetClass' => History::class, 'targetAttribute' => ['history_id' => 'id']],
            [['to_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['to_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id'                 => Yii::t('site', 'ID'),
            'type'               => Yii::t('site', 'Type'),
            'table'              => Yii::t('site', 'Table'),
            'manual'             => Yii::t('site', 'Manual'),
            'from_activation_id' => Yii::t('site', 'Activation'),
            'to_activation_id'   => Yii::t('site', 'Activation'),
            'from_user_id'       => Yii::t('site', 'From User'),
            'to_user_id'         => Yii::t('site', 'To User'),
            'history_id'         => Yii::t('site', 'History'),
            'from_amount'        => Yii::t('site', 'Sum'),
            'to_amount'          => Yii::t('site', 'Sum'),
            'from_sapphire'      => Yii::t('site', 'Points'),
            'to_sapphire'        => Yii::t('site', 'Points'),
            'status'             => Yii::t('site', 'Status'),
            'comment'            => Yii::t('site', 'Comment'),
            'created_at'         => Yii::t('site', 'Created'),
            'updated_at'         => Yii::t('site', 'Updated'),
        ];
    }

    public function afterDelete() {
        parent::afterDelete();
        if($this->fromUser){
            $this->fromUser->checkBalance();
        }
        if($this->toUser){
            $this->toUser->checkBalance();
        }
    }

    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);
        if($this->fromUser){
            $this->fromUser->checkBalance();
        }
        if($this->toUser){
            $this->toUser->checkBalance();
        }
    }

    public function behaviors() {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * Gets query for [[FromUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFromUser() {
        return $this->hasOne(User::class, ['id' => 'from_user_id']);
    }

    /**
     * Gets query for [[History]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHistory() {
        return $this->hasOne(History::class, ['id' => 'history_id']);
    }

    /**
     * Gets query for [[ToUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getToUser() {
        return $this->hasOne(User::class, ['id' => 'to_user_id']);
    }

    public function getStatusName() {
        return $this->getStatusesList()[$this->status];
    }

    public function getStatusesList() {
        return [
            self::STATUS_WAITING  => Yii::t('site', 'Waiting'),
            self::STATUS_INACTIVE => Yii::t('site', 'Inactive'),
            self::STATUS_ACTIVE   => Yii::t('site', 'Active'),
        ];
    }

    public function getTypeName() {
        if($this->type == self::TYPE_PROMOTION){
            return Yii::t('account', 'Buying table {n}',
                ['n' => ($this->table > 10 ? $this->table - 10 : $this->table) ]);
        }
        return $this->getTypesList()[$this->type];
    }

    public function getTypesList() {
        return [
            self::TYPE_TRANSFER     => Yii::t('site', 'Transfer'),
            self::TYPE_REFILL       => Yii::t('site', 'Refill'),
            self::TYPE_PAYOUT       => Yii::t('site', 'Payout'),
            self::TYPE_ACTIVATION   => Yii::t('site', 'Activation'),
            self::TYPE_CHARGING     => Yii::t('site', 'Charging'),
            self::TYPE_PROMOTION    => Yii::t('site', 'Promotion'),
            self::TYPE_ACCUMULATION => Yii::t('site', 'Accumulation'),
            self::TYPE_TOUR         => Yii::t('site', 'Sapphire tour'),
            self::TYPE_TRAVEL       => Yii::t('site', 'Travel'),
            self::TYPE_E_ACTIVE             => 'EMERALD - Active',
            self::TYPE_EMERALD              => 'EMERALD - Add referals',
            self::TYPE_EMERALD_PASSIVE      => 'EMERALD - Passive',
        ];
    }

    public function getFromUserName() {
        $name = '';
        if($this->type == self::TYPE_REFILL && $this->history){
            $name = $this->history->currency;
        }
        $name = $this->type == self::TYPE_REFILL && $this->manual ? 'MANUAL' : $name;
        $name = $this->fromUser ? $this->fromUser->username : $name;
        return $name;
    }

    public function getToUserName() {
        $name = $this->type == self::TYPE_PAYOUT ? 'PAYEER' : 'SYSTEM';
        $name = $this->type == self::TYPE_PAYOUT && $this->manual ? 'MANUAL' : $name;
        $name = $this->toUser ? $this->toUser->username : $name;
        return $name;
    }

}
