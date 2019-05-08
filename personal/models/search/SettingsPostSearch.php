<?php

namespace app\modules\personal\models\search;

use yii\data\ActiveDataProvider;
use app\modules\personal\models\SettingsPost;

class SettingsPostSearch extends SettingsPost
{
    public function rules()
    {
        return [
            ['id', 'integer'],
            ['name', 'safe'],
        ];
    }

    public function search($params)
    {
        $query = SettingsPost::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
    
}
