<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Balance;

/**
 * BalanceSearch represents the model behind the search form of `app\models\Balance`.
 */
class BalanceSearch extends Balance
{

    public $pageSize = 20;

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'type', 'history_id', 'status', 'updated_at'], 'integer'],
            [['from_user_id', 'to_user_id'], 'string'],
            [['from_amount', 'to_amount'], 'number'],
            [['created_at', 'comment'], 'safe'],
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
        $query = Balance::find();

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
            'id'         => $this->id,
            'type'       => $this->type,
            //'from_user_id' => $this->from_user_id,
            //'to_user_id'   => $this->to_user_id,
            'history_id' => $this->history_id,
            //'from_amount' => $this->from_amount,
            //'to_amount'   => $this->to_amount,
            'status'     => $this->status,
            //'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
        if(!is_null($this->from_amount) && $this->from_amount !== ''){
            $query->andFilterWhere(['OR', ['FLOOR(from_amount)' => floor($this->from_amount)], ['FLOOR(to_amount)' => floor($this->from_amount)]]);
        }
        if($this->from_user_id){
            $query->joinWith(['fromUser' => function ($query) {
                $query->alias('from_user');
            }], false, 'INNER JOIN');
        }
        if($this->to_user_id){
            $query->joinWith(['toUser' => function ($query) {
                $query->alias('to_user');
            }], false, 'INNER JOIN');
        }
        if(preg_match('|^\d+-\d+-\d+|is', $this->created_at)){
            $created_at = strtotime($this->created_at);
            $query->andFilterWhere(['>=', 'created_at', $created_at]);
            $query->andFilterWhere(['<=', 'created_at', $created_at + 3600 * 24]);
        }
        $query->andFilterWhere(['like', 'from_user.username', $this->from_user_id])
            ->andFilterWhere(['like', 'to_user.username', $this->to_user_id])
            ->andFilterWhere(['like', 'comment', $this->comment]);


        return $dataProvider;
    }
}
