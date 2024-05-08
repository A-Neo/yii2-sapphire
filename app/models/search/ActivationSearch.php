<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Activation;

/**
 * ActivationSearch represents the model behind the search form of `app\models\Activation`.
 *
 * @property string $username
 */
class ActivationSearch extends Activation
{
    public $username;

    public $pageSize = 20;

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'user_id', 'table', 'clone', 'status', 't1_left', 't1_right', 't2_left', 't2_right', 't3_left', 't3_right', 't4_left', 't4_right', 't5_left', 't5_right', 't6_left', 't6_right', 'updated_at'], 'integer'],
            [['username', 'created_at'], 'safe'],
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
        $query = Activation::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query'      => $query,
            'sort'       => [
                'attributes'   => array_merge($this->attributes(), ['username']),
                'defaultOrder' => ['created_at' => SORT_DESC],
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
            't.user_id'    => $this->user_id,
            't.table'      => $this->table,
            't.clone'      => $this->clone,
            't.status'     => $this->status,
            't.t1_left'    => $this->t1_left,
            't.t1_right'   => $this->t1_right,
            't.t2_left'    => $this->t2_left,
            't.t2_right'   => $this->t2_right,
            't.t3_left'    => $this->t3_left,
            't.t3_right'   => $this->t3_right,
            't.t4_left'    => $this->t4_left,
            't.t4_right'   => $this->t4_right,
            't.t5_left'    => $this->t5_left,
            't.t5_right'   => $this->t5_right,
            't.t6_left'    => $this->t6_left,
            't.t6_right'   => $this->t6_right,
            //'t.created_at' => $this->created_at,
            't.updated_at' => $this->updated_at,
        ]);
        if(preg_match('|^\d+-\d+-\d+|is', $this->created_at)){
            $created_at = strtotime($this->created_at);
            $query->andFilterWhere(['>=', 'created_at', $created_at]);
            $query->andFilterWhere(['<=', 'created_at', $created_at + 3600 * 24]);
        }
        if($this->username){
            $query->joinWith('user');
            $query->andFilterWhere(['like', 'user.username', $this->username]);
        }

        return $dataProvider;
    }
}
