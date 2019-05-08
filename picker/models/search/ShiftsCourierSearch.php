<?php

namespace app\modules\picker\models\search;

use yii\data\ActiveDataProvider;
use app\modules\picker\models\ShiftsCourier;

class ShiftsCourierSearch extends ShiftsCourier
{
       
    public function __construct($shiftsPickerId, $config = array()) {
        $this->shifts_picker_id = $shiftsPickerId;
        parent::__construct($config);
    }
    
    public function rules()
    {
        return [
            [['courier_name', 'courier_phone'], 'safe'],
            ['type_courier', 'in', 'range' => array_keys(self::getTypeCouriersArrayFilter())],
            ['state', 'in', 'range' => array_keys(static::getStatesArray())],
        ];
    }
    
    public static function getTypeCouriersArrayFilter() 
    {
        $result = ShiftsCourier::getTypeCouriersArray();
        unset($result[ShiftsCourier::TYPE_COURIER_PICKUP]);
        return $result;
    }

    public function search($params)
    {
        $query = ShiftsCourier::find()
            ->where(['shifts_picker_id' => $this->shifts_picker_id])
            ->andWhere(['<>', 'type_courier', self::TYPE_COURIER_PICKUP]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['date_open' => SORT_DESC],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
        
    
        $query->andFilterWhere([
            'courier_id' => $this->courier_id,
            'type_courier' => $this->type_courier,
            'state' => $this->state,
        ]);

        $query->andFilterWhere(['like', 'courier_name', $this->courier_name])
            ->andFilterWhere(['like', 'courier_phone', $this->courier_phone]);
        
        return $dataProvider;
    }
    
}
