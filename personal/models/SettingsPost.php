<?php

namespace app\modules\personal\models;

use Yii;

use app\modules\personal\exceptions\CanNotBeDeletedException;

/**
 * This is the model class for table "{{%personal_settings_post}}".
 *
 * @property integer $id
 * @property string $name
 *
 * @property Card[] $cards
 * @property Question[] $questions
 * @property Vacancy[] $vacancies
 */
class SettingsPost extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%personal_settings_post}}';
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
            'name' => 'Имя',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCards()
    {
        return $this->hasMany(Card::className(), ['post_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestions()
    {
        return $this->hasMany(Question::className(), ['post_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVacancies()
    {
        return $this->hasMany(Vacancy::className(), ['post_id' => 'id']);
    }
    
    public static function deleteSettingsPost($settingsPost)
    {
        $transaction = SettingsPost::getDb()->beginTransaction();
        try {
            if ($settingsPost->getCards()->count() > 0) {
                throw new CanNotBeDeletedException('Нельзя удалить запись пока она используется в карточках сотрудников.');
            }
            if ($settingsPost->getQuestions()->count() > 0) {
                 throw new CanNotBeDeletedException('Нельзя удалить запись пока она используется в анкетах.');
            }
            if ($settingsPost->getVacancies()->count() > 0) {
                throw new CanNotBeDeletedException('Нельзя удалить запись пока она используется в вакансиях.');   
            }
            
            if (!$settingsPost->delete()) {
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
