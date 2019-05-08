<?php

namespace app\modules\picker\models\search;

use yii\data\ActiveDataProvider;
use app\modules\picker\models\Options;

class OptionsSearch extends Options
{
    public function rules()
    {
        return [
            [['id', 'label'], 'safe'],
        ];
    }

    public function search($params)
    {
        $query = Options::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_ASC],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }


        $query->andFilterWhere(['like', 'id', $this->id])
            ->andFilterWhere(['like', 'label', $this->label]);
        
        return $dataProvider;
    }
    
}
