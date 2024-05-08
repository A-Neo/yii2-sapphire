<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\User;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * UserSearch represents the model behind the search form of `app\models\User`.
 */
class UserSearch extends User
{

    public $referrer;

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id'], 'safe'],
            [['referrer_id', 'status', 'updated_at', 'sapphire'], 'integer'],
            [['balance'], 'number'],
            [['referrer', 'username', 'wallet', 'balance', 'created_at', 'full_name', 'country', 'birth_date', 'auth_key', 'password_hash', 'password_reset_token', 'email', 'verification_token', 'role', 'permissions', 'wallet'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params) {
        $query = User::find();
        $query->joinWith(['referrer' => function ($query) {
            $query->alias('referrer');
        }]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => [
                'attributes' => array_merge($this->attributes(), [
                    'referrer' => [
                        'asc'     => ['referrer.username' => SORT_ASC],
                        'desc'    => ['referrer.username' => SORT_DESC],
                        'default' => SORT_DESC,
                    ],
                ]),
            ],
        ]);

        $this->load($params);

        if(!$this->validate()){
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'user.id'          => $this->id,
            'user.referrer_id' => $this->referrer_id,
            'user.wallet'      => $this->wallet,
            'user.balance'     => $this->balance,
            'user.country'     => $this->country,
            'user.birth_date'  => $this->birth_date,
            'user.status'      => $this->status,
            'user.sapphire'    => $this->sapphire,
            //'user.created_at'  => $this->created_at,
            'user.updated_at'  => $this->updated_at,
        ]);
        if(preg_match('|^\d+-\d+-\d+|is', $this->created_at)){
            $created_at = strtotime($this->created_at);
            $query->andFilterWhere(['>=', 'created_at', $created_at]);
            $query->andFilterWhere(['<=', 'created_at', $created_at + 3600 * 24]);
        }
        $query->andFilterWhere(['like', 'user.username', $this->username])
            ->andFilterWhere(['like', 'user.full_name', $this->full_name])
            ->andFilterWhere(['like', 'referrer.username', $this->referrer])
            ->andFilterWhere(['like', 'referrer.auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'referrer.password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'referrer.password_reset_token', $this->password_reset_token])
            ->andFilterWhere(['like', 'referrer.email', $this->email])
            ->andFilterWhere(['like', 'referrer.verification_token', $this->verification_token])
            ->andFilterWhere(['like', 'referrer.role', $this->role])
            ->andFilterWhere(['like', 'referrer.permissions', $this->permissions]);

        return $dataProvider;
    }
}
