<?php

namespace app\modules\orders\controllers;

use yii\filters\VerbFilter;
use app\modules\orders\models\Order;
use app\modules\orders\models\Stage;
use app\modules\orders\models\LogRecord;
use app\modules\orders\models\OrdersApi;

class CronController extends DefaultController 
{
    private static $DELIVERY_TIME = 15* 60;  // В секундах. 
    
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'update-delivering-orders' => ['get'],
                ],
            ],
        ];
    }
    
    public function actionUpdateDeliveringOrders()
    {
        $currentTime = OrdersApi::getCurrentTimestamp();
        $maxPossibleDeliveringTime = (new \DateTime())
            ->setTimestamp($currentTime)
            ->sub(new \DateInterval('PT'.self::$DELIVERY_TIME.'S'))
            ->getTimestamp();
        
        $deliveringStageId = Stage::getDeliveringStageId();        
        $orders = Order::find()
            ->where([Order::tableName().'.stage_id' => $deliveringStageId])
            ->innerJoinWith([
                'logRecords' => function($q) use ($deliveringStageId, $maxPossibleDeliveringTime) {
                    $q->onCondition([
                        'and',
                        ['=', LogRecord::tableName().'.stage_id', $deliveringStageId], 
                        ['<=', LogRecord::tableName().'.date', $maxPossibleDeliveringTime],
                    ]);
                },
            ])
            ->groupBy([Order::tableName().'.id'])
            ->all();
     
        $deliveredStageId = Stage::getDeliveredStageId();
        $success = true;
        foreach ($orders as $order) {
            $logRecord = new LogRecord();
            $logRecord->stage_id = $deliveredStageId;
            $success = $success && Order::updateStageInOrder($order, $logRecord);
        }    
        
        return $success;
    }
    
}