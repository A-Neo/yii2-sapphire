<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\caching\TagDependency;

/**
 * This is the model class for table "{{%activation}}".
 *
 * @property int        $id
 * @property int        $user_id
 * @property int        $clone
 * @property int        $table
 * @property int        $status
 * @property int        $start
 * @property int|null   $t1_left
 * @property int|null   $t1_right
 * @property int|null   $t2_left
 * @property int|null   $t2_right
 * @property int|null   $t3_left
 * @property int|null   $t3_right
 * @property int|null   $t4_left
 * @property int|null   $t4_right
 * @property int|null   $t5_left
 * @property int|null   $t5_right
 * @property int|null   $t6_left
 * @property int|null   $t6_right
 * @property int        $created_at
 * @property int        $updated_at
 *
 * @property User       $user
 * @property Activation $t1Left
 * @property Activation $t1Right
 * @property Activation $t2Left
 * @property Activation $t2Right
 * @property Activation $t3Left
 * @property Activation $t3Right
 * @property Activation $t4Left
 * @property Activation $t4Right
 * @property Activation $t5Left
 * @property Activation $t5Right
 * @property Activation $t6Left
 * @property Activation $t6Right
 */
class Activation extends \yii\db\ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE   = 1;
    const STATUS_CLOSED   = 2;

    public $buy = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '{{%activation}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['user_id'], 'required'],
            [['user_id', 'table', 'clone', 'start', 'status', 't1_left', 't1_right', 't2_left', 't2_right', 't3_left', 't3_right', 't4_left', 't4_right', 't5_left', 't5_right', 't6_left', 't6_right', 'created_at', 'updated_at'], 'integer'],
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
            't4_left'    => Yii::t('site', 'T 4 Left'),
            't4_right'   => Yii::t('site', 'T 4 Right'),
            't5_left'    => Yii::t('site', 'T 5 Left'),
            't5_right'   => Yii::t('site', 'T 5 Right'),
            't6_left'    => Yii::t('site', 'T 6 Left'),
            't6_right'   => Yii::t('site', 'T 6 Right'),
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

    public function getT5Left() {
        return $this->hasOne(self::class, ['id' => 't5_left']);
    }

    public function getT5Right() {
        return $this->hasOne(self::class, ['id' => 't5_right']);
    }

    public function getT6Left() {
        return $this->hasOne(self::class, ['id' => 't6_left']);
    }

    public function getT6Right() {
        return $this->hasOne(self::class, ['id' => 't6_right']);
    }

    public function getTop($n = null) {
        return self::find()->where(['status' => [self::STATUS_ACTIVE, self::STATUS_CLOSED]])
            ->andWhere(['OR',
                        ['t' . ($n ? $n : $this->table) . '_left' => $this->id],
                        ['t' . ($n ? $n : $this->table) . '_right' => $this->id]])
            /*->andWhere(['>=', 'table', $this->table])*/ ->one();
    }

    public function getStatusesList() {
        return [
            self::STATUS_INACTIVE => Yii::t('site', 'Inactive'),
            self::STATUS_ACTIVE   => Yii::t('site', 'Active'),
            self::STATUS_CLOSED   => Yii::t('site', 'Closed'),
        ];
    }

    public function isUsed($n) {
        return self::find()->where(['AND', ['status' => [self::STATUS_ACTIVE, self::STATUS_CLOSED]], ['OR', ["t{$n}_left" => $this->id], ["t{$n}_right" => $this->id]]])->exists();
    }

    public function checkTx($top) {
        $l = $top->{"t{$top->table}Left"};
        $r = $top->{"t{$top->table}Right"};
        $ll = $l ? $top->{"t{$top->table}Left"}->{"t{$top->table}Left"} : null;
        $lr = $l ? $top->{"t{$top->table}Left"}->{"t{$top->table}Right"} : null;
        $rl = $r ? $top->{"t{$top->table}Right"}->{"t{$top->table}Left"} : null;
        $rr = $r ? $top->{"t{$top->table}Right"}->{"t{$top->table}Right"} : null;
        $a = $ll ? [$ll] : [];
        $a = $lr ? array_merge($a, [$lr]) : $a;
        $a = $rl ? array_merge($a, [$rl]) : $a;
        $a = $rr ? array_merge($a, [$rr]) : $a;
        $c = count($a);
        $cAm = Yii::$app->settings->get('system', 'chargingAmount' . $top->table);
        $u = $top->user;
        if($c >= 1 && $top->table < 6){
            $b = Balance::find()->where(['type' => Balance::TYPE_CHARGING, 'table' => $top->table, 'to_activation_id' => $top->id])->one();
            if(!$b && $u){
                $b = new Balance();
                $b->setAttributes([
                    'type'               => Balance::TYPE_CHARGING,
                    'status'             => Balance::STATUS_ACTIVE,
                    'table'              => $top->table,
                    'from_activation_id' => $a[0]->id,
                    'from_user_id'       => $a[0]->user_id,
                    'to_user_id'         => $top->user_id,
                    'to_activation_id'   => $top->id,
                    'to_amount'          => $cAm,
                ]);
                $b->save();
            }
        }
        if($c > 1 || $top->table == 6){
            $bbq = Balance::find()->where(['type' => $top->table == 6 ? Balance::TYPE_CHARGING : Balance::TYPE_ACCUMULATION, 'table' => $top->table, 'to_activation_id' => $top->id]);
            if(!empty($b)){
                $bbq->andWhere(['not', ['id' => $b->id]]);
            }
            $bb = $bbq->indexBy('from_activation_id')->all();
            foreach($a as $i => $aa){
                if($i == 0 && $top->table < 6){
                    continue;
                }
                if(!isset($bb[$aa->id]) && $u){
                    $b = new Balance();
                    $b->setAttributes([
                        'type'               => $top->table == 6 ? Balance::TYPE_CHARGING : Balance::TYPE_ACCUMULATION,
                        'status'             => Balance::STATUS_ACTIVE,
                        'table'              => $top->table,
                        'from_activation_id' => $aa->id,
                        'from_user_id'       => $aa->user_id,
                        'to_user_id'         => $top->user_id,
                        'to_activation_id'   => $top->id,
                        'to_amount'          => $cAm,
                    ]);
                    $b->save();
                    if($c == 1 && $top->table == 6){
                        // Реинвест
                        $clone = Activation::find()->where(['user_id' => $top->user_id, 'table' => 1, 'status' => [Activation::STATUS_ACTIVE, Activation::STATUS_CLOSED]])->andWhere(['>', 'clone', 0])->count();
                        $clone++;
                        $cloneA = new Activation();
                        $cloneA->setAttributes([
                            'user_id' => $top->user_id,
                            'table'   => 1,
                            'status'  => 1,
                            'clone'   => $clone,
                            'start'   => 1,
                        ]);
                        $cloneA->save();
                        $clone++;
                        $cloneA = new Activation();
                        $cloneA->setAttributes([
                            'user_id' => $top->user_id,
                            'table'   => 2,
                            'status'  => 1,
                            'clone'   => $clone,
                            'start'   => 2,
                        ]);
                        $cloneA->save();
                        $clone++;
                        $cloneA = new Activation();
                        $cloneA->setAttributes([
                            'user_id' => $top->user_id,
                            'table'   => 3,
                            'status'  => 1,
                            'clone'   => $clone,
                            'start'   => 3,
                        ]);
                        $cloneA->save();
                    }
                    if($c == 4 && $top->table == 6){
                        $top->status = Activation::STATUS_CLOSED;
                        $top->save();
                        $b = new Balance();
                        $b->setAttributes([
                            'type'               => Balance::TYPE_CHARGING,
                            'status'             => Balance::STATUS_WAITING,
                            'table'              => $top->table,
                            'from_activation_id' => $top->id,
                            'from_user_id'       => $top->user_id,
                            'to_user_id'         => $top->user_id,
                            'to_activation_id'   => $top->id,
                            'to_sapphire'        => 3,
                        ]);
                        $b->save();
                        if($top->clone == 0){
                            $ref = $u->referrer;
                            if($ref && $ref->id <> $u->id){
                                $a = $ref->getActiveActivations()->andWhere(['clone' => 0])->one();
                                if($a){
                                    $b->setAttributes([
                                        'type'               => Balance::TYPE_CHARGING,
                                        'status'             => Balance::STATUS_WAITING,
                                        'table'              => $top->table,
                                        'from_activation_id' => $top->id,
                                        'from_user_id'       => $u->id,
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
        if($c == 4 && $top->table < 6){
            $top->table++;
            $top->save();
            if($u){
                $balance = new Balance();
                $balance->setAttributes([
                    'type'               => Balance::TYPE_PROMOTION,
                    'status'             => Balance::STATUS_ACTIVE,
                    'table'              => $top->table,
                    'from_activation_id' => $top->id,
                    'from_user_id'       => $top->user_id,
                    'from_amount'        => $pAm = Yii::$app->settings->get('system', 'promotionAmount' . $top->table),
                ]);
                $balance->save();
            }
        }
        $top->refresh();
    }

    public function checkSetAutoClone($activation) {
        if($activation->clone && !$activation->isUsed($activation->table)){
            $hasMain = Activation::find()->where(['status' => [Activation::STATUS_ACTIVE, Activation::STATUS_CLOSED], 'user_id' => $activation->user_id, 'clone' => 0])
                ->andWhere(['>=', 'table', $activation->table])->exists();
            if(!$hasMain){
                return;
            }
            $mains = Activation::find()->where(['status' => Activation::STATUS_ACTIVE, 'user_id' => $activation->user_id, 'table' => $activation->table])->orderBy(['clone' => SORT_ASC])->all();
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
        if($activation->isUsed($t)){
            return;
        }
        if(empty($aa)){
            if($activation->user && $activation->user->referrer && !($activation->user_id == 1 && $activation->clone == 0)){
                $aa = $activation->user->referrer->activeActivations;
            }
        }
        foreach($aa as $active){
            if($active->table < $activation->table || $active->start > $activation->table || $active->id == $activation->id){
                continue;
            }
            if(empty($active->{"t{$t}_left"})){
                $active->{"t{$t}_left"} = $activation->id;
                $active->save();
                break;
            }
            if(empty($active->{"t{$t}_right"})){
                $active->{"t{$t}_right"} = $activation->id;
                $active->save();
                break;
            }
            if($l = $active->{"t{$t}Left"}){
                if(empty($l->{"t{$t}_left"})){
                    $l->{"t{$t}_left"} = $activation->id;
                    $l->save();
                    break;
                }
                if(empty($l->{"t{$t}_right"})){
                    $l->{"t{$t}_right"} = $activation->id;
                    $l->save();
                    break;
                }
            }
            if($r = $active->{"t{$t}Right"}){
                if(empty($r->{"t{$t}_left"})){
                    $r->{"t{$t}_left"} = $activation->id;
                    $r->save();
                    break;
                }
                if(empty($r->{"t{$t}_right"})){
                    $r->{"t{$t}_right"} = $activation->id;
                    $r->save();
                    break;
                }
            }
        }
        $activation->refresh();
    }

    public function checkSetAuto($activation) {
        if($activation->status == Activation::STATUS_INACTIVE){
            return;
        }
        if($activation->clone > 0){
            $other = Activation::find()->where([
                'user_id' => $activation->user_id,
                'status'  => [Activation::STATUS_ACTIVE, Activation::STATUS_CLOSED],
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
            $aa = $t->activeActivations;
            if(empty($aa)){
                continue;
            }
            $item->checkSetAutoInternal($item, $i, $aa);
            $item->refresh();
            if($t->id == 1 && !$item->isUsed($i)){
                $item->createRootClone($i);
                $t->refresh();
                $aa = $t->activeActivations;
                $item->checkSetAutoInternal($item, $i, $aa);
                $item->refresh();
            }
            if(in_array($t->id, $loopCheck) || $item->isUsed($i)){
                break;
            }
        }
    }

    public function createRootClone($n = 1) {
        $activations = Activation::find()->where(['user_id' => 1, 'status' => [Activation::STATUS_ACTIVE, Activation::STATUS_CLOSED]])
            ->andWhere(['>=', 'table', $n])
            ->andWhere(['<=', 'start', $n])
            ->orderBy(['clone' => SORT_ASC])->all();
        foreach($activations as $activation){
            if($activation->hasPlace($n)){
                return true;
            }
        }
        $clone = Activation::find()->where(['user_id' => 1, 'status' => [Activation::STATUS_ACTIVE, Activation::STATUS_CLOSED]])->count();
        $cloneA = new Activation();
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
        if($l = $this->{"t{$t}Left"}){
            if(empty($l->{"t{$t}_left"})){
                return true;
            }
            if(empty($l->{"t{$t}_right"})){
                return true;
            }
        }
        if($r = $this->{"t{$t}Right"}){
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
        // если установили одну из t{1-6}_left или t{1-6}_right то пересчитать для того кого установили
        // а если просто сохранили то найти кому можно установить автоматом
        // 1 Проверить если начисления сюда
        $this->checkTx($this);
        $this->refresh();
        if(($top = $this->top) && ($top->id <> $this->id)){
            // 2 Проверить если начисления вышестоящему
            $this->checkTx($top);
            $this->refresh();
            $this->checkSetAuto($top);
            $this->refresh();
            if(($top = $this->top) && ($top->id <> $this->id)){
                // 3 Проверить если начисления вышестоящему на 2 уровня
                $this->checkTx($top);
                $this->refresh();
                $this->checkSetAuto($top);
                $this->refresh();
            }
        }
        //$this->checkSetAutoClone($this);
        $this->refresh();
        $this->checkSetAuto($this);
        $this->refresh();
        if($this->user){
            foreach($this->user->referrals as $referral){
                foreach($referral->activeActivations as $activation){
                    $this->checkSetAuto($activation);
                }
            }
        }
        $this->refresh();
        if($this->user_id != 1/* && $this->table == 1 */ && !$this->isUsed($this->table) && $this->user && $this->user->referrer_id == 1){
            $this->createRootClone($this->table);
            $this->checkSetAuto($this);
        }
        // Если нет вышестоящего проверить что можно установить
        // Проверить что можно установить личников пользователя сюда или ниже (если мы продвинулись а там были встоплисте)
        parent::afterSave($insert, $changedAttributes);
        TagDependency::invalidate(Yii::$app->cache, ['activations', 'activation-' . $this->id]);
    }

}
