<?php

namespace app\commands;

use app\components\Api;
use app\models\Activation;
use app\models\TravelMain;
use app\models\User;
use Yii;
use yii\console\controllers\HelpController;

class CronController extends \yii\console\Controller
{

    public $defaultAction = 'help';

    /**
     * Help message
     */
    public function actionHelp($command = null) {
        $help = new HelpController('help', $this->module);
        $help->actionIndex('cron' . ($command ? '/' . $command : ''));
    }

    /**
     * Cron command run all cron checking at once
     */
    public function actionCheck() {
        $this->actionCheckStopLists();
        $this->actionCheckInactiveUsers();
    }

    public function actionUser($login, $password) {
        $user = User::findByUsername($login, null);
        $user->setPassword($password);
        $user->save();
    }

    public function actionCheckInactiveUsers() {
        $timeout = time() - 3600 * 24 * 7;
        $ids = User::find()->alias('u')
            ->joinWith(['balancesFrom' => function ($query) {
                $query->alias('bf');
            }], false, 'LEFT JOIN')
            ->joinWith(['balancesTo' => function ($query) {
                $query->alias('bt');
            }], false, 'LEFT JOIN')
            ->joinWith(['activations' => function ($query) {
                $query->alias('a')->andOnCondition(['a.status' => [Activation::STATUS_ACTIVE, Activation::STATUS_CLOSED]]);
            }], false, 'LEFT JOIN')
            ->where(['a.id' => null, 'bf.id' => null, 'bt.id' => null, 'u.balance' => 0])
            ->andWhere(['<', 'u.created_at', $timeout])
            ->select('u.id')
            ->limit(100)
            ->column();
        Activation::deleteAll(['user_id' => $ids]);
        User::deleteAll(['id' => $ids]);
    }

    /**
     * Cron command check timeouted stoplist position and set it automatic in structure on in sponsor structure
     */
    public function actionCheckStopLists() {
        $timeout = time() - 3600 * 24 * 3;
        for($i = 1; $i <= 6; $i++){
            $items = Activation::find()->alias('t')->select('t.*')
                ->leftJoin(Activation::tableName() . ' as t1', "t1.id=t.t{$i}_left OR t1.id=t.t{$i}_right")
                ->andWhere(['>=', 't.table', $i])
                ->andWhere(['<=', 't.start', $i])
                ->andWhere(['t1.id' => null])
                ->andWhere(['<', 't.updated_at', $timeout])->andWhere(['>', 't.id', 1])->all();
            foreach($items as $item){
                $item->checkSetAutoInternal($item, $i);
                $item->checkSetToSponsor($item, $i);
            }
        }
    }

    /**
     * Проверка отложенных переходов на стол выше для структуры Travel
     */
    public function actionCheckDelayTravel()
    {
        TravelMain::checkDelayUsers();
        echo "\nDelay success\n\n";
    }

    public function actionTest1($user) {
        $user = User::findByUsername($user);
        if(!$user){
            echo "User $user not found\n";
            exit;
        }
        $a = $user->getActiveActivations()->andWhere(['clone' => 0])->one();
        if($a && $a->table < 6){
            $a->table++;
            $a->save();
        }
    }

    public function actionTest($referrer = null) {
        $ref = 1;
        if($referrer == 'all'){
            $names = Yii::$app->db->createCommand('SELECT username FROM `user` WHERE id NOT IN(SELECT referrer_id FROM `user` GROUP BY referrer_id HAVING count(id)>1)')->queryColumn();
            foreach($names as $name){
                echo $name . "\n";
                $this->actionTest($name);
                $this->actionTest($name);
            }
            return;
        }
        if($referrer){
            $mod = User::findByUsername($referrer);
            if($mod){
                $ref = $mod->id;
            }else{
                echo "User $referrer not found\n";
                exit;
            }
        }
        $c = User::find()->where(['like', 'username', 'test%'])->count() + 1;
        $username = 'test' . $c;
        while(User::findByUsername($username, null)){
            $c++;
            $username = 'test' . $c;
        }
        $user = new User();
        $user->setAttributes([
            'referrer_id' => $ref,
            'status'      => 1,
            'username'    => $username,
            'email'       => $username . '@test.com',
        ]);
        $user->generateAuthKey();
        $user->setPassword('31415926');
        if(!$user->save()){
            var_dump($user->getErrors());
            exit;
        }
        $activation = new Activation();
        $activation->setAttributes([
            'status'  => 1,
            'user_id' => $user->id,
            'table'   => 1,
        ]);
        if(!$activation->save()){
            var_dump($activation->getErrors());
            exit;
        }
    }

    public function actionHistory() {
        Api::updateHistory(true);
    }
}