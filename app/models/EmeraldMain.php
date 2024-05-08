<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;

use app\components\emerald\BaseTrait;

use app\helpers\FunctionHelper as Help;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "emerald_main".
 *
 * @property int $id
 * @property int $id_user
 * @property int $level
 * @property int $created_at
 * @property int $updated_at
 */

class EmeraldMain extends \yii\db\ActiveRecord
{
    use BaseTrait;

    const NAME            = 'emerald';
    const PROJECT_NAME    = 'Emerald Health';

    const STATUS_UNPAID   = 1;
    const STATUS_ACTIVE   = 2;
    const STATUS_CLOSED   = 3;

    const CONTRIBUTION    = 50;

    const MAX_LEVEL       = 5;
    const MAX_USERS       = 4;

    public static $user;
    public static $reffer;

    const PLAN_LIST = [
        1 => [
            'payment' => 10,
        ],
        2 => [
            'payment' => 20,
        ],
        3 => [
            'payment'       => 40,
            'passive'       => 20,
            'slot'          => [
                1 => ['month'  => 1],
                2 => ['month'  => 1],
                3 => ['month'  => 1],
                4 => ['month'  => 2],
                5 => ['month'  => 10], // По заполнению всего уровня
            ],
        ],
        4 => [
            'payment'       => 75,
            'passive'       => 50,
            'slot'          => [
                1 => ['month'       => 2],
                2 => ['month'       => 2],
                3 => ['month'       => 3],
                4 => ['month'       => 3],
                5 => ['tour_name'   => 5], // Тур в Анталию
            ],
        ],
        5 => [
            'payment'       => 200,
            'passive'       => 300,
            'slot'          => [
                1 => ['month'       => 10],
                2 => ['month'       => 10],
                3 => ['month'       => 15],
                4 => ['month'       => 15],
                5 => ['complete'    => [
                    'tour'  => '4 all tour',
                    'phone' => 'Samsung S24'
                ]],
            ]
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'emerald_main';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_user', 'level', 'created_at', 'updated_at'], 'integer'],
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

    public static function initUser($id_user, $id_referal)
    {
        // Start New
        self::$user     = User::findOne(['id' => $id_user]);
        self::$reffer   = $id_referal;

        if (!self::$user) return 'Пользователь не найден';
        if (!self::$reffer) return 'Пользователь по реф id не найден';

        if (self::checkIsActive(self::$user->id)) return 'Уровень пользователя уже активирован';
        if (!self::checkIsActive(self::$reffer->id)) return 'Ваш реф. пользователь не активировал проект';

        if (!self::checkIsBalance()) return 'Недостаточно средств на балансе! Вам надо иметь как минимум: $' . self::CONTRIBUTION;

        $transaction = Yii::$app->db->beginTransaction();

        self::$user->id_ref_emerald = self::$reffer->id;
        self::$user->updateAttributes(['id_ref_emerald']);

        // Создаем новую запись в проекте (таблице)
        $this_model = new self();
        $this_model->id_user = self::$user->id;
        $this_model->status = self::STATUS_ACTIVE;
        $this_model->level = 1;

        if (!$this_model->save()) {
            $transaction->rollBack();
            return 'Не удалось сохранить EmeraldMain [ERR001]';
        }

        //Снимаем с юзера деньги

        self::$user->balance -= (double) self::CONTRIBUTION;
        self::$user->updateAttributes(['balance']);

        self::makeBalanceRecord(Balance::TYPE_E_ACTIVE, 1, self::$user->id, 0, (double) self::CONTRIBUTION, 0, 'Активация пользователя');


        $model_em = self::find()->where(['id_user' => self::$reffer->id])->orderBy(['level' => SORT_DESC])->one();
        $table = $model_em->id;


        //Добавляем юзера к рефереру
        $model_user             = new EmeraldUsers();
        $model_user->id_ref     = self::$reffer->id;
        $model_user->id_user    = self::$user->id;
        $model_user->id_table   = $table;

        if (!$model_user->save()) {
            $transaction->rollBack();
            return 'Не удалось добавить пользователя к реферу EmeraldUsers [ERR0010]';
        }

        $transaction->commit();

        // Практически основное в данной пирамиде - проверка уровня реферера, отчисления ему, левелап
        self::updateReferer($model_em->level);

        return true;
    }


    public static function checkDelayUsers()
    {
        //Проверка отложенных юзеров - проверяем всех
        $delayUsers = EmeraldDelay::find()->all();

        foreach($delayUsers as $delUser) {
            /** @var $delUser EmeraldDelay */
            //Проверяем наличие стола у реферера
            $refRefTable = self::findOne(['id_user' => $delUser->id_ref, 'level' => $delUser->level]);

            //Стол есть - выполняем апдейт и удаляем запись об отложенном юзере
            if ($refRefTable) {
                $tUser = new EmeraldUsers();
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
                        $tUser = new EmeraldUsers();
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
