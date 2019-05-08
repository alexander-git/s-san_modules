<?php

namespace app\modules\orders\models\search;


use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\orders\models\Option;
use app\modules\orders\models\OptionVal;

class OptionValSearch extends OptionVal
{
    public $optionName;
    
    public function rules()
    {
        return [
            ['option_id', 'safe'],
            ['value', 'safe'],
            ['optionName', 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($cityId, $params)
    {
        $query = OptionVal::find()
            ->select([
                "ov.*",
                "o.name AS optionName"
            ])
            ->from(['ov' => OptionVal::tableName()])
            ->joinWith([
                'option' => function($q) {
                    $q->from(['o' => Option::tableName()]);
                },
            ]);
                
        $query->where(['ov.city_id' => $cityId]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'option_id',
                    'value',
                    'cityId',
                    'optionName',
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'option_id', $this->option_id]);
        $query->andFilterWhere(['like', 'ov.value', $this->value]);
        $query->andFilterWhere(['like', 'o.name', $this->optionName]);
        
        return $dataProvider;
    }
}
