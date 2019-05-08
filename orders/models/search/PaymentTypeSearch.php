<?php

namespace app\modules\orders\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\orders\models\PaymentType;

class PaymentTypeSearch extends PaymentType
{

    public function rules()
    {
        return [
            [['id', 'sort'], 'integer'],
            [['name'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = PaymentType::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'sort' => SORT_ASC,
                    'id' => SORT_ASC,
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'sort' => $this->sort,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
