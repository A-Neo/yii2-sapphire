<?php

namespace app\components\emerald;

use app\models\TravelDelay;
use app\models\TravelUsers;
use yii\db\ActiveRecord;

use app\components\RecordCopier;

use app\models\User;
use app\models\Balance;
use app\models\EmeraldMain;
use app\models\EmeraldUsers;
use app\models\EmeraldDelay;

trait BaseTrait
{

    /**
     * Получаем всех юзеров на столе
     *
     * @return EmeraldUsers[]|array|ActiveRecord[]
     */
    public function getUsers($id_table)
    {
        return EmeraldUsers::find()->where(['id_table' => $id_table])->all();
    }

    /**
     * Получаем рефералов юзера
     *
     * @return EmeraldUsers[]|array|ActiveRecord[]
     */
    public function getRefUsers($id_ref)
    {
        return EmeraldUsers::find()->where(['id_table' => $this->id])->all();
//        return EmeraldUsers::find()->where(['id_ref' => $id_ref])->all();
    }

    public static function getUsersCount($id_table)
    {
        return EmeraldUsers::find()->where(['id_table' => $id_table])->count();
        //return $users->count();
    }

    /**
     * Получаем уровни юзера
     *
     * @return EmeraldMain[]|array|ActiveRecord[]
     */
    public static function getLevels()
    {
        return EmeraldMain::find()->where(['id_user' => self::$user->id])->orderBy(['level' => SORT_ASC])->indexBy('level')->all();
    }

    public static function getLastLevels()
    {
        return EmeraldMain::find()->where(['id_user' => 10])->orderBy(['id' => SORT_DESC])->limit(1)->one();
    }

    /**
     * Проверяем активнен ли юзер
     *
     * @param $userId
     * @return bool
     */
    public static function checkIsActive($id_user)
    {
        return (int)EmeraldMain::find()->where(['id_user' => $id_user])->count() > 0;
    }

    /**
     * Увеличиваем уровень юзера
     *
     * @param $model
     * @return void
     */
    public static function addLevel($model)
    {
        RecordCopier::copyWithAttributes($model, ['level' => $model->level + 1]);
    }

    public static function checkIsBalance()
    {
        return self::$user->balance > self::CONTRIBUTION;
    }

    public static function makeBalanceRecord($type, $level, $from, $to, $fromAmount, $toAmount, $comment = '')
    {
        $balance = new Balance();
        $balance->type = $type;

        if($level > 0) $balance->table = $level;
        if($from > 0) $balance->from_user_id = $from;
        if($to > 0) $balance->to_user_id = $to;
        if($fromAmount > 0) $balance->from_amount = $fromAmount;
        if($toAmount > 0) $balance->to_amount = $toAmount;

        $balance->comment = $comment;
        $balance->status = 1;

        return $balance->save();
    }

    public static function setLevel($level = 1)
    {
        $up_level = $level + 1;
        if ($up_level < self::MAX_LEVEL) {

            $up_table = EmeraldMain::findOne(['id_user' => self::$reffer->id, 'level' => $up_level]);

            if (!$up_table) {
                //Не нашли стола - открываем
                $table = new EmeraldMain();
                $table->id_user = self::$reffer->id;
                $table->level = $up_level;
                if (!$table->save()) return;


                //Проверяем, можно ли стать на стол к своему рефу
                $table_ref = self::findOne(['id_user' => self::$reffer->id_ref_emerald, 'level' => $up_level]);

                if ($table_ref) {
                    //Стол доступен - становимся на него и рекурсивно вызываем этот же метод уже для реферера
                    $model_user = new EmeraldUsers();
                    $model_user->id_table = $table_ref->id;
                    $model_user->id_ref = self::$reffer->id_ref_emerald;
                    $model_user->id_user = self::$reffer->id;
                    if ($model_user->save()) self::updateReferer(self::$reffer->id_ref_emerald, $up_level, self::$reffer->id);
                } else {
                    //Стол недоступен - откладываем запись "на потом"
                    $delay = new EmeraldDelay();
                    $delay->id_user = self::$reffer->id;
                    $delay->id_ref = self::$reffer->id_ref_emerald;
                    $delay->level = $up_level;
                    $delay->date_end = time() + 172800;
                    $delay->save();
                }
            }
        }
    }

    public static function updateReferer($level = 1)
    {
        $model_main = EmeraldMain::findOne(['id_user' => self::$reffer->id, 'level' => $level]);
        if (!$model_main) return;

        //Получаем количество ставших на стол
        $count_user_table = (int)EmeraldUsers::find()->where(['id_ref' => self::$reffer->id ,'id_table' => $model_main->id])->count();
        if ($count_user_table < 1) return;

        //Для 1, 2 и 3 участника просто снимаем деньги и кидаем на накопление и на баланс
        // для остальных - скидываем деньги на вывод и на баланс ST, а так же чекаем левелап

        $balance = (double) self::PLAN_LIST[$level]['payment'];

        self::$reffer->balance += (double) self::PLAN_LIST[$level]['payment'];
        self::$reffer->updateAttributes(['balance']);

        self::makeBalanceRecord(Balance::TYPE_EMERALD, $level,
            0, self::$reffer->id, 0, (double) self::PLAN_LIST[$level]['payment'], 'Пополнение от реферала');

        // add passive balance function

        if ($count_user_table == 4) {
            self::setLevel($level);
            // add passive balance function
        }

        if ($level > 2) {
            //self::addPassiveBalance($level);
        }
    }

    public static function addPassiveBalance($level)
    {
        $passive = self::PLAN_LIST[$level];
    }

}


