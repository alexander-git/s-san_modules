<?php

namespace app\modules\orders\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;
use kartik\mpdf\Pdf;
use app\modules\orders\models\Order;
use app\modules\orders\models\Stage;
use app\modules\orders\models\OrdersApi;

class CheckController extends DefaultController
{    
    // В миллиметрах.
    const CHECK_COURIER_WIDTH = 30;
    const CHECK_COURIER_HEIGHT = 100;
    const CHECK_CLIENT_WIDHT = 30;
    const CHECK_CLIENT_HEIGHT = 100;
    
    const PDF_CSS_FILE = '@app/modules/orders/assets/checkCss/style.css';
    
    public function actionChecksPrint($orderId)
    {
        return $this->render('checksPrint', [
            'model' => $this->findOrderModel($orderId)
        ]);
    }
    
    public function actionPrintCheckCourier($orderId) 
    {
        $model = $this->findOrderModel($orderId);
        $products = $this->getProductsForOrder($model);
        
        $content = $this->renderPartial('check-templates/_checkCourier', [
            'model' => $model,
            'products' => $products,
            'datePreparation' => $this->getOrderDatePrepartion($model),
            'dateAccept' => $this->getOrderDateAccept($model),
        ]);
    
        $pdf = new Pdf([ 
            'mode' => Pdf::MODE_UTF8, 
            'format' => [self::CHECK_COURIER_WIDTH , self::CHECK_COURIER_HEIGHT], 
            'orientation' => Pdf::ORIENT_PORTRAIT, 
            'destination' => Pdf::DEST_BROWSER, 
            'content' => $content,  
            'cssFile' => self::PDF_CSS_FILE,
            'options' => ['title' => 'Чек курьера для заказа №'.$model->order_num],
            'marginLeft' => 0,
            'marginRight' => 0,
            'marginTop' => 0,
            'marginBottom' => 0,
        ]);

        return $pdf->render(); 
    }
    
    public function actionPrintCheckClient($orderId)
    {
        $model = $this->findOrderModel($orderId);
        $products = $this->getProductsForOrder($model);
        
        $content = $this->renderPartial('check-templates/_checkClient', [
            'model' => $model,
            'products' => $products,
            'datePreparation' => $this->getOrderDatePrepartion($model),
            'dateAccept' => $this->getOrderDateAccept($model),
        ]);
    
        $pdf = new Pdf([ 
            'mode' => Pdf::MODE_UTF8, 
            'format' => [self::CHECK_CLIENT_WIDHT, self::CHECK_CLIENT_HEIGHT], 
            'orientation' => Pdf::ORIENT_PORTRAIT, 
            'destination' => Pdf::DEST_BROWSER, 
            'content' => $content,  
            'cssFile' => self::PDF_CSS_FILE,
            'options' => ['title' => 'Чек клиента для заказа №'.$model->order_num],
            'marginLeft' => 0,
            'marginRight' => 0,
            'marginTop' => 0,
            'marginBottom' => 0,
        ]);

        return $pdf->render(); 
    }
    
    private function findOrderModel($id) 
    {
        $model = Order::findOne(['id' => $id]);
        if ($model === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        } 
        
        return $model;
    }
    
        
    private function getProductsForOrder($order) 
    {
        $productIds = [];
        foreach ($order->orderItems as $orderItem) {
            $productIds []= $orderItem->product_id;
        }
        
        $products = OrdersApi::getProductsByIds($productIds);
        $products = ArrayHelper::index($products, 'id');
        
        $additionalProductIds = [];
        foreach ($products as $product) {
            if (
                !empty($product->parent_id) && 
                !isset($products[$product->parent_id])
            ) {
                $additionalProductIds []= $product->parent_id;
            }
        }
        
        if (count($additionalProductIds) > 0) {
            $additionalProducts = OrdersApi::getProductsByIds($additionalProductIds);
            $additionalProducts = ArrayHelper::index( $additionalProducts, 'id');
        } else {
            $additionalProducts = [];
        }
        
        return ArrayHelper::merge($products, $additionalProducts);
    }
    
    private function getOrderDatePrepartion($order)
    {
        if (count($order->orderItemLogs) == 0) {
            return null;            
        }
        
        $orderItemLogs = $order->orderItemLogs;
        $max = $orderItemLogs[0]->date_preparation;
        foreach ($orderItemLogs as $orderItemLog) {
            if ($orderItemLogs->date_preparation > $max) {
                $max = $orderItemLog->date_preparation;
            }
        }
    
        return $max;
    }
    
    public function getOrderDateAccept($order)
    {
        $acceptedStageId = Stage::getAcceptedStageId();
        foreach ($order->logRecords as $logRecord) {
            if ($logRecord->stage_id === $acceptedStageId) {
                return $logRecord->date;
            }
        }
        
        return null;
    }

}

    
