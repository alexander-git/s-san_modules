<?php

namespace app\modules\orders\models;

use yii\helpers\Json;
use app\modules\orders\exceptions\CanNotBeDeletedException;

/**
 * This is the model class for table "{{%orders_stage}}".
 *
 * @property integer $id
 * @property string $name
 * @property integer $sort
 *
 * @property LogRecord[] $logRecords
 * @property Order[] $orders
 */
class Stage extends \yii\db\ActiveRecord
{    
    private static $stageIdsCache = [];
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%orders_stage}}';
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
            
            ['sort', 'required'],
            ['sort', 'integer'],
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
            'sort' => 'Порядок',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLogRecords()
    {
        return $this->hasMany(LogRecords::className(), ['stage_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Order::className(), ['stage_id' => 'id']);
    }
    
    public static function deleteStage($stage)
    {        
        $transaction = static::getDb()->beginTransaction();
        try {
            if (count($stage->orders) > 0) {
                throw new CanNotBeDeletedException('Удаление невозможно. Есть заказы с такой стадией.');
            }
            if (count($stage->logRecords) > 0) {
                throw new CanNotBeDeletedException('Удаление невозможно. Есть записи логов стадией.');
            }
             
            if ($stage->delete() === false) {
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
    
    public static function getNewStageId()
    {
        return self::getCachedStateId('Новый');        
    }
    
    public static function getAcceptedStageId() 
    {
        return self::getCachedStateId('Принят');
    }
    
    public static function getInWorkStageId()
    {
        return self::getCachedStateId('В работе');
    }
    
    public static function getDeliveringStageId()
    {
        return self::getCachedStateId('Доставляется');
    }
    
    public static function getDeliveredStageId()
    {
        return self::getCachedStateId('Доставлен');
    }
            
    public static function getCanceledStageId() 
    {
        return self::getCachedStateId('Отменён');
    }
    
    public static function getStageIdsAsJson()
    {
        $result = new \stdClass();
        $result->new = self::getNewStageId();
        $result->accepted = self::getAcceptedStageId();
        $result->inWork = self::getInWorkStageId();
        $result->delivering = self::getDeliveringStageId();
        $result->delevered = self::getDeliveredStageId();
        $result->canceled = self::getCanceledStageId();
        return Json::encode($result);
    }
    
    private static function getCachedStateId($stageName)
    {
        if (!isset(self::$stageIdsCache[$stageName])) {
            self::$stageIdsCache[$stageName] = (int) static::find()
                ->where(['name' => $stageName])
                ->scalar();
        }
                
        return self::$stageIdsCache[$stageName];
    }
    
}
