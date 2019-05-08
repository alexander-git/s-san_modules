<?php

namespace app\modules\personal\models\search;

use yii\data\ActiveDataProvider;
use app\modules\personal\models\DocsList;
use app\modules\personal\models\CardDocs;

class DocsListCardSearch extends DocsList
{
    public $check;
    
    public function rules()
    {
        return [
            [['id', 'type'], 'integer'],
            ['check', 'boolean'],
            ['name', 'safe'],
        ];
    }

    public function search($cardId, $params)
    {
        $query = DocsList::find()
            ->select([DocsList::tableName().'.*', 'cardDocs.check'])  
            ->joinWith([
                'cardDocs' => function  ($query) use ($cardId) {
                    $query->from(['cardDocs' => CardDocs::tableName()])
                        ->andOnCondition(['cardDocs.card_id' => $cardId]);
                },
            ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => [
                'attributes' => [
                    'id',
                    'name',
                    'type',
                    'check' => [
                        'asc' => ['cardDocs.check' => SORT_ASC],
                        'desc' => ['cardDocs.check' => SORT_DESC],
                    ],
                ],
                'defaultOrder' => ['id' => SORT_ASC],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'type' => $this->type,
        ]);

        if ($this->check !== null && $this->check !== '') {
            $check = (boolean) $this->check;
            if ($check) {
                $query->andWhere(['not', ['cardDocs.check' => null]]);
            } else {
                $query->andWhere(['cardDocs.check' => null]);
            }
        }
        
        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
    
}