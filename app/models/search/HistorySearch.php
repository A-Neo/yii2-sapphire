<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\History;

/**
 * HistorySearch represents the model behind the search form of `app\models\History`.
 */
class HistorySearch extends History
{

    public $pageSize = 20;

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id'], 'integer'],
            [['date', 'type', 'status', 'user_id', 'from', 'debitedCurrency', 'to', 'creditedCurrency', 'protect', 'comment', 'isApi'], 'safe'],
            [['debitedAmount', 'creditedAmount', 'payeerFee', 'gateFee', 'exchangeRate'], 'number'],
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
        $query = History::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query'      => $query,
            'sort'       => [
                'defaultOrder' => ['id' => SORT_DESC],
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
        $query->andFilterWhere([
            'id'             => $this->id,
            'date'           => $this->date,
            'debitedAmount'  => $this->debitedAmount,
            'creditedAmount' => $this->creditedAmount,
            'payeerFee'      => $this->payeerFee,
            'gateFee'        => $this->gateFee,
            'exchangeRate'   => $this->exchangeRate,
        ]);
        if($this->user_id){
            $query->joinWith(['user' => function ($query) {
                $query->alias('user');
            }], false, 'INNER JOIN');
        }
        $query->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'user.username', $this->user_id])
            ->andFilterWhere(['like', 'from', $this->from])
            ->andFilterWhere(['like', 'debitedCurrency', $this->debitedCurrency])
            ->andFilterWhere(['like', 'to', $this->to])
            ->andFilterWhere(['like', 'creditedCurrency', $this->creditedCurrency])
            ->andFilterWhere(['like', 'protect', $this->protect])
            ->andFilterWhere(['like', 'comment', $this->comment])
            ->andFilterWhere(['like', 'isApi', $this->isApi]);

        return $dataProvider;
    }
}
