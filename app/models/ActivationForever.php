<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\caching\TagDependency;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "{{%activation_forever}}".
 *
 * @property int        $id
 * @property int        $user_id
 * @property int        $clone
 * @property int        $table
 * @property int        $status
 * @property int        $start
 * @property int|null   $t1_left
 * @property int|null   $t1_midle
 * @property int|null   $t1_right
 * @property int|null   $t2_left
 * @property int|null   $t2_midle
 * @property int|null   $t2_right
 * @property int|null   $t3_left
 * @property int|null   $t3_midle
 * @property int|null   $t3_right
 * @property int        $created_at
 * @property int        $updated_at
 *
 * @property User       $user
 * @property ActivationForever $t1Left
 * @property ActivationForever $t1Midle
 * @property ActivationForever $t1Right
 * @property ActivationForever $t2Left
 * @property ActivationForever $t2Right
 * @property ActivationForever $t3Left
 * @property ActivationForever $t3Right
 */
class ActivationForever extends \yii\db\ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE   = 1;
    const STATUS_CLOSED   = 2;

    public $buy = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '{{%activation_forever}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['user_id'], 'required'],
            [['user_id', 'table', 'clone', 'start', 'status',
                't1_left', 't1_midle',
                't1_right', 't2_left', 't2_right',
                't4_right', 't4_left', 't4_midle',
                't3_left', 't3_right', 'created_at', 'updated_at'], 'integer'],
            [['clone'], 'default', 'value' => 0],
            [['start'], 'default', 'value' => 1],
            [['status'], 'default', 'value' => self::STATUS_INACTIVE],
            [['status'], 'in', 'range' => [self::STATUS_INACTIVE, self::STATUS_ACTIVE, self::STATUS_CLOSED]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id'         => Yii::t('site', 'ID'),
            'user_id'    => Yii::t('site', 'User'),
            'table'      => Yii::t('site', 'Table'),
            'status'     => Yii::t('site', 'Status'),
            'clone'      => Yii::t('site', 'Clone'),
            't1_left'    => Yii::t('site', 'T 1 Left'),
            't1_right'   => Yii::t('site', 'T 1 Right'),
            't2_left'    => Yii::t('site', 'T 2 Left'),
            't2_right'   => Yii::t('site', 'T 2 Right'),
            't3_left'    => Yii::t('site', 'T 3 Left'),
            't3_right'   => Yii::t('site', 'T 3 Right'),
            'created_at' => Yii::t('site', 'Created'),
            'updated_at' => Yii::t('site', 'Updated'),
        ];
    }

    public function behaviors() {
        return [
            TimestampBehavior::class,
        ];
    }

    public function getUser() {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getT1Left() {
        return $this->hasOne(self::class, ['id' => 't1_left']);
    }
    public function getT1Midle() {
        return $this->hasOne(self::class, ['id' => 't1_midle']);
    }

    public function getT2Midle() {
        return $this->hasOne(self::class, ['id' => 't2_midle']);
    }

    public function getT3Midle() {
        return $this->hasOne(self::class, ['id' => 't3_midle']);
    }

    public function getT4Midle() {
        return $this->hasOne(self::class, ['id' => 't4_midle']);
    }

    public function getT1Right() {
        return $this->hasOne(self::class, ['id' => 't1_right']);
    }

    public function getT2Left() {
        return $this->hasOne(self::class, ['id' => 't2_left']);
    }

    public function getT2Right() {
        return $this->hasOne(self::class, ['id' => 't2_right']);
    }

    public function getT3Left() {
        return $this->hasOne(self::class, ['id' => 't3_left']);
    }

    public function getT3Right() {
        return $this->hasOne(self::class, ['id' => 't3_right']);
    }

    public function getT4Left() {
        return $this->hasOne(self::class, ['id' => 't4_left']);
    }

    public function getT4Right() {
        return $this->hasOne(self::class, ['id' => 't4_right']);
    }



    public function getTop($n = null) {
//        $n = $n ? ($n - 10) : null;

        return self::find()->where('status IN (1,2) AND (
        t' . ($n ? $n : $this->table) . '_left = ' . $this->id . ' OR 
        t' . ($n ? $n : $this->table) . '_midle = ' . $this->id . ' OR 
        t' . ($n ? $n : $this->table) . '_right = ' . $this->id . ')')
            ->one();





//        return self::find()->where(['status' => [self::STATUS_ACTIVE, self::STATUS_CLOSED]])
//            ->andWhere(['OR',
//                        ['t' . ($n ? $n : $this->table) . '_left' => $this->id],
//                        ['t' . ($n ? $n : $this->table) . '_right' => $this->id]])
//            /*->andWhere(['>=', 'table', $this->table])*/ ->one();
    }

    public function getStatusesList() {
        return [
            self::STATUS_INACTIVE => Yii::t('site', 'Inactive'),
            self::STATUS_ACTIVE   => Yii::t('site', 'Active'),
            self::STATUS_CLOSED   => Yii::t('site', 'Closed'),
        ];
    }

    public function isUsed($n) {
//        $n = ($n > 10) ? ($n - 10) : $n;
        return self::find()->where(['AND', ['status' => [self::STATUS_ACTIVE, self::STATUS_CLOSED]], ['OR', ["t{$n}_left" => $this->id], ["t{$n}_right" => $this->id]]])->exists();
    }

    public function checkTx($top) {
//        VarDumper::dump($top, 5, true);die;
        $l = $top->{"t{$top->table}Left"};
        $m = $top->{"t{$top->table}Midle"};
        $r = $top->{"t{$top->table}Right"};

        $a = $l ? [$l] : [];// убрать топ
        $a = $m ? array_merge($a, [$m]) : $a;
        $a = $r ? array_merge($a, [$r]) : $a;
        $countOfReferals = count($a);
        $chargingAmount = Yii::$app->settings->get('system', 'chargingAmountForever' . $top->table);
        $user = $top->user;
        if($countOfReferals >= 1 && $top->table < 4){//если регистрируется дети родителям пишем camount
            $b = Balance::find()
                ->where(['type' => Balance::TYPE_ACCUMULATION,
                    'table' => 10 + $top->table, 'to_activation_id' => $top->id])->one();
            if(!$b && $user){
//                VarDumper::dump($b, 5, true);


                    $b = new Balance();
                    $b->setAttributes([//поменять на чаржинг
                        'type' => Balance::TYPE_ACCUMULATION,
                        'status' => Balance::STATUS_ACTIVE,
                        'table' => 10 + $top->table,
                        'from_activation_id' => $a[0]->id,
                        'from_user_id' => $a[0]->user_id,
                        'to_user_id' => $top->user_id,
                        'to_activation_id' => $top->id,
                        'to_amount' => $chargingAmount,
                    ]);
                    $b->save();

            }
        }



        if($countOfReferals > 1 || $top->table == 4 ){//если есть дети или 4 стол
            $bbq = Balance::find()
                ->where(['type' => $top->table == 4 ?
                    Balance::TYPE_CHARGING : Balance::TYPE_ACCUMULATION,
                    'table' => 10 + $top->table, 'to_activation_id' => $top->id]);
            if(!empty($b)){
                $bbq->andWhere(['not', ['id' => $b->id]]);
            }
            $bb = $bbq->indexBy('from_activation_id')->all();
            foreach($a as $i => $aa){
                if($i == 0 && $top->table < 4){
                    continue;
                }
                if(!isset($bb[$aa->id]) && $user){
                    $b = new Balance();
                    $b->setAttributes([
                        'type'               => $top->table == 4 ? Balance::TYPE_CHARGING
                            : Balance::TYPE_ACCUMULATION,
                        'status'             => Balance::STATUS_ACTIVE,
                        'table'              => 10 + $top->table,
                        'from_activation_id' => $aa->id,
                        'from_user_id'       => $aa->user_id,
                        'to_user_id'         => $top->user_id,
                        'to_activation_id'   => $top->id,
                        'to_amount'          => $chargingAmount,
                    ]);
                    $b->save();
                    if($countOfReferals == 1 && $top->table == 4){
                        // Реинвест
                        $clone = ActivationForever::find()
                            ->where(['user_id' => $top->user_id, 'table' => 1,
                                'status' => [ActivationForever::STATUS_ACTIVE,
                                    ActivationForever::STATUS_CLOSED]])
                            ->andWhere(['>', 'clone', 0])->count();
                        $clone++;
                        $cloneA = new ActivationForever();
                        $cloneA->setAttributes([
                            'user_id' => $top->user_id,
                            'table'   => 1,
                            'status'  => 1,
                            'clone'   => $clone,
                            'start'   => 1,
                        ]);
                        $cloneA->save();
                        $clone++;
                        $cloneA = new ActivationForever();
                        $cloneA->setAttributes([
                            'user_id' => $top->user_id,
                            'table'   => 2,
                            'status'  => 1,
                            'clone'   => $clone,
                            'start'   => 2,
                        ]);
                        $cloneA->save();
                        $clone++;
                        $cloneA = new ActivationForever();
                        $cloneA->setAttributes([
                            'user_id' => $top->user_id,
                            'table'   => 3,
                            'status'  => 1,
                            'clone'   => $clone,
                            'start'   => 3,
                        ]);
                        $cloneA->save();
                    }
                    if($countOfReferals == 4 && $top->table == 4){
                        $top->status = ActivationForever::STATUS_CLOSED;
                        $top->save();
                        $b = new Balance();
                        $b->setAttributes([
                            'type'               => Balance::TYPE_CHARGING,
                            'status'             => Balance::STATUS_WAITING,
                            'table'              => 10 + $top->table,
                            'from_activation_id' => $top->id,
                            'from_user_id'       => $top->user_id,
                            'to_user_id'         => $top->user_id,
                            'to_activation_id'   => $top->id,
                            'to_sapphire'        => 3,
                        ]);
                        $b->save();
                        if($top->clone == 0){
                            $ref = $user->referrer;
                            if($ref && $ref->id <> $user->id){
                                $a = $ref->getActiveActivationsForever()->andWhere(['clone' => 0])->one();
                                if($a){
                                    $b->setAttributes([
                                        'type'               => Balance::TYPE_CHARGING,
                                        'status'             => Balance::STATUS_WAITING,
                                        'table'              => 10 + $top->table,
                                        'from_activation_id' => $top->id,
                                        'from_user_id'       => $user->id,
                                        'to_user_id'         => $ref->id,
                                        'to_activation_id'   => $a->id,
                                        'to_sapphire'        => 3,
                                    ]);
                                    $b->save();
                                }
                            }
                        }
                    }
                }
            }
        }
        if($countOfReferals == 3 && $top->table < 4){
            $top->table++;
            $top->save();
            if($user){
                $balance = new Balance();
                $balance->setAttributes([
                    'type'               => Balance::TYPE_PROMOTION,
                    'status'             => Balance::STATUS_ACTIVE,
                    'table'              => 10 + $top->table,
                    'from_activation_id' => $top->id,
                    'from_user_id'       => $top->user_id,
                    'from_amount'        => $pAm = Yii::$app->settings->get('system', 'promotionAmountForever' . $top->table),
                ]);
                $balance->save();
            }
        }
        $top->refresh();
    }

    public function checkSetAutoClone($activation) {
        if($activation->clone && !$activation->isUsed($activation->table)){
            $hasMain = ActivationForever::find()->where(['status' => [ActivationForever::STATUS_ACTIVE, ActivationForever::STATUS_CLOSED], 'user_id' => $activation->user_id, 'clone' => 0])
                ->andWhere(['>=', 'table', $activation->table])->exists();
            if(!$hasMain){
                return;
            }
            $mains = ActivationForever::find()->where(['status' => ActivationForever::STATUS_ACTIVE, 'user_id' => $activation->user_id, 'table' => $activation->table])->orderBy(['clone' => SORT_ASC])->all();
            foreach($mains as $main){
                if($main->id == $activation->id
                    || ($main->top && $main->top->id == $activation->id)
                    || ($activation->top && $main->id == $activation->top->id)){
                    continue;
                }
                $this->checkSetAutoInternal($activation, $activation->table, [$main]);
                $activation->refresh();
                if($activation->isUsed($activation->table)){
                    break;
                }
            }
        }
    }

    public function checkSetAutoInternal($activation, $t, $aa = []) {
//        VarDumper::dump($aa, 5, true);die;

        if($activation->isUsed($t)){
            return;
        }
        if(empty($aa)){
            if($activation->user && $activation->user->referrer &&
                !($activation->user_id == 1 && $activation->clone == 0)){
                $aa = $activation->user->referrer->activeActivationsForever;

            }
        }
        foreach($aa as $active){
//            VarDumper::dump($active, 5, true);die;

            if($active->table < $activation->table ||
                $active->start > $activation->table ||
                $active->id == $activation->id){
                continue;
            }
            if(empty($active->{"t{$t}_left"})   ){
                $active->{"t{$t}_left"} = $activation->id;
                $active->save();
                break;
            }
            if(empty($active->{"t{$t}_midle"})){
                $active->{"t{$t}_midle"} = $activation->id;
                $active->save();
                break;
            }
            if(empty($active->{"t{$t}_right"})){
                if ($active->{"t{$t}_midle"} == $activation->id) break;
                $active->{"t{$t}_right"} = $activation->id;
                $active->save();
                break;
            }






//            if($l = $active->{"t{$t}Left"}){
//                if(empty($l->{"t{$t}_left"})){
//                    $l->{"t{$t}_left"} = $activation->id;
//                    $l->save();
//                    break;
//                }
//                if(empty($l->{"t{$t}_right"})){
//                    $l->{"t{$t}_right"} = $activation->id;
//                    $l->save();
//                    break;
//                }
//            }


//            if($r = $active->{"t{$t}Right"}){
//                if(empty($r->{"t{$t}_left"})){
//                    $r->{"t{$t}_left"} = $activation->id;
//                    $r->save();
//                    break;
//                }
//                if(empty($r->{"t{$t}_right"})){
//                    $r->{"t{$t}_right"} = $activation->id;
//                    $r->save();
//                    break;
//                }
//            }


        }
        $activation->refresh();
    }

    public function checkSetAuto($activation) {
        if($activation->status == ActivationForever::STATUS_INACTIVE){
            return;
        }
        if($activation->clone > 0){
            $other = ActivationForever::find()->where([
                'user_id' => $activation->user_id,
                'status'  => [ActivationForever::STATUS_ACTIVE, ActivationForever::STATUS_CLOSED],
            ])
                ->andWhere(['>=', 'table', $activation->table])
                ->andWhere(['<=', 'start', $activation->table])
                ->andWhere(['NOT', ['id' => $activation->id]])
                ->exists();
            if(!$other){
                $this->checkSetToSponsor($activation, $activation->table);
            }
            return;
        }
        for($i = 1; $i <= $activation->table; $i++){
//            VarDumper::dump($activation->table, 5, true);die;
            $this->checkSetAutoInternal($activation, $i);
        }
    }

    public function checkSetToSponsor($item, $i) {
        if($item->isUsed($i)){
            return;
        }
        $loopCheck = [];
        $t = $item->user;
        while($t){
            $loopCheck[] = $t->id;
            $t = $t->referrer;
            if(empty($t)){
                break;
            }
            $aa = $t->activeActivationsForever;
            if(empty($aa)){
                continue;
            }
            $item->checkSetAutoInternal($item, $i, $aa);
            $item->refresh();
            if($t->id == 1 && !$item->isUsed($i)){
                $item->createRootClone($i);
                $t->refresh();
                $aa = $t->activeActivationsForever;
                $item->checkSetAutoInternal($item, $i, $aa);
                $item->refresh();
            }
            if(in_array($t->id, $loopCheck) || $item->isUsed($i)){
                break;
            }
        }
    }

    public function createRootClone($n = 1) {
        $activations = ActivationForever::find()->where(['user_id' => 1, 'status' => [ActivationForever::STATUS_ACTIVE, ActivationForever::STATUS_CLOSED]])
            ->andWhere(['>=', 'table', $n])
            ->andWhere(['<=', 'start', $n])
            ->orderBy(['clone' => SORT_ASC])->all();
        foreach($activations as $activation){
            if($activation->hasPlace($n)){
                return true;
            }
        }
        $clone = ActivationForever::find()->where(['user_id' => 1, 'status' => [ActivationForever::STATUS_ACTIVE, ActivationForever::STATUS_CLOSED]])->count();
        $cloneA = new ActivationForever();
        $cloneA->setAttributes([
            'user_id' => 1,
            'table'   => $n,
            'status'  => 1,
            'clone'   => $clone,
            'start'   => 1,
        ]);
        $cloneA->save();
    }

    public function hasPlace($t = 1) {
        if(empty($this->{"t{$t}_left"})){
            return true;
        }
        if(empty($this->{"t{$t}_right"})){
            return true;
        }
        if($l = $this->{"t1{$t}Left"}){
            if(empty($l->{"t{$t}_left"})){
                return true;
            }
            if(empty($l->{"t{$t}_right"})){
                return true;
            }
        }
        if($r = $this->{"t1{$t}Right"}){
            if(empty($r->{"t{$t}_left"})){
                return true;
            }
            if(empty($r->{"t{$t}_right"})){
                return true;
            }
        }
        return false;
    }

    public function afterSave($insert, $changedAttributes) {
        @set_time_limit(0);
        if(Yii::$app->session->get('disableAutoSet', false)){
            parent::afterSave($insert, $changedAttributes);
            TagDependency::invalidate(Yii::$app->cache, ['activations', 'activation-' . $this->id]);
            return;
        }
        // если установили одну из t{1-6}_left или t{1-6}_right то пересчитать
        // для того кого установили
        // а если просто сохранили то найти кому можно установить автоматом
        // 1 Проверить если начисления сюда
        $this->checkTx($this);//
        $this->refresh();//закоментить
        if(($top = $this->top) && ($top->id <> $this->id)){
            // 2 Проверить если начисления вышестоящему
            $this->checkTx($top);
            $this->refresh();
            $this->checkSetAuto($top);
            $this->refresh();
            $this->check2TableFirst($top);
            if ($top->top) $this->check2TableFirst($top->top);

            $this->check3TableFirst($top);
            if ($top->top) $this->check3TableFirst($top->top);

            $this->check4TableFirst($top);
            if ($top->top) $this->check4TableFirst($top->top);


//            if(($top = $this->top) && ($top->id <> $this->id)){
//                // 3 Проверить если начисления вышестоящему на 2 уровня
//                $this->checkTx($top);
//                $this->refresh();
//                $this->checkSetAuto($top);
//                $this->refresh();
//            }
        }
        //$this->checkSetAutoClone($this);
        $this->refresh();
        $this->checkSetAuto($this);
        $this->check2TableFirst($this);
        if (($top1 = $this->top) && ($top1->id <> $this->id)) {
            $this->check2TableFirst($top1->top);
            if ($top1->top)   $this->check2TableFirst($top1->top);
        }

        $this->check3TableFirst($this);
        if (($top1 = $this->top) && ($top1->id <> $this->id)) {
            $this->check3TableFirst($top1->top);
            if ($top1->top)   $this->check3TableFirst($top1->top);
        }

        $this->check4TableFirst($this);
        if (($top1 = $this->top) && ($top1->id <> $this->id)) {
            $this->check4TableFirst($top1->top);
            if ($top1->top)   $this->check4TableFirst($top1->top);
        }

        $this->refresh();
        if($this->user){
            foreach($this->user->referrals as $referral){
                foreach($referral->activeActivationsForever as $activation){
                    $this->checkSetAuto($activation);
                }
            }
        }
        $this->refresh();
        if($this->user_id != 1/* && $this->table == 1 */
            && !$this->isUsed($this->table)
            && $this->user && $this->user->referrer_id == 1){
        //            $this->createRootClone($this->table);
        //            $this->checkSetAuto($this);
        //        }
        //        // Если нет вышестоящего проверить что можно установить
        //        // Проверить что можно установить личников пользователя сюда или ниже
        //        // (если мы продвинулись а там были встоплисте)
        parent::afterSave($insert, $changedAttributes);
        TagDependency::invalidate(Yii::$app->cache, ['activations', 'activation-' . $this->id]);
    }




    }

    public function check2TableFirst($top){

        if (!$top) return;

        if ($top->table == 2 && $top->t2Left && !$top->t2Midle) {

            $balanceAccumulation = Balance::find()
                ->where(['type' => Balance::TYPE_ACCUMULATION,
                    'table' => 12,
                    'to_activation_id' => $top->id,
                    'from_activation_id' => $top->t2Left->id
                ])->one();

            if ($balanceAccumulation) {
                $balanceAccumulation->to_amount = Yii::$app->settings->get('system', 'chargingFirstAmountForever2');
                $balanceAccumulation->save();
            }

            $balanceCharging = Balance::find()
                ->where(['type' => Balance::TYPE_CHARGING,
                    'table' => 12,
                    'to_activation_id' => $top->id,
                    'from_activation_id' => $top->t2Left->id
                ])->one();

            if ($balanceCharging) {
                $balanceCharging->to_amount =
                    Yii::$app->settings->get('system', 'chargingAmountForever2') - Yii::$app->settings->get('system', 'chargingFirstAmountForever2');
//                VarDumper::dump($balanceCharging, 5, true);die;
                $balanceCharging->save();
            }
            else {
                $balanceCharging = new Balance();
                $balanceCharging->setAttributes([//поменять на чаржинг
                    'type' => Balance::TYPE_CHARGING,
                    'status' => Balance::STATUS_ACTIVE,
                    'table' => 12,
                    'from_activation_id' => $top->t2Left->id,
                    'from_user_id' => $top->t2Left->user_id,
                    'to_user_id' => $top->user_id,
                    'to_activation_id' => $top->id,
                    'to_amount' => Yii::$app->settings->get('system', 'chargingAmountForever2') - Yii::$app->settings->get('system', 'chargingFirstAmountForever2')
                ]);
                $balanceCharging->save();



            }


        }
        return;
        }

    public function check3TableFirst($top){

        if (!$top) return;

        if ($top->table == 3 && $top->t3Left && !$top->t3Midle) {

            $balanceAccumulation = Balance::find()
                ->where(['type' => Balance::TYPE_ACCUMULATION,
                    'table' => 13,
                    'to_activation_id' => $top->id,
                    'from_activation_id' => $top->t3Left->id
                ])->one();

            if ($balanceAccumulation) {
                $balanceAccumulation->to_amount = Yii::$app->settings->get('system', 'chargingFirstAmountForever3');
                $balanceAccumulation->save();




            }

            $balanceCharging = Balance::find()
                ->where(['type' => Balance::TYPE_CHARGING,
                    'table' => 13,
                    'to_activation_id' => $top->id,
                    'from_activation_id' => $top->t3Left->id
                ])->one();

            if ($balanceCharging) {
                $balanceCharging->to_amount =
                    Yii::$app->settings->get('system', 'chargingAmountForever3') - Yii::$app->settings->get('system', 'chargingFirstAmountForever3');
//                VarDumper::dump($balanceCharging, 5, true);die;
                $balanceCharging->save();
            }
            else {
                $balanceCharging = new Balance();
                $balanceCharging->setAttributes([//поменять на чаржинг
                    'type' => Balance::TYPE_CHARGING,
                    'status' => Balance::STATUS_ACTIVE,
                    'table' => 13,
                    'from_activation_id' => $top->t3Left->id,
                    'from_user_id' => $top->t3Left->user_id,
                    'to_user_id' => $top->user_id,
                    'to_activation_id' => $top->id,
                    'to_amount' => (Yii::$app->settings->get('system', 'chargingAmountForever3') - Yii::$app->settings->get('system', 'chargingFirstAmountForever3') - 12)
                ]);
                $balanceCharging->save();

                $balanceAccumulationSapphire = new Balance();
                $balanceAccumulationSapphire->setAttributes([//поменять на чаржинг
                    'type' => Balance::TYPE_ACCUMULATION,
                    'status' => Balance::STATUS_ACTIVE,
                    'table' => 13,
                    'from_activation_id' => $top->t3Left->id,
                    'from_user_id' => $top->t3Left->user_id,
                    'to_user_id' => $top->user_id,
                    'to_activation_id' => $top->id,
                    'to_amount' => 12,
                    'comment' => 'sapphireActivation',
                ]);
                $balanceAccumulationSapphire->save();



            }


        }
        return;
        }

    public function check4TableFirst($top){

        if (!$top) return;

        if ($top->table == 4 && $top->t4Left && !$top->t4Midle) {

            $balanceAccumulation = Balance::find()
                ->where(['type' => Balance::TYPE_ACCUMULATION,
                    'table' => 14,
                    'to_activation_id' => $top->id,
                    'from_activation_id' => $top->t4Left->id
                ])->one();

            if ($balanceAccumulation) {
                $balanceAccumulation->to_amount = Yii::$app->settings->get('system', 'chargingFirstAmountForever4');
                $balanceAccumulation->save();
            }

            $balanceCharging = Balance::find()
                ->where(['type' => Balance::TYPE_CHARGING,
                    'table' => 14,
                    'to_activation_id' => $top->id,
                    'from_activation_id' => $top->t4Left->id
                ])->one();

            if ($balanceCharging) {
                $balanceCharging->to_amount =
                    Yii::$app->settings->get('system', 'chargingAmountForever4') - Yii::$app->settings->get('system', 'chargingFirstAmountForever4');
//                VarDumper::dump($balanceCharging, 5, true);die;
                $balanceCharging->save();
            }
            else {
                $balanceCharging = new Balance();
                $balanceCharging->setAttributes([//поменять на чаржинг
                    'type' => Balance::TYPE_CHARGING,
                    'status' => Balance::STATUS_ACTIVE,
                    'table' => 14,
                    'from_activation_id' => $top->t4Left->id,
                    'from_user_id' => $top->t4Left->user_id,
                    'to_user_id' => $top->user_id,
                    'to_activation_id' => $top->id,
                    'to_amount' => Yii::$app->settings->get('system', 'chargingAmountForever4') - Yii::$app->settings->get('system', 'chargingFirstAmountForever4')
                ]);
                $balanceCharging->save();



            }


        }
        return;
    }












}
