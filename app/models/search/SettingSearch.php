<?php

namespace app\models\search;

use app\models\SettingModel;
use yii\data\ActiveDataProvider;

/**
 * Class SettingSearch
 *
 * @package yii2mod\settings\models\search
 */
class SettingSearch extends SettingModel
{

    /**
     * @var int the default page size
     */
    public $pageSize = 10;

    /**
     * @inheritdoc
     */
    public function rules(): array {
        return [
            [['type', 'section', 'key', 'value', 'status', 'description', 'system'], 'safe'],
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search(array $params): ActiveDataProvider {
        $query = self::find();

        $dataProvider = new ActiveDataProvider([
            'query'      => $query,
            'pagination' => [
                'pageSize' => $this->pageSize,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'status'  => $this->status,
            'section' => $this->section,
            'type'    => $this->type,
            'system'  => $this->system,
        ]);

        $query->andFilterWhere(['like', 'key', $this->key])
            ->andFilterWhere(['like', 'value', $this->value])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }

    public function sections() {
        return self::find()->select('section')->groupBy('section')->asArray()->column();
    }

}
