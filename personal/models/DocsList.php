<?php

namespace app\modules\personal\models;

use Yii;

use app\modules\personal\exceptions\CanNotBeDeletedException;

/**
 * This is the model class for table "{{%personal_docs_list}}".
 *
 * @property integer $id
 * @property string $name
 * @property integer $type
 *
 * @property CardDocs[] $cardDocs
 */
class DocsList extends \yii\db\ActiveRecord
{
    const SCENARIO_UPDATE = 'update';
    
    const TYPE_DEFAULT = 0;
    const TYPE_LOADABLE = 1;
    
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%personal_docs_list}}';
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
            
            ['type', 'required'],
            ['type', 'integer'],
            ['type', 'in', 'range' => array_keys(self::getTypesArray())],
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
            'type' => 'Тип',
        ];
    }
    
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        
        $scenarios[self::SCENARIO_UPDATE] = [
            'name',
            '!type',
        ];
        
        return $scenarios;
    }

    public static function getTypesArray()
    {
        return [
            self::TYPE_DEFAULT => 'Обычный',
            self::TYPE_LOADABLE => 'Загружаемый',
        ];
    }
    
    public function getTypeName()
    {
        return self::getTypesArray()[$this->type];
    }
    
    public function getIsTypeDefault()
    {
        return $this->type === self::TYPE_DEFAULT;
    }
    
    public function getIsTypeLoadable()
    {
        return $this->type === self::TYPE_LOADABLE;
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCardDocs()
    {
        return $this->hasMany(CardDocs::className(), ['docs_id' => 'id']);
    }
    
    public function getCards()
    {
        return $this->hasMany(Card::className(), ['id' => 'card_id'])
            ->via('cardDocs');
    }
 
    public static function deleteDocsList($docsList)
    {
        $transaction = DocsList::getDb()->beginTransaction();
        try {            
            if ($docsList->getCardDocs()->count() > 0) {
                throw new CanNotBeDeletedException('Нельзя удалить запись пока она используется в карточках сотрудников.');
            }
            
            if (!$docsList->delete()) {
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
