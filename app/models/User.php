<?php

namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\caching\TagDependency;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property int          $id
 * @property string       $username
 * @property string       $auth_key
 * @property string       $password_hash
 * @property string|null  $password_reset_token
 * @property string|null  $password
 * @property string|null  $fin_password
 * @property string|null  $fin_password_t
 * @property string       $email
 * @property string|null  $verification_token
 * @property string       $role
 * @property array[]      $permissions
 * @property int          $referrer_id
 * @property int          $telegram_chat_id
 * @property string       $wallet
 * @property string       $wallet_perfect
 * @property string       $wallet_tether
 * @property string       $wallet_banki_rf
 * @property string       $wallet_dc
 * @property int          $sapphire
 * @property int          $sapphire_personal
 * @property int          $sapphire_partners
 * @property double       $balance
 * @property double       $balance_travel
 * @property double       $accumulation
 * @property string       $full_name
 * @property string       $country
 * @property string       $phone
 * @property string       $birth_date
 * @property string       $avatar
 * @property int          $status
 * @property int          $created_at
 * @property int          $updated_at
 *
 * @property string       $referrer_name
 * @property User         $referrer
 * @property User[]       $referrals
 * @property Activation[] $activeActivations
 * @property ActivationForever[] $activeActivationsForever
 *
 * @property Balance[]    $balancesFrom
 * @property Balance[]    $balancesTo
 * @property Tour[]       $tours
 *
 * @property int          $partnersCount
 * @property int          $structuresCount
 */
class User extends ActiveRecord implements IdentityInterface
{

    const PROMOTE_MANUAL     = 0;
    const PROMOTE_AUTOMATIC  = 1;
    const PROMOTE_MANUAL_RUN = 2;

    const STATUS_BLOCKED  = -1;
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE   = 1;
    const ROLE_USER       = 'user';
    const ROLE_MODERATOR  = 'moderator';
    const ROLE_ADMIN      = 'admin';

    public $password       = null;
    public $fin_password_t = null;
    public $referrer_name  = null;

