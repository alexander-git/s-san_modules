<?php

namespace app\modules\orders\models;

/**
 * This is the model class for table "{{%orders_category_station}}".
 *
 * @property integer $category_id
 * @property integer $station_id
 */
class CategoryStation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%orders_category_station}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['city_id', 'required'],
            ['city_id', 'integer'],
            
            ['category_id', 'required'],
            ['category_id', 'integer'],
            
            ['station_id', 'required'],
            ['station_id', 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'city_id' => 'Город',
            'category_id' => 'Категория',
            'station_id' => 'Станция',
        ];
    }
}
