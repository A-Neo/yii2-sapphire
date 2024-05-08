<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "travel".
 *
 * @property int        $id
 * @property int        $user_id
 * @property int        $level
 * @property int        $status
 * @property int        $slot1
 * @property int        $slot2
 * @property int        $slot3
 * @property int        $created_at
 * @property int        $updated_at
 *
 * @property User       $user
 */
class Travel extends \yii\db\ActiveRecord
{
    const STATUS_UNPAID   = 1;
    const STATUS_ACTIVE   = 2;
    const STATUS_CLOSED   = 3;

    public $slot_one;
    public $slot_two;
    public $slot_tree;

    const WELCOME_DEPOSIT = 1;
    const MAX_LEVEL = 7;

    private $_levels = [];

    private $_slot1 = null;
    private $_slot2 = null;
    private $_slot3 = null;

    static $planList = [
        1 => [
            'contribution' => 8,
            'slot1'        => 2,
            'slot2'        => 3,
            'slot3'        => 3,
            'refBalance'   => 4,
            'refBalanceSt' => 4,
        ],
        2 => [
            'contribution' => 16,
            'slot1'        => 4,
            'slot2'        => 4,
            'slot3'        => 4,
            'refBalance'   => 8,
            'refBalanceSt' => 8,
        ],
        3 => [
            'contribution' => 36,
            'slot1'        => 8,
            'slot2'        => 8,
            'slot3'        => 8,
            'refBalance'   => 18,
            'refBalanceSt' => 18,
        ],
        4 => [
            'contribution' => 84,
            'slot1'        => 16,
            'slot2'        => 16,
            'slot3'        => 16,
            'refBalance'   => 42,
            'refBalanceSt' => 42,
        ],
        5 => [
            'contribution' => 204,
            'slot1'        => 35,
            'slot2'        => 35,
            'slot3'        => 35,
            'refBalance'   => 102,
            'refBalanceSt' => 102,
        ],
        6 => [
            'contribution' => 507,
            'slot1'        => 70,
            'slot2'        => 70,
            'slot3'        => 70,
            'refBalance'   => 254,
            'refBalanceSt' => 253,
        ],
        7 => [
            'contribution' => 1311,
            'slot1'        => 150,
            'slot2'        => 150,
            'slot3'        => 150,
            'refBalance'   => 656,
            'refBalanceSt' => 655,
        ],
        8 => [
            'contribution' => 3483,
            'slot1'        => 2700,
            'slot2'        => 300,
            'slot3'        => 2700,
            'refBalance'   => 1742,
            'refBalanceSt' => 1741,
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'travel';
    }

    function __construct($user_id = false) {
        if (!$user_id) {
            $this->user_id = Yii::$app->user->identity->getId();
        } else {
            $this->user_id = $user_id;
        }
        // если нет записи в таблице, создаем первый уровень неоплаченный
        if ($this->user_id && $this->getMaxCurrentLevel() === 0) {
            $this->level = 1;
            $this->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['user_id'], 'required'],
            [['user_id', 'level', 'status', 'created_at', 'updated_at'], 'integer'],
            [['level'], 'default', 'value' => 1],
            [['status'], 'default', 'value' => self::STATUS_UNPAID],
            [['status'], 'in', 'range' => [self::STATUS_UNPAID, self::STATUS_ACTIVE, self::STATUS_CLOSED]]
        ];
    }

