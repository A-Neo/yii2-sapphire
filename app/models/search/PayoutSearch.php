<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Payout;

/**
 * PayoutSearch represents the model behind the search form of `app\models\Payout`.
 *
 */
class PayoutSearch extends Payout
{

    public $pageSize = 20;
    public $wallet;
    public $username;

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'balance_id', 'user_id', 'history_id', 'comission', 'status', 'updated_at'], 'integer'],
            [['amount'], 'number'],
            [['wallet_type'], 'string'],
            [['wallet', 'created_at', 'username', 'comment'], 'safe'],
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
        $query = Payout::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query'      => $query,
            'sort'       => [
                'defaultOrder' => ['id' => SORT_DESC],
                'attributes'   => array_merge($this->attributes(), ['username', 'wallet']),
            ],
            'pagination' => [
                'defaultPageSize' => $this->pageSize,
            ],
        ]);

        $this->load($params);

        if(!$this->validate()){
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->alias('t');
        $query->andFilterWhere([
            't.id'         => $this->id,
            't.balance_id' => $this->balance_id,
            't.user_id'    => $this->user_id,
            't.history_id' => $this->history_id,
            't.amount'     => $this->amount,
            't.status'     => $this->status,
            //'t.created_at' => $this->created_at,
            't.updated_at' => $this->updated_at,
        ]);
        if(preg_match('|^\d+-\d+-\d+|is', $this->created_at)){
            $created_at = strtotime($this->created_at);
            $query->andFilterWhere(['>=', 't.created_at', $created_at]);
            $query->andFilterWhere(['<=', 't.created_at', $created_at + 3600 * 24]);
        }
        $query->joinWith('user');
        $query->andFilterWhere(['like', 'wallet', $this->wallet])
            ->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'comment', $this->comment]);
        return $dataProvider;
    }
}
