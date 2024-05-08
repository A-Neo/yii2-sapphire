<?php

namespace app\components\emerald;

trait InitTrait
{
    public static function initUser($id_user, $id_referal)
    {
        self::$user     = User::findOne('id' => $id_user);
        self::$reffer   = User::findOne('id' => $id_referal);

        if (!self::$user) return 'Пользователь не найден';
        if (!self::$reffer) return 'Пользователь по реф id не найден';

        if (self::checkIsActive(self::$user->id)) return 'Уровень пользователя уже активирован';
        if (!self::checkIsActive(self::$reffer->id)) return 'Ваш реф. пользователь не активировал проект';

        if (!self::checkIsBalance()) return 'Недостаточно средств на балансе! Вам надо иметь как минимум: $' . self::CONTRIBUTION;



            User::findOne(['id' => $userId]);
        // Проверка юзера на активность
        self::checkIsActive($user)

            //Проверяем активность юзера и реферера
            if (self::checkIsActive($userId)) {
                return 'уровень уже активирован';
            }
            if (!self::checkIsActive($ref_user->id)) {
                return 'ваш реферер не активировал проект';
            }
    }






}

public static function initUser($userId, $ref_user = null)
{
    //Тарифы
    $planList = self::getPlanList();

    //Получаем юзера
    $userId = (int)$userId;
    $user = User::findOne(['id' => $userId]);
    if (!$user) return 'пользователь не найден';


    //Проверяем активность юзера и реферера
    if (self::checkIsActive($userId)) {
        return 'уровень уже активирован';
    }
    if (!self::checkIsActive($ref_user->id)) {
        return 'ваш реферер не активировал проект';
    }
    self::$user = $user;
    self::$user_id = $userId;
    self::$ref_id = $ref_user->id;

    // Проверяем баланс
    self::$user->balance += 1000;
    self::$user->save();
    //Проверяем баланс
    if (self::$user->balance < $planList[1]['contribution'] + self::WELCOME_DEPOSIT) {
        return 'недостаточно средств на балансе! Вам надо иметь как минимум: $' . ($planList[1]['contribution'] + self::WELCOME_DEPOSIT);
    }

    $level = 1;
    $qnt = (int)EmeraldUsers::find()->where(['id_ref' => self::$ref_id])->count();

    self::$this_level_up = self::getLevelLeftUp(self::$ref_id); // true - lvl up, false - none

    if (self::$this_level_up) self::addLevelUpReferer(self::$ref_id);

    $transaction = Yii::$app->db->beginTransaction();


    //Добавляем запись о столе юзера
    $tMain = new self();
    $tMain->id_user = self::$user_id;
    $tMain->id_ref = self::$ref_id;
    $tMain->level = 1;

    if (!$tMain->save()) {
        $transaction->rollBack();
        return 'что-то пошло не так [ERR001]';
    }
    //Снимаем с юзера деньги

    self::$user->balance -= $planList[1]['contribution'];
    self::$user->updateAttributes(['balance']);

    self::makeBalanceRecord(Balance::TYPE_E_ACTIVE, 1, self::$user_id, 0, $planList[1]['contribution'], 0, 'Emerald Health - Активация пользователя');


    //Help::dd(self::getLevels($ref_user->id));

    //Добавляем юзера к рефереру
    $eUser = new EmeraldUsers();
    $eUser->id_ref = self::$ref_id;
    $eUser->id_user = self::$user_id;
    if (!$eUser->save()) {
        $transaction->rollBack();
        return 'При добавление юзера к рефереру [ERR0010]';
    }

    $table = self::findOne(['id_ref' => self::$ref_id, 'level' => self::$this_level]);

    //Help::dd($table);
    //Help::dd($table);
    if (!$table) {
        $transaction->rollBack();
        return 'у реферера нету стола';
    }

//        $tUser = new EmeraldUsers();
//        $tUser->id_table = $table->id;
//        $tUser->id_ref = $ref_user->id;
//        $tUser->id_user = $userId;
//        if (!$tUser->save()) {
//            $transaction->rollBack();
//            return 'что-то пошло не так [ERR002]';
//        }


    self::$this_level = self::getLevel(self::$ref_id);
    //инициализация завершена - коммит
    $transaction->commit();


    //Практически основное в данной пирамиде - проверка уровня реферера, отчисления ему, левелап
    self::updateReferer(self::$ref_id, self::$this_level, self::$user_id);

    return true;
}