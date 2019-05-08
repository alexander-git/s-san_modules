<?php

namespace app\modules\personal\models;

use Yii;

use app\modules\personal\exceptions\CanNotBeDeletedException;

/**
 * This is the model class for table "{{%personal_about_us_value}}".
 *
 * @property integer $id
 * @property string $name
 *
 * @property Question[] $questions
 */
class AboutUsValue extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%personal_about_us_value}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'required'],
            ['name', 'string', 'max' => 255],
            ['name', 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Значение',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestions()
    {
        return $this->hasMany(Question::className(), ['about_us_id' => 'id']);
    }
    
    public static function deleteAboutUsValue($aboutUsValue)
    {
        $transaction = AboutUsValue::getDb()->beginTransaction();
        try {
            if ($aboutUsValue->getQuestions()->count() > 0) {
                throw new CanNotBeDeletedException('Нельзя удалить запись пока она используется в анкетах.');
            }
            
            if (!$aboutUsValue->delete()) {
                $transaction->rollBack();
                return false;
            }
            
            $transaction->commit();
            return true;
        }
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
    
}