    // public function afterFind(){
    //     $this->slot_one = $this->hasOne(User::class, ['id' => 'slot1']);
    // } 

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id'         => Yii::t('site', 'ID'),
            'user_id'    => Yii::t('site', 'User'),
            'level'      => Yii::t('site', 'Table'),
            'status'     => Yii::t('site', 'Status'),
            'slot1'      => Yii::t('site', 'Slot 1'),
            'slot2'      => Yii::t('site', 'Slot 2'),
            'slot3'      => Yii::t('site', 'Slot 3'),
            'created_at' => Yii::t('site', 'Created'),
            'updated_at' => Yii::t('site', 'Updated'),
        ];
    }

    // public function behaviors() {
    //     return [
    //         TimestampBehavior::class,
    //     ];
    // }

    public function getStatusesList() {
        return [
            self::STATUS_ACTIVE   => Yii::t('site', 'Active'),
            self::STATUS_CLOSED   => Yii::t('site', 'Closed'),
            self::STATUS_UNPAID   => Yii::t('site', 'Unpaid'),
        ];
    }

    public function getMaxCurrentLevel() {
        return (int)self::find()->select('level')->where(['user_id' => $this->user_id])->groupBy('level')->max('level');
    }

    public function getCurrentLevelInfo() {
        $user_max_level = $this->getMaxCurrentLevel();
        return self::find()->where(['user_id' => $this->user_id, 'level' => $user_max_level])->one();
    }

    public function getSlotOne() {
        if ($this->_slot1 === null) {
            if ((int)$this->_slot1 > 0) {
                $this->_slot1 = User::findOne(['id' => $this->slot1]);
            } else {
                $this->_slot1 = false;
            }
        }
        return $this->_slot1;
    }

    public function getSlotTwo() {
        if ($this->_slot2 === null) {
            if ((int)$this->_slot2 > 0) {
                $this->_slot2 = User::findOne(['id' => $this->slot2]);
            } else {
                $this->_slot2 = false;
            }
        }
        return $this->_slot2;
    }

    public function getSlotTree() {
        if ($this->_slot3 === null) {
            if ((int)$this->_slot3 > 0) {
                $this->_slot3 = User::findOne(['id' => $this->slot3]);
            } else {
                $this->_slot3 = false;
            }
        }
        return $this->_slot3;
    }

    public function getLevels()
    {
        if (count($this->_levels) == 0) {
            $this->_levels = self::find()->where(['user_id' => $this->user_id])->indexBy('level')->all();
        }
        return $this->_levels;
    }

    public function checkReferReady()
    {
        $user = User::getCurrent();

        return self::find()->where(['user_id' => $user->referrer_id, 'status' => self::STATUS_ACTIVE])->count() > 0;
    }

    public function updateTables()
    {
        //Сначала апдейт юзера
        $user = User::getCurrent();

        $level = $this->getCurrentLevelInfo();
        if ($level->level == 1) {
            $user->balance -= self::$planList[1]['contribution']  + self::WELCOME_DEPOSIT;
            $user->updateAttributes(['balance']);
            $this->makeBalanceRecord(Balance::TYPE_ACTIVATION, 1,
                                          $user->id, 0, self::$planList[1]['contribution'], 0,
                                          'Активация пользователя');
            $this->makeBalanceRecord(Balance::TYPE_ACTIVATION, 1,
                                     $user->id, 0, self::WELCOME_DEPOSIT, 0,
                                     'На развитие');
        } else {
            $user->accumulation -= self::$planList[(int)$level->level]['contribution'];
            $user->updateAttributes(['accumulation']);
            $this->makeBalanceRecord(Balance::TYPE_ACCUMULATION, $level->level,
                                     $user->id, 0, self::$planList[(int)$level->level]['contribution'], 0,
                                     'Активация уровня ' . $level->level);
        }

        //Теперь реферера
        $refUser = User::findOne(['id' => $user->referrer_id]);
        $refTravel = new self($user->referrer_id);
        $refLevel = $refTravel->getCurrentLevelInfo();
        $refIndex = (int)$refLevel->level;

        if(!$refTravel->getSlotOne()) {
            $refLevel->slot1 = $user->id;
            $refLevel->updateAttributes(['slot1']);
        } elseif (!$refTravel->getSlotTwo()) {
            $refLevel->slot2 = $user->id;
            $refLevel->updateAttributes(['slot2']);
        } elseif (!$refTravel->getSlotTree()) {
            $refLevel->slot3 = $user->id;
            $refLevel->updateAttributes(['slot3']);
        } else {
            $refTravel = new Travel($user->referrer_id);
            $refTravel->level = $refLevel->level + 1;
            $refTravel->status = self::STATUS_ACTIVE;
            $refTravel->slot1 = $user->id;
            $refTravel->save();
            $refIndex = (int)$refTravel->level;
            $refUser->accumulation -= self::$planList[(int)$refTravel->level]['contribution'];
            $refUser->updateAttributes(['accumulation']);
            $this->makeBalanceRecord(Balance::TYPE_ACCUMULATION, $refTravel->level,
                                     $refUser->id, 0, self::$planList[(int)$refTravel->level]['contribution'], 0,
                                     'Активация уровня ' . $refTravel->level);
        }

        $refUser->balance += self::$planList[$refIndex]['refBalance'];
        $refUser->balance_travel += self::$planList[$refIndex]['refBalanceSt'];
        $refUser->updateAttributes(['balance', 'balance_travel']);

        $this->makeBalanceRecord(Balance::TYPE_CHARGING, $refTravel->level,
                                 0, $refUser->id, 0, self::$planList[$refIndex]['refBalance'],
                                 'Пополнение от реферала');
        $this->makeBalanceRecord(Balance::TYPE_TRAVEL, $refTravel->level,
                                 0, $refUser->id, 0, self::$planList[$refIndex]['refBalanceSt'],
                                 'Пополнение от реферала');


    }

    public function makeBalanceRecord($type, $level, $from, $to, $fromAmount, $toAmount, $comment = '')
    {
        $balance = new Balance();
        $balance->type = $type;
        if ($level > 0) {
            $balance->table = $level;
        }
        if ($from > 0) {
            $balance->from_user_id = $from;
        }
        if ($to > 0) {
            $balance->to_user_id = $to;
        }
        if ($fromAmount > 0) {
            $balance->from_amount = $fromAmount;
        }
        if ($toAmount > 0) {
            $balance->to_amount = $toAmount;
        }
        $balance->comment = $comment;
        $balance->status = 1;
        return $balance->save();
    }

}
