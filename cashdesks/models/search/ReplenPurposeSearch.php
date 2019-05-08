<?php

namespace app\modules\cashdesks\models\search;

use Yii;

use yii\data\ActiveDataProvider;
use app\modules\cashdesks\models\ReplenPurpose;

class ReplenPurposeSearch extends ReplenPurpose
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['id', 'integer'],
            ['name', 'safe'],
        ];
    }

    public function search($params)
    {
        $query = ReplenPurpose::find();


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
