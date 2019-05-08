<?php

namespace app\modules\personal\models\search;

use yii\data\ActiveDataProvider;
use app\modules\personal\models\QuestionHistory;

class QuestionHistorySearch extends QuestionHistory
{
    
    public function __construct($questionId, $config = array())
    {
        $this->question_id = $questionId;
        parent::__construct($config);
    }
    
    public function rules()
    {
        return [
            [['id', 'date_change', 'state'], 'integer'],
            ['text', 'safe'],
        ];
    }
    
    public function search($params)
    {
        $query = QuestionHistory::find()
            ->where(['question_id' => $this->question_id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'date_change',
                ],
                'defaultOrder' => [
                    'date_change' => SORT_DESC
                ],
            ], 
            'pagination' => false,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'state' => $this->state,
        ]);

        $query->andFilterWhere(['like', 'text', $this->text]);
    
        return $dataProvider;
    }
    
}