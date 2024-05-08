<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "travel_main".
 *
 * @property int $id
 * @property int $id_user
 * @property int $level
 * @property int $created_at
 * @property int $updated_at
 */
class TravelMain extends \yii\db\ActiveRecord
{

    const WELCOME_DEPOSIT = 1;

    const PLAN_LIST = [
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
            'slot2'        => 3000 ,
            'slot3'        => 2700,
            'refBalance'   => 1742,
            'refBalanceSt' => 1741,
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'travel_main';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_user', 'level', 'created_at', 'updated_at'], 'integer'],
            [['id_user', 'level'], 'required'],
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
            'id_user' => 'Id User',
            'level' => 'Level',
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

    public function getUsers()
    {
        return TravelUsers::find()
            ->where(['id_table' => $this->id])
            ->all();
    }

    public static function getLevels($userId)
    {
        return self::find()->where(['id_user' => $userId])->orderBy(['level' => SORT_ASC])->indexBy('level')->all();
    }

    public static function checkIsActive($userId)
    {
        return (int)self::find()->where(['id_user' => $userId])->count() > 0;
    }

    public static function getPlanList()
    {
        $result = self::PLAN_LIST;

        $names = [];
        for($i = 1; $i <= 8; $i++) {
            $names[] = 'Travel' . $i . '_contribution';
            $names[] = 'Travel' . $i . '_slot1';
            $names[] = 'Travel' . $i . '_slot2';
            $names[] = 'Travel' . $i . '_slot3';
            $names[] = 'Travel' . $i . '_refBalance';
            $names[] = 'Travel' . $i . '_refBalanceSt';
        }

        $settings = SettingModel::find()->where(['key' => $names])->indexBy('key')->all();

        for($i = 1; $i <= 8; $i++) {
            if (isset($settings['Travel' . $i . '_contribution'])) {
                $result[$i]['contribution'] = $settings['Travel' . $i . '_contribution']->value;
            }
            if (isset($settings['Travel' . $i . '_slot1'])) {
                $result[$i]['slot1'] = $settings['Travel' . $i . '_slot1']->value;
            }
            if (isset($settings['Travel' . $i . '_slot2'])) {
                $result[$i]['slot2'] = $settings['Travel' . $i . '_slot2']->value;
            }
            if (isset($settings['Travel' . $i . '_slot3'])) {
                $result[$i]['slot3'] = $settings['Travel' . $i . '_slot3']->value;
            }
            if (isset($settings['Travel' . $i . '_refBalance'])) {
                $result[$i]['refBalance'] = $settings['Travel' . $i . '_refBalance']->value;
            }
            if (isset($settings['Travel' . $i . '_refBalanceSt'])) {
                $result[$i]['_refBalanceSt'] = $settings['Travel' . $i . '_refBalanceSt']->value;
            }
        }

        return $result;
    }

    public static function initUser($userId)
    {
        //Тарифы
        $planList = self::getPlanList();

        //Получаем юзера
        $userId = (int)$userId;
        $user = User::findOne(['id' => $userId]);
        if (!$user) {
            return 'пользователь не найден';
        }

        //Проверяем активность юзера и реферера
        if (TravelMain::checkIsActive($userId)) {
            return 'уровень уже активирован';
        }
        if (!TravelMain::checkIsActive($user->referrer_id)) {
            return 'ваш реферер не активировал проект';
        }

        //Проверяем баланс
        if ($user->balance < $planList[1]['contribution'] + self::WELCOME_DEPOSIT) {
            return 'недостаточно средств на балансе! Вам надо иметь как минимум: $' . ($planList[1]['contribution'] + self::WELCOME_DEPOSIT);
        }

        $transaction = Yii::$app->db->beginTransaction();

        //Добавляем запись о столе юзера
        $tMain = new self();
        $tMain->id_user = $userId;
        $tMain->level = 1;
        if (!$tMain->save()) {
            $transaction->rollBack();
            return 'что-то пошло не так [ERR001]';
        }
        //Снимаем с юзера деньги
        /*
        $user->balance -= $planList[1]['contribution']  + self::WELCOME_DEPOSIT;
        $user->updateAttributes(['balance']);
        self::makeBalanceRecord(Balance::TYPE_ACTIVATION, 1,
                                 $user->id, 0, $planList[1]['contribution'], 0,
                                 'Активация пользователя');
        */
        self::makeBalanceRecord(Balance::TYPE_ACTIVATION, 1,
                                 $user->id, 0, self::WELCOME_DEPOSIT, 0,
                                 'На развитие');

        //Добавляем юзера к рефереру
        $table = self::findOne(['id_user' => $user->referrer_id, 'level' => 1]);

        if (!$table) {
            $transaction->rollBack();
            return 'у реферера нету стола';
        }
        $tUser = new TravelUsers();
        $tUser->id_table = $table->id;
        $tUser->id_ref = $user->referrer_id;
        $tUser->id_user = $userId;

        if (!$tUser->save()) {
            $transaction->rollBack();
            return 'что-то пошло не так [ERR002]';
        }

        //инициализация завершена - коммит
        $transaction->commit();

        //Практически основное в данной пирамиде - проверка уровня реферера, отчисления ему, левелап
        self::updateReferer($user->referrer_id, 1, $userId);

        return true;
    }

    public static function updateReferer($refId, $level, $userId)
    {
        //Тарифы
        $planList = self::getPlanList();

        //Достаём самого реферера
        $refId = (int)$refId;
        $ref = User::findOne(['id' => $refId]);
        if (!$ref) {
            return;
        }

        //Проверяем, каким по счету стал в нужном левеле юзер
        //Выбираем стол
        $level = (int)$level;
        $table = self::findOne(['id_user' => $refId, 'level' => $level]);
        if (!$table) {
            return;
        }

        //Получаем количество ставших на стол
        $cnt = (int)TravelUsers::find()->where(['id_table' => $table->id])->count();
        if ($cnt < 1) {
            return;
        }

        //Для 1, 2 и 3 участника просто снимаем деньги и кидаем на накопление и на баланс
        // для остальных - скидываем деньги на вывод и на баланс ST, а так же чекаем левелап
        if ($cnt < 4) {
            //Берем цифры из нужного слота и сохраняем баланс и записи в историю
            switch($cnt) {
                case 1:
                    $balance = $planList[$level]['slot1'];
                    $accum = $planList[$level]['contribution'] - $planList[$level]['slot1'];
                    break;
                case 2:
                    $balance = $planList[$level]['slot2'];
                    $accum = $planList[$level]['contribution'] - $planList[$level]['slot2'];
                    break;
                case 3:
                    $balance = $planList[$level]['slot3'];
                    $accum = $planList[$level]['contribution'] - $planList[$level]['slot3'];
                    break;
            }
            $ref->balance += $balance;
            $ref->accumulation += $accum;
            $ref->updateAttributes(['balance', 'accumulation']);

            self::makeBalanceRecord(Balance::TYPE_CHARGING, $level,
                                    $userId, $refId, ($level > 1 ? 0 : $balance), $balance,
                                    'Пополнение от реферала');
            self::makeBalanceRecord(Balance::TYPE_ACCUMULATION, $level,
                                    $userId, $refId, ($level > 1 ? 0 : $accum), $accum,
                                    'Пополнение от реферала');
        }
        //Сюда теперь попадает 3-й юзер - стол открывается, но деньги списаны ранее
        if ($cnt > 2) {
            if ($cnt > 3) {
                //Берем цифры из нужного слота и сохраняем баланс и записи в историю
                //Делаем это только на 4-го и последующих приглашённых
                $balance = $planList[$level]['refBalance'];
                $balanceSt = $planList[$level]['refBalanceSt'];

                $ref->balance_travel += $balanceSt;
                $ref->updateAttributes(['balance_travel']);

                self::makeBalanceRecord(Balance::TYPE_CHARGING, $level,
                                        0, $refId, $balance, $balance,
                                        'Пополнение от реферала');

                self::makeBalanceRecord(Balance::TYPE_TRAVEL, $level,
                                        0, $refId, $balanceSt, 0,
                                        'Пополнение от реферала на баланс ST');

                if ($level == 1) {
                    self::makeBalanceRecord(Balance::TYPE_TRAVEL, $level,
                                            $userId, 0, $balance + $balanceSt, 0,
                                            'Открытие стола ' . $level);
                }

            }
            //Теперь чекаем левелап
            $upLevel = $level + 1;
            if ($upLevel < 9) {
                $upTable = self::findOne(['id_user' => $ref->id, 'level' => $upLevel]);
                if (!$upTable) {
                    //Не нашли стола - открываем
                    $table = new self();
                    $table->id_user = $ref->id;
                    $table->level = $upLevel;
                    if (!$table->save()) {
                        return;
                    }

                    //Списываем деньги с накопительного счета
                    /*
                    $ref->accumulation -= $planList[$upLevel]['contribution'];
                    $ref->updateAttributes(['accumulation']);
                    self::makeBalanceRecord(Balance::TYPE_ACCUMULATION, $upLevel,
                                            $ref->id, 0, $planList[$upLevel]['contribution'], 0,
                                            'Активация стола ' . $upLevel);
                    */


                    //Проверяем, можно ли стать на стол к своему рефу
                    $refRefTable = self::findOne(['id_user' => $ref->referrer_id, 'level' => $upLevel]);
                    if ($refRefTable) {
                        //Стол доступен - становимся на него и рекурсивно вызываем этот же метод уже для реферера
                        $tUser = new TravelUsers();
                        $tUser->id_table = $refRefTable->id;
                        $tUser->id_ref = $ref->referrer_id;
                        $tUser->id_user = $ref->id;
                        if ($tUser->save()) {
                            self::updateReferer($ref->referrer_id, $upLevel, $ref->id);
                        }
                    } else {
                        //Стол недоступен - откладываем запись "на потом"
                        $delay = new TravelDelay();
                        $delay->id_user = $ref->id;
                        $delay->id_ref = $ref->referrer_id;
                        $delay->level = $upLevel;
                        $delay->date_end = time() + 172800;
                        $delay->save();
                    }
                }
            }
        }

    }

    public static function makeBalanceRecord($type, $level, $from, $to, $fromAmount, $toAmount, $comment = '')
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

    public static function checkDelayUsers()
    {
        //Проверка отложенных юзеров - проверяем всех
        $delayUsers = TravelDelay::find()->all();

        foreach($delayUsers as $delUser) {
            /** @var $delUser TravelDelay */
            //Проверяем наличие стола у реферера
            $refRefTable = self::findOne(['id_user' => $delUser->id_ref, 'level' => $delUser->level]);

            //Стол есть - выполняем апдейт и удаляем запись об отложенном юзере
            if ($refRefTable) {
                $tUser = new TravelUsers();
                $tUser->id_table = $refRefTable->id;
                $tUser->id_ref = $delUser->id_ref;
                $tUser->id_user = $delUser->id_user;
                if ($tUser->save()) {
                    self::updateReferer($delUser->id_ref, $delUser->level, $delUser->id_user);
                    $delUser->delete();
                }
            } else {
                //Стола нет - проверим время, если час пробил - запускаем процедуру перехода
                if ((int)$delUser->date_end < time()) {
                    //ищем следующего рефа, к кому уйти
                    $newRefTable = self::findNewRef($delUser->id_ref, $delUser->level);
                    if ($newRefTable) {
                        //Меняем реферера для юзера
                        /* Не меняем - заказчик решил, что не надо
                        $user = User::findOne(['id' => $delUser->id_user]);
                        if (!$user) {
                            continue;
                        }
                        $user->referrer_id = $newRefTable->id_user;
                        $user->updateAttributes(['referrer_id']);
                        */
                        //Теперь становимся на стол к найденному рефереру
                        $tUser = new TravelUsers();
                        $tUser->id_table = $newRefTable->id;
                        $tUser->id_ref = $newRefTable->id_user;
                        $tUser->id_user = $delUser->id_user;
                        if ($tUser->save()) {
                            self::updateReferer($newRefTable->id_user, $delUser->level, $delUser->id_user);
                            $delUser->delete();
                        }
                    }
                }
            }
        }

    }

    public static function findNewRef($idRef, $level)
    {
        $i = 0;
        $refRefTable = self::findOne(['id_user' => $idRef, 'level' => $level]);
        while($refRefTable == null && $i < 1000) {
            $user = User::findOne(['id' => $idRef]);
            if (!$user) {
                return null;
            }
            $idRef = $user->referrer_id;
            $refRefTable = self::findOne(['id_user' => $idRef, 'level' => $level]);
            $i++;
        }

        return $refRefTable;
    }

}
