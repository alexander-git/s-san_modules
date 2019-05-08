<?php

namespace app\modules\cashdesks\models;

use Yii;

/**
 * This is the model class for table "{{%cashdesks_replen_type}}".
 *
 * @property integer $id
 * @property string $name
 */
class ReplenType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%cashdesks_replen_type}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'required'],
            ['name', 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
        ];
    }
    
}
