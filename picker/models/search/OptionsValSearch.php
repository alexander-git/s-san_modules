<?php

namespace app\modules\picker\models\search;

use yii\data\ActiveDataProvider;
use app\modules\picker\models\OptionsVal;

class OptionsValSearch extends OptionsVal
{
    
    public $optionLabel;
        
    public function __construct($departmentId, $config = array()) {
        $this->depart_id = $departmentId;
        parent::__construct($config);
    }
    
    public function rules()
    {
        return [
            [['opt_id', 'val', 'optionLabel'], 'safe'],
        ];
    }

    public function search($params)
    {
        $query = OptionsVal::find()
            ->joinWith(['option'])
            ->where(['depart_id' => $this->depart_id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'opt_id',
                    'val',
                    'optionLabel' => [
                        'asc' => ['{{%picker_options}}.label' => SORT_ASC],
                        'desc' => ['{{%picker_options}}.label' => SORT_DESC],
                        'default' => SORT_ASC,
                    ],
                ],
                'defaultOrder' => ['opt_id' => SORT_ASC],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }


        $query->andFilterWhere(['like', 'opt_id', $this->opt_id])
            ->andFilterWhere(['like', 'val', $this->val])
            ->andFilterWhere(['like', '{{%picker_options}}.label', $this->optionLabel]);
        
        return $dataProvider;
    }
    
}

