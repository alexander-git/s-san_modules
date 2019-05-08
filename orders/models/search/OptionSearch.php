<?php

namespace app\modules\orders\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\orders\models\Option;

class OptionSearch extends Option
{
    public function rules()
    {
        return [
            ['id', 'safe'],
            ['name', 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Option::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'id', $this->id]);
        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
    
}
