<?php

namespace app\modules\picker\models\search;

use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use app\modules\picker\models\ShiftsCourier;

class ShiftsCourierSummarySearch extends ShiftsCourier
{
    
    public function __construct($config = [])
    {
        parent::__construct($config);
    }
    
    public function rules()
    {
        return [
            [['courier_name'], 'safe'],
            
            //['count_order', 'check_sum', 'check_nocash', 'spend', 'gifts'], 'safe'],
        ];
    }
    
    public function attributeLabels() {
        $labels = [
            'count_order' => 'Кол-во Заказов',
            'check_sum' => 'Оборот (руб.)',
            'cash' => 'Наличные (руб.)',
            'check_nocash' => 'Безнал (руб.)',
            'spend' => 'Расходы (руб.)',
            'gifts' => 'Сертификаты (руб.)',
        ];
        
        return ArrayHelper::merge(parent::attributeLabels(), $labels);
    }
            
    public function search($shiftsId, $params)
    {
        $query = ShiftsCourierSummarySearch::find()
            ->where(['shifts_id' => $shiftsId]);
    
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => [
                'defaultOrder' => ['date_close' => SORT_ASC],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
        
        $query->andFilterWhere(['like', 'courier_name', $this->courier_name]);
        
        return $dataProvider;
    }
    
}