    //const PROMOTE_MANUAL     = 0;
    public static $amount = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['username', 'auth_key', 'password_hash', 'email'], 'required'],
            [['status', 'created_at', 'updated_at', 'referrer_id', 'sapphire', 'sapphire_personal', 'sapphire_partners'], 'integer'],
            [['status', 'created_at', 'updated_at', 'referrer_id'], 'filter', 'filter' => [self::class, 'filterInt']],
            [['balance', 'accumulation', 'balance_travel', 'balance_emerald', 'id_ref_emerald'], 'number'],
            [['status'], 'default', 'value' => self::STATUS_INACTIVE],
            [['status'], 'in', 'range' => [self::STATUS_BLOCKED, self::STATUS_ACTIVE, self::STATUS_INACTIVE]],
            [['username', 'referrer_name', 'password', 'password_hash', 'password_reset_token', 'fin_password', 'fin_password_t', 'email', 'wallet', 'wallet_perfect', 'wallet_tether', 'wallet_banki_rf', 'wallet_dc', 'phone', 'country', 'verification_token'], 'string', 'max' => 255],
            [['birth_date'], 'string'],
            [['auth_key', 'role'], 'string', 'max' => 32],
            [['full_name'], 'string', 'max' => 1024],
            [['role'], 'default', 'value' => self::ROLE_USER],
            [['role'], 'in', 'range' => [self::ROLE_USER, self::ROLE_MODERATOR, self::ROLE_ADMIN]],
            [['username'], 'unique'],
            [['email'], 'email'],
            [['wallet'], 'match', 'pattern' => '#[P|U][0-9]+#sm'],
            [['password_reset_token'], 'unique'],
            [['permissions', 'avatar'], 'safe'],
            [['permissions'], 'default', 'value' => []],
            [['referrer_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['referrer_id' => 'id']],
        ];
    }

    public static function filterInt($val) {
        if(is_null($val)){
            return $val;
        }
        return intval($val);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id'                   => Yii::t('site', 'ID'),
            'username'             => Yii::t('site', 'Username'),
            'auth_key'             => Yii::t('site', 'Auth key'),
            'password'             => Yii::t('site', 'Password'),
            'fin_password'         => Yii::t('site', 'Financial password'),
            'fin_password_t'       => Yii::t('site', 'Financial password'),
            'password_hash'        => Yii::t('site', 'Password hash'),
            'password_reset_token' => Yii::t('site', 'Password reset token'),
            'email'                => Yii::t('site', 'Email'),
            'verification_token'   => Yii::t('site', 'Verification token'),
            'role'                 => Yii::t('site', 'Role'),
            'permissions'          => Yii::t('site', 'Permissions'),
            'referrer_id'          => Yii::t('site', 'Referrer'),
            'referrer'             => Yii::t('site', 'Referrer'),
            'referrer_name'        => Yii::t('site', 'Referrer'),
            'wallet'               => Yii::t('site', 'Wallet'),
            'wallet_perfect'       => Yii::t('site', 'Wallet Perfect'),
            'wallet_tether'        => Yii::t('site', 'Wallet Tether'),
            'wallet_banki_rf'      => Yii::t('site', 'Wallet Banki RF'),
            'wallet_dc'            => Yii::t('site', 'Wallet DC'),
            'sapphire'             => Yii::t('site', 'Sapphire'),
            'balance'              => Yii::t('site', 'Balance'),
            'balance_emerald'      => 'Balance - Emerald Health',
            'balance_travel'       => Yii::t('site', 'Balance ST'),
            'accumulation'         => Yii::t('site', 'Accumulation'),
            'full_name'            => Yii::t('site', 'Full name'),
            'country'              => Yii::t('site', 'Country'),
            'phone'                => Yii::t('site', 'Phone'),
            'birth_date'           => Yii::t('site', 'Birth date'),
            'avatar'               => Yii::t('site', 'Avatar'),
            'status'               => Yii::t('site', 'Status'),
            'created_at'           => Yii::t('site', 'Created'),
            'updated_at'           => Yii::t('site', 'Updated'),
            'partnersCount'        => Yii::t('site', 'Partners'),
            'structuresCount'      => Yii::t('site', 'Structure'),
        ];
    }

    public function behaviors() {
        return [
            TimestampBehavior::class,
        ];
    }

    public function beforeValidate() {
        if(Yii::$app->has('user')):
            if(// Нельзя менять статус/роль и права  в таких случаяъ
                $this->id == Yii::$app->user->id // Самому себе
                || $this->role == self::ROLE_ADMIN && Yii::$app->user->identity && Yii::$app->user->identity->role == self::ROLE_MODERATOR // Админу если ты модератор
                || Yii::$app->user->identity == self::ROLE_USER // если ты пользователь (как ты вообще в админку попал :-)
            ){
                $oldStatus = $this->getOldAttribute('status');
                $oldStatus = is_null($oldStatus) ? $this->status : $oldStatus;
                if(Yii::$app->user->identity && Yii::$app->user->identity->role == self::ROLE_ADMIN
                    || (Yii::$app->user->identity && Yii::$app->user->identity->role == self::ROLE_MODERATOR && $this->role == self::ROLE_MODERATOR)){
                    $this->status = (in_array($this->status, [self::STATUS_ACTIVE]) && in_array($oldStatus, [self::STATUS_ACTIVE])) ? $this->status : $oldStatus;
                }else{
                    $this->status = $oldStatus;
                }
                $this->role = $this->getOldAttribute('role') ?: $this->role;
                $this->permissions = $this->getOldAttribute('permissions') ?: $this->permissions;
            }
        endif;
        if(empty($this->referrer_id) && get_called_class() == User::class){
            $this->referrer_id = 1;
        }

        return parent::beforeValidate();
    }

    public function beforeSave($insert) {
        if(is_array($this->permissions)){
            $this->permissions = json_encode($this->permissions);
        }
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function afterFind() {
        if(!is_array($this->permissions)){
            $this->permissions = json_decode($this->permissions, true);
        }
        $this->checkBalance();
        parent::afterFind();
    }

    public function checkBalance() {
        $id = $this->id;
        $balances = Balance::find()->where(['to_user_id' => $id])
            ->orWhere(['from_user_id' => $id])
            ->andWhere(['status' => Balance::STATUS_ACTIVE])
            ->asArray()->all();
        $b = 0;
        $a = 0;
        $s = 0;
        $sp = 0;
        $spp = 0;
        foreach($balances as $i => $balance){
            unset($balances[$i]['table']);
            unset($balances[$i]['status']);
            unset($balances[$i]['created_at']);
            unset($balances[$i]['updated_at']);
            unset($balances[$i]['history_id']);
            unset($balances[$i]['from_activation_id']);
            unset($balances[$i]['to_activation_id']);
            if($balance['from_user_id'] == $id && $balance['from_sapphire']){
                $s -= $balance['from_sapphire'];
                $sp -= $balance['from_sapphire'] > 3 ? 3 : $balance['from_sapphire'];
                $spp -= $balance['from_sapphire'] > 3 ? $balance['from_sapphire'] - 3 : 0;
            }
            if($balance['to_user_id'] == $id && $balance['to_sapphire']){
                $s += $balance['to_sapphire'];
                if($balance['from_user_id'] == $id){
                    $sp += $balance['to_sapphire'];
                }else{
                    $spp += $balance['to_sapphire'];
                }
            }
            if($balance['from_user_id'] == $id && $balance['from_amount']){
                $b -= $balance['from_amount'];
                if($balance['type'] == Balance::TYPE_PROMOTION){
                    $a -= $balance['from_amount'];
                }
            }
            if($balance['to_user_id'] == $id && $balance['to_amount']){
                $b += $balance['to_amount'];
                if($balance['type'] == Balance::TYPE_ACCUMULATION){
                    $a += $balance['to_amount'];
                }
            }
        }
        if($this->balance != $b || $this->accumulation != $a
            || $this->sapphire != $s || $this->sapphire_personal != $sp || $this->sapphire_partners != $spp){
            $this->setAttributes([
                'balance'           => $b,
                'accumulation'      => $a,
                'sapphire'          => $s,
                'sapphire_personal' => $sp,
                'sapphire_partners' => $spp,
            ]);
            $this->save(false, ['balance', 'accumulation', 'sapphire', 'sapphire_personal', 'sapphire_partners', 'updated_at']);
        }
    }

    public function afterSave($insert, $changedAttributes) {
        if(!is_array($this->permissions)){
            $this->permissions = json_decode($this->permissions, true);
        }
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
        TagDependency::invalidate(Yii::$app->cache, ['users', 'user-' . $this->id]);
        if($this->referrer_name && $this->id != 1 && (empty($this->referrer) || $this->referrer_name != $this->referrer->username)){
            $ref = self::findByUsername($this->referrer_name);
            if(empty($ref)){
                Yii::$app->session->addFlash('error', Yii::t('admin', 'Referrer not found'));
                return;
            }
            /*
            if(!$ref->isActive()){
                Yii::$app->session->addFlash('error', Yii::t('admin', 'Referrer not activated'));
                return;
            }
            */
            if($ref->id == $this->id){
                Yii::$app->session->addFlash('error', Yii::t('admin', 'Your can not change referrer same user'));
                return;
            }
            $r = $ref;
            while($r->referrer && $r->id != 1){
                $r = $r->referrer;
                if($r->id == $this->id){
                    Yii::$app->session->addFlash('error', Yii::t('admin', 'Your can not change referrer same user'));
                    return;
                }
            }
            Yii::$app->session->addFlash('success', Yii::t('admin', 'Referrer successfully changed'));
            $this->updateAttributes(['referrer_id' => $ref->id]);
        }
    }

    /**
     * Вернуть все записи уровней пользователя
     *
     * @return ActiveQuery
     */
    public function getEmeraldMain()
    {
        return $this->hasMany(EmeraldMain::class, ['id_ref' => 'id']);
    }

    /**
     * Вернуть моих приглашенных пользователей
     *
     * @return ActiveQuery
     */
    public function getEmeraldUser()
    {
        return $this->hasMany(EmeraldUsers::class, ['id_ref' => 'id']);
    }

    /**
     * Вернуть моих отложенных пользователей
     *
     * @return ActiveQuery
     */
    public function getEmeraldDelay()
    {
        return $this->hasMany(EmeraldDelay::class, ['id_ref' => 'id']);
    }

    /**
     * @param $amount
     * @return void
     */
    public static function setAmout($amount)
    {
        self::$amount = $amount;
    }

    /**
     * @param $amount
     * @param $type
     * @param $from_user_id
     * @return void
     */
    public function addBalanceUser($amount = 0)
    {
        $this->balance = $amount;

        if (!$this->save()) {
            var_dump($thiы->errors);
            die;
        }

    }

    /**
     * @param $amount
     * @return int|void
     */
    public function addHistoryUser($amount = 0)
    {
        $history = new History();
        $history->setAttributes([
            'user_id '          => $this->id,
            'date'              => date('Y-m-d H:i:s', time()),
            'status'            => Balance::STATUS_WAITING,
            'type'              => Balance::TYPE_REFILL,
            'currency'          => History::CURRENCY_TETHER,
            'creditedCurrency'  => 'TRX',
            'creditedAmount'    => $amount,
            'isApi'             => 'N'
        ]);
        $history->save();
        if ($history) return $history->id;

    }

    /**
     * @param $type
     * @param $from
     * @param $amount
     * @param $comment
     * @return void
     */
    public function addBalanceFromEmerald($type = 11, $from = 1, $amount = 0, $comment = '')
    {
        $h_id = $this->addHistoryUser($amount);
        $to_amout = $this->balance + $amount;

        $balance = new Balance();
        $balance->type = $type;
        $balance->table = 1;
        $balance->from_user_id = $from;
        $balance->to_user_id = $this->id;
        $balance->from_amount = $this->balance;
        $balance->to_amount = $to_amout;
        $balance->history_id = $h_id;
        $balance->comment = $comment;
        $balance->status = 1;
        $balance->save();
    }

    /**
     * Gets query for [[Referrer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReferrer() {
        return $this->hasOne(User::class, ['id' => 'referrer_id']);
    }

    /**
     * Gets query for [[Referrals]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReferrals() {
        return $this->hasMany(User::class, ['referrer_id' => 'id']);
    }

    public static function findIdentity($id, $status = self::STATUS_ACTIVE) {
        return static::find()->filterWhere(['id' => $id, 'status' => $status])->one();
    }

    public static function findIdentityByAccessToken($token, $type = null) {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     *
     * @return static|null
     */
    public static function findByUsername($username, $status = self::STATUS_ACTIVE) {
        return static::find()->filterWhere(['username' => $username, 'status' => $status])->one();
    }

    /**
     * Finds user by email
     *
     * @param string $email
     *
     * @return static|null
     */
    public static function findByEmail($email, $status = self::STATUS_ACTIVE) {
        return static::find()->filterWhere(['email' => $email, 'status' => $status])->one();
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     *
     * @return static|null
     */
    public static function findByPasswordResetToken($token, $status = self::STATUS_ACTIVE) {
        if(!static::isPasswordResetTokenValid($token)){
            return null;
        }
        return static::findOne([
            'password_reset_token' => $token,
            'status'               => $status,
        ]);
    }

    /**
     * Finds user by verification email token
     *
     * @param string $token verify email token
     *
     * @return static|null
     */
    public static function findByVerificationToken($token, $status = self::STATUS_INACTIVE) {
        return static::find()->filterWhere(['verification_token' => $token, 'status' => $status])->one();
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     *
     * @return bool
     */
    public static function isPasswordResetTokenValid($token) {
        if(empty($token)){
            return false;
        }
        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire = isset(Yii::$app->params['user.passwordResetTokenExpire']) ? Yii::$app->params['user.passwordResetTokenExpire'] : 3600;
        return $timestamp + $expire >= time();
    }

    public function getId() {
        return $this->getPrimaryKey();
    }

    public function getAuthKey() {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey) {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     *
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password, $attribute = 'password_hash') {
        return !empty($this->$attribute) && Yii::$app->security->validatePassword($password, $this->$attribute);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password, $attribute = 'password_hash') {
        $this->$attribute = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey() {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken() {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Generates new token for email verification
     */
    public
    function generateEmailVerificationToken() {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken() {
        $this->password_reset_token = null;
    }

    public function getRoleName() {
        return $this->getRolesList()[$this->role];
    }

    public function getRolesList() {
        return [
            self::ROLE_USER      => Yii::t('site', 'User'),
            self::ROLE_MODERATOR => Yii::t('site', 'Moderator'),
            self::ROLE_ADMIN     => Yii::t('site', 'Administrator'),
        ];
    }

    public function getStatusName() {
        return $this->getStatusesList()[$this->status];
    }

    public function getStatusesList() {
        return [
            self::STATUS_BLOCKED  => Yii::t('site', 'Blocked'),
            self::STATUS_INACTIVE => Yii::t('site', 'Inactive'),
            self::STATUS_ACTIVE   => Yii::t('site', 'Active'),
        ];
    }

    public function getPermissionsNames() {
        $result = [];
        $permissions = $this->permissions;
        if(!is_array($permissions)){
            try{
                $permissions = json_decode($permissions, true);
                if(empty($permissions)){
                    $permissions = [];
                }
            }catch(\Exception $e){
                $permissions = [];
            }
        }
        $names = $this->getPermissionsList();
        foreach($permissions as $permission){
            if(isset($names[$permission])){
                $result[] = $names[$permission];
            }
        }
        return empty($result) ? null : implode('<br/>', $result);
    }

    public function getPermissionsList() {
        return [
            'user'       => Yii::t('admin', 'Users'),
            'history'    => Yii::t('admin', 'History'),
            'balance'    => Yii::t('admin', 'Balances'),
            'payout'     => Yii::t('admin', 'Payouts'),
            'activation' => Yii::t('admin', 'Activations'),
            'tour'       => Yii::t('admin', 'Sapphire tours'),
            'page'       => Yii::t('admin', 'Pages'),
            'news'       => Yii::t('admin', 'News'),
            'setting'    => Yii::t('admin', 'Settings'),
        ];
    }

    public function getReferralLink() {
        return Url::to(['/site/signup', 'id' => $this->id], true);
    }

    public function hasClosed() {
        return $this->getActiveActivations()->where(['table' => 6, 'status' => Activation::STATUS_CLOSED])->exists();
    }
    public function hasClosedForever() {
        return $this->getActiveActivationsForever()->where(['table' => 3, 'status' => ActivationForever::STATUS_CLOSED])->exists();
    }

    public function isActive($table = null) {
        $active = $this->getActiveActivations()->filterWhere(['>=', 'table', $table])->exists();
        if(!$active && $this->id == 1 && (empty($table) || $table == 1)){
            // Бесплатная автоматическая активация администратора
            $active = new Activation();
            $active->setAttributes([
                'user_id' => $this->id,
                'table'   => 1,
                'status'  => 1,
            ]);
            $active->save();
            $active = $this->getActiveActivations()->exists();
        }

        if (!$active) {
            $active = Travel::find()->where(['user_id' => $this->id, 'status' => Travel::STATUS_ACTIVE])->count() > 0;
        }

        return $active;
    }

    public function isActiveForever($table = null) {
        $active = $this->getActiveActivationsForever()
            ->filterWhere(['>=', 'table', $table])
            ->exists();
        if(!$active && $this->id == 1 && (empty($table) || $table == 1)){
            // Бесплатная автоматическая активация администратора
            $active = new ActivationForever();
            $active->setAttributes([
                'user_id' => $this->id,
                'table'   => 1,
                'status'  => 1,
            ]);
            die;
            $active->save();
            $active = $this->getActiveActivationsForever()->exists();
        }
        return $active;
    }

    public function getClones($table = null, $start = null) {
        return $this->getActiveActivations()->filterWhere(['>=', 'table', $table])->filterWhere(['start' => $start])->andWhere(['>', 'clone', 0])->all();
    }
    public function getClonesForever($table = null, $start = null) {
        return $this->getActiveActivationsForever()->filterWhere(['>=', 'table', $table])->filterWhere(['start' => $start])->andWhere(['>', 'clone', 0])->all();
    }

    public function getActivations() {
        return $this->hasMany(Activation::class, ['user_id' => 'id']);
    }
    public function getActivationsForever() {
        return $this->hasMany(ActivationForever::class, ['user_id' => 'id']);
    }

    public function getActiveActivations() {
        return $this->hasMany(Activation::class, ['user_id' => 'id'])
    ->andOnCondition(['status' => [Activation::STATUS_ACTIVE, Activation::STATUS_CLOSED]]);
    }

    public function getActiveActivationsForever() {
        return $this->hasMany(ActivationForever::class, ['user_id' => 'id'])
            ->andOnCondition(['status' => [ActivationForever::STATUS_ACTIVE, ActivationForever::STATUS_CLOSED]]);
    }

    public function set($id = null, $at = null, $t = 1, $side = 0) {
        if(empty($id) || empty($at) || $id == $at){
            return false;
        }
        $activation = Activation::find()->where(['status' => Activation::STATUS_ACTIVE, 'id' => $id])->andWhere(['>=', 'table', $t])->andWhere(['<=', 'start', $t])->one();
        if(empty($activation) || empty($activation->user) || ($activation->user->referrer_id != $this->id && $activation->clone == 0) || $activation->isUsed($t)){
            return false;
        }
        $at = Activation::find()->where(['status' => Activation::STATUS_ACTIVE, 'id' => $at, 'table' => $t])->one();
        $s = 't' . $t . ($side == 1 ? '_right' : '_left');
        $ss = 't' . $t . ($side == 1 ? 'Right' : 'Left');
        if(empty($at) || !empty($at->{$ss}) || empty($at->user)){
            return false;
        }
        $aa = $at;
        $ids = [$aa->id, $id];
        // Check for loop
        while($aa = $aa->getTop($t)){
            if($aa->user_id === 1){
                break;
            }
            if(in_array($aa->id, $ids)){
                return false;
            }
            $ids[] = $aa->id;
        }
        /*$uu = $at->user;
        $aa = $at;
        while(!($uu->id == $this->id || $uu->referrer_id == $this->id)){
            $aa = Activation::find()->where(['status' => Activation::STATUS_ACTIVE])->andWhere(['OR', ['t' . $t . '_left' => $aa->id], ['t' . $t . '_right' => $aa->id]])->andWhere(['>=', 'table', $t])->one();
            $uu = $aa->user;
            if($uu->id == $this->id || $uu->referrer_id == $this->id){
                break;
            }
            if($this->id == 1){
                return false;
            }
        }
        */
        $at->{$s} = $activation->id;
        $at->save();
        // TODO начисления и уведомления
        return true;
    }

    public function setForever($id = null, $at = null, $t = 1, $side = 0) {
        if(empty($id) || empty($at) || $id == $at){
            return false;
        }
        $activation = ActivationForever::find()->where(['status' => ActivationForever::STATUS_ACTIVE, 'id' => $id])->andWhere(['>=', 'table', $t])->andWhere(['<=', 'start', $t])->one();
        if(empty($activation) || empty($activation->user) || ($activation->user->referrer_id != $this->id && $activation->clone == 0) || $activation->isUsed($t)){
            return false;
        }
        $at = ActivationForever::find()->where(['status' => ActivationForever::STATUS_ACTIVE, 'id' => $at, 'table' => $t])->one();
        $s = 't' . $t . ($side == 1 ? '_right' : '_left');
        $ss = 't' . $t . ($side == 1 ? 'Right' : 'Left');
        if(empty($at) || !empty($at->{$ss}) || empty($at->user)){
            return false;
        }
        $aa = $at;
        $ids = [$aa->id, $id];
        // Check for loop
        while($aa = $aa->getTop($t)){
            if($aa->user_id === 1){
                break;
            }
            if(in_array($aa->id, $ids)){
                return false;
            }
            $ids[] = $aa->id;
        }

        $at->{$s} = $activation->id;
        $at->save();
        // TODO начисления и уведомления
        return true;
    }

    /**
     * Gets query for [[BalancesFrom]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBalancesFrom() {
        return $this->hasMany(Balance::class, ['from_user_id' => 'id']);
    }

    /**
     * Gets query for [[BalancesTo]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBalancesTo() {
        return $this->hasMany(Balance::class, ['to_user_id' => 'id']);
    }

    /**
     * Gets query for [[Tour]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTours() {
        return $this->hasMany(Tour::class, ['user_id' => 'id']);
    }

    public static function byCountries() {
        self::updateAll(['country' => ''], ['country' => null]);
        $list = self::find()->select('`country`,count(*) as `count`')->groupBy('country')->orderBy(['count' => SORT_DESC])->asArray()->all();
        $result = [];
        foreach($list as $item){
            $item['country'] = $item['country'] ?: Yii::t('yii', '(not set)');
            $result[] = "{$item['country']}: {$item['count']}";
        }
        return implode('<br/>', $result);
    }

    public function getPartnersCount() {
        return $this->getReferrals()->count();
    }

    public function getStructuresCount() {
        $activations = $this->getActivations()->all();
        $users = [];
        $line2 = [];
        $line3 = [];
        foreach($activations as $activation){
            for($i = 1; $i <= 6; $i++){
                if($activation->{"t{$i}_left"}){
                    $line2[] = $activation->{"t{$i}Left"};
                    $users[$activation->{"t{$i}Left"}->user_id] = $activation->{"t{$i}Left"}->user_id;
                }
                if($activation->{"t{$i}_right"}){
                    $line2[] = $activation->{"t{$i}Right"};
                    $users[$activation->{"t{$i}Right"}->user_id] = $activation->{"t{$i}Right"}->user_id;
                }
            }
        }
        foreach($line2 as $activation){
            for($i = 1; $i <= 6; $i++){
                if($activation->{"t{$i}_left"}){
                    $line3[] = $activation->{"t{$i}Left"};
                    $users[$activation->{"t{$i}Left"}->user_id] = $activation->{"t{$i}Left"}->user_id;
                }
                if($activation->{"t{$i}_right"}){
                    $line3[] = $activation->{"t{$i}Right"};
                    $users[$activation->{"t{$i}Right"}->user_id] = $activation->{"t{$i}Right"}->user_id;
                }
            }
        }
        return count($users);
    }

    public static function getCurrent()
    {
        return self::findOne(['id' => Yii::$app->user->id]);
    }
}
