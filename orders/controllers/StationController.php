<?php

namespace app\modules\orders\controllers;

use Yii;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use app\modules\orders\models\Order;
use app\modules\orders\models\OrderItemLog;
use app\modules\orders\models\Stage;
use app\modules\orders\models\Station;
use app\modules\orders\models\OptionVal;
use app\modules\orders\models\OrdersApi;
use app\modules\orders\models\search\OrderReadySearch;

class StationController extends DefaultController
{    
    const CHECK_COURIER_WIDTH = 30;
    const CHECK_COURIER_HEIGHT = 100;
    const CHECK_CLIENT_WIDHT = 30;
    const CHECK_CLIENT_HEIGHT = 100;
    
    private $cityNamesCache = [];
    
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'start-card-in-work' => ['post'],
                    'complete-card' => ['post'],
                    'cancel-card' => ['post'],
                    'set-product-preparing' => ['post'],
                    'set-product-prepared' => ['post'],
                    'start-card-in-pick' => ['post'],
                    'deliver-card-pick' => ['post'],
                    'cancel-card-pick' => ['post'],
                    'set-product-added' => ['post'],
                ],
            ],
        ];
    }
        
    public function actionIndex()
    {
        $cityId = $this->getCityId();
        if ($cityId === null) {
            return $this->redirect(['city-select']);
        }
        
        return $this->render('index', [
            'cityId' => $cityId,
            'cityName' => $this->getCityNameById($cityId),
            'stationsList' => $this->getStationsList(),
            'pickStationId' => Station::getPickStationId(),
        ]);
    }
    
    public function actionCitySelect()
    {
        return $this->render('citySelect', [
            'citiesList' => $this->getCitiesList(),
        ]);
    }
    
    public function actionStation($stationId, $cityId)
    {
        return $this->render('station', [
            'stationId' => $stationId,
            'stationName' =>  $this->getStationNameById($stationId),
            'cityId' => $cityId,
            'cityName' => $this->getCityNameById($cityId),
            'timeOffset' => $this->getTimeOffset(),
            'stationOrdersCount' => OptionVal::getStationOrdersCount($cityId),
            'orderStageIdsJson' => Stage::getStageIdsAsJson(),
            'orderItemLogStatesJson' => OrderItemLog::getStatesAsJson(),
        ]);
    }
    
    public function actionStationPick($cityId)
    {
        $stationId = Station::getPickStationId();
        return $this->render('stationPick', [
            'stationId' => $stationId,
            'stationName' =>  $this->getStationNameById($stationId),
            'cityId' => $cityId,
            'cityName' => $this->getCityNameById($cityId),
            'timeOffset' => $this->getTimeOffset(),
            'orderStageIdsJson' => Stage::getStageIdsAsJson(),
            'orderItemLogStatesJson' => OrderItemLog::getStatesAsJson(),
        ]);
    }
     
    public function actionGetCards($cityId, $stationId)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $orders = OrderItemLog::getOrdersForStation($cityId, $stationId);
        $ordersJson = $this->prepareOrdersFromStationToJsonEncode($orders);
        return $ordersJson;
    }
    
    public function actionUpdateCards($cityId, $stationId)
    {
        $get = Yii::$app->request->queryParams;
        if (isset($get['orderIds'])) {
            $needAdditionalOrders = true;
        } else {
           $needAdditionalOrders = false;
        }
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (!$needAdditionalOrders) {
            $orders = OrderItemLog::getOrdersForStation($cityId, $stationId);
            $ordersJson = $this->prepareOrdersFromStationToJsonEncode($orders);  
            return $ordersJson;
        }
        
        $orderIds = $get['orderIds']; 
        $orders = OrderItemLog::getOrdersForStationAdditional($cityId, $stationId, $orderIds);
        $ordersJson = $this->prepareOrdersFromStationToJsonEncode($orders);  
        return $ordersJson;
    }
    
    
    public function actionStartCardInWork($orderId, $stationId)
    {
        $model = $this->findOrderModel($orderId);
        $success = OrderItemLog::startCardInWork($model, $stationId);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $order = OrderItemLog::getOrderForStation($orderId, $stationId);
        if ($success) {
            return $this->getOrderSuccessObject($order);
        } else {
            return $this->getOrderErrorObject($order, $model);
        }
    }
        
    public function actionCompleteCard($orderId, $stationId)
    {
        $model = $this->findOrderModel($orderId);
        $success = OrderItemLog::completeCard($model, $stationId);
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($success) {
            return $this->getOrderIdSuccessObject($orderId);
        } else {
            $order = OrderItemLog::getOrderForStation($orderId, $stationId);
            return $this->getOrderErrorObject($order, $model);
        }
    }
    
    public function actionCancelCard($orderId, $stationId)
    {
        $model = $this->findOrderModel($orderId);
        $success = OrderItemLog::cancelCard($model, $stationId);
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($success) {
            return $this->getOrderIdSuccessObject($orderId);
        } else {
            $order = OrderItemLog::getOrderForStation($orderId, $stationId);
            return $this->getOrderErrorObject($order, $model);
        }
    }
    
    public function actionSetProductPrepared($orderId, $productId)
    {
        $model = $this->findOrderItemLogModel($orderId, $productId);
        $success = OrderItemLog::setProductPrepared($model);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $order = OrderItemLog::getOrderForStation($orderId, $model->station);
        if ($success) {
            return $this->getOrderSuccessObject($order);
        } else {
            return $this->getOrderErrorObject($order, $model);
        }
    }
    
    public function actionSetProductPreparing($orderId, $productId)
    {
        $model = $this->findOrderItemLogModel($orderId, $productId);
        $success = OrderItemLog::setProductPreparing($model);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $order = OrderItemLog::getOrderForStation($orderId, $model->station);
        if ($success) {
            return $this->getOrderSuccessObject($order);
        } else {
            return $this->getOrderErrorObject($order, $model);
        }
    }
    
    public function actionGetCardsPick($cityId)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $orders = OrderItemLog::getOrdersForStationPick($cityId);
        $ordersJson = $this->prepareOrdersFromStationToJsonEncode($orders);
        return $ordersJson;
    }
    
    public function actionUpdateCardsPick($cityId)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $orders = OrderItemLog::getOrdersForStationPick($cityId);
        $ordersJson = $this->prepareOrdersFromStationToJsonEncode($orders);
        return $ordersJson;
    }
    
    public function actionStartCardInPick($orderId) 
    {
        $model = $this->findOrderModel($orderId);
        $success = OrderItemLog::startCardInPick($model);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $order = OrderItemLog::getOrderForStationPick($orderId); 
        if ($success) {
            return $this->getOrderSuccessObject($order);
        } else {
            return $this->getOrderErrorObject($order, $model);
        }
    }
    
    public function actionDeliverCardPick($orderId)
    {
        $model = $this->findOrderModel($orderId);
        $success = OrderItemLog::deliverCardPick($model);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $order = OrderItemLog::getOrderForStationPick($orderId); 
        if ($success) {
            return $this->getOrderSuccessObject($order);
        } else {
            return $this->getOrderErrorObject($order, $model);
        }
    }
    
    public function actionCancelCardPick($orderId) 
    {
        $model = $this->findOrderModel($orderId);
        $success = OrderItemLog::cancelCardPick($model);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $order = OrderItemLog::getOrderForStationPick($orderId); 
        if ($success) {
            return $this->getOrderSuccessObject($order);
        } else {
            return $this->getOrderErrorObject($order, $model);
        }
    }
                                
    public function actionSetProductAdded($orderId, $productId)
    {
        $model = $this->findOrderItemLogModel($orderId, $productId);
        $success = OrderItemLog::setProductAdded($model);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $order = OrderItemLog::getOrderForStationPick($orderId); 
        if ($success) {
            return $this->getOrderSuccessObject($order);
        } else {
            return $this->getOrderErrorObject($order, $model);
        }
    }
    
    public function actionIndexReady($cityId)
    {
        $searchModel = new OrderReadySearch();
        $dataProvider = $searchModel->search($cityId, Yii::$app->request->queryParams);

        return $this->render('indexReady', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'cityId' => $cityId,
            'cityName' => $this->getCityNameById($cityId),
            'stagesList' => $this->getStagesListReady(),
        ]);
    }
    
    private function findOrderModel($id) 
    {
        $model = Order::findOne(['id' => $id]);
        if ($model === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        } 
        
        return $model;
    }
    
    private function findOrderItemLogModel($orderId, $productId)
    {
        $model = OrderItemLog::findOne([
            'order_id' => $orderId,
            'product_id' => $productId,
        ]);
        if ($model === null) {
            throw new NotFoundHttpException('The requested page does not exist.'); 
        }
        
        return $model;
    } 
    
    private function getOrderSuccessObject($order) 
    {
        $result = new \stdClass();
        $result->success = true;
        $result->order = $this->prepareOrderFromStationToJsonEncode($order);
        $result->orderId = $order->id;
        return $result;
    }
    
    private function getOrderIdSuccessObject($orderId) 
    {
        $result = new \stdClass();
        $result->success = true;
        $result->orderId = $orderId;
        return $result;
    }
    
    private function getOrderErrorObject($order, $model)
    {
        $result = new \stdClass();
        $result->success = false;
        $result->order = $this->prepareOrderFromStationToJsonEncode($order);
        $errorMessage = $this->getErrorMessageOnModel($model);
        if (!empty($errorMessage)) {
            $result->errorMessage = $errorMessage;
        }
        return $result;
    }
    
    private function getErrorMessageOnModel($model)
    {
        $errors = $model->getFirstErrors();
        if (count($errors) === 0) {
            return null;
        } else {
            return $errors[0];
        }
    }
        
    private function getCityId()
    {
        $get = Yii::$app->request->queryParams;
       
        // Если передан в get запомним в сессии.
        if (isset($get['cityId'])) {
            $cityId = (int) $get['cityId'];
            Yii::$app->session->set('cityId', $cityId);
            return $cityId;
        }
        
        
        $sessionCityId = Yii::$app->session->get('cityId', null);
        return $sessionCityId;
    }
    
    private function getTimeOffset()
    {
        return (new \DateTime())->getOffset();
    }
    
    
    protected function getCityNameById($cityId)
    {
        if (isset($this->cityNamesCache[$cityId])) {
            return $this->cityNamesCache[$cityId];
        }
        
        $cityName = parent::getCityNameById($cityId);
        if ($cityName === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        
        $this->cityNamesCache[$cityId] = $cityName;
        return $cityName;
    }
      
    private function getStationsList()
    {
        return Station::getStationsList();
    }
    
    private function getStationNameById($stationId)
    {
        return Station::getStationNameById($stationId);
    }
    
    private function getStagesListReady() 
    {
        $stages = Stage::find()
            ->where(['in', 'id', OrderReadySearch::getPossibleStageIds()])
            ->orderBy(['sort' => SORT_ASC])
            ->all();
        
        return ArrayHelper::map($stages, 'id', 'name'); 
    }
    
    private function prepareOrdersFromStationToJsonEncode($orders)
    {  
        // Получим продукты, которые есть во всех выбранных заказах.
        // Так как нам будут нужны названия.
        $products = $this->getProductsForOrders($orders);
        
        $ordersJson = [];
        foreach ($orders as $order) {         
            $ordersJson []= $this->getOrderJson($order, $products);
        }
        
        return $ordersJson;
    }
    
    private function prepareOrderFromStationToJsonEncode($order)
    {
        $products = $this->getProductsForOrder($order);
        return $this->getOrderJson($order, $products);
    }
    
    private function getProductsForOrders($orders) 
    {
        $productIds = [];
        foreach ($orders as $order) {
            foreach ($order->orderItemLogs as $orderItemLog) {
                $productIds []= $orderItemLog->product_id;
            }
        }
        $productIds = array_unique($productIds);
        $products = OrdersApi::getProductsByIds($productIds);
        $products = ArrayHelper::index($products, 'id');
        return $products;
    }
    
    private function getProductsForOrder($order) 
    {
        return $this->getProductsForOrders([$order]);
    }
    
    private function getOrderJson($order, $products)
    {
        $orderJson = new \stdClass();
        $orderSelctableFields = OrderItemLog::getOrderSelectableFields();
        foreach ($orderSelctableFields as $fieldName) {
            $orderJson->$fieldName = $order->$fieldName;
        }
            
        $orderJson->orderItemLogs = $this->getOrdersItemLogJson($order, $products);
        return $orderJson;
    }
    
    private function getOrdersItemLogJson($order, $products) {
        if (count($order->orderItemLogs) === 0) {
            return [];
        }
        
        $orderItemLogAttributes = $order->orderItemLogs[0]->attributes();
        $orderItemLogsJson = [];
        foreach ($order->orderItemLogs as $orderItemLog) {
            $orderItemLogJson = new \stdClass();
            foreach ($orderItemLogAttributes as $attributeName) {
                $orderItemLogJson->$attributeName = $orderItemLog->$attributeName;
            }
            // Добавим количество и название продукта.
            $orderItemLogJson->quantity = $orderItemLog->orderItem->quantity;
            $orderItemLogJson->productName = $products[$orderItemLog->product_id]->name;
            //$orderItemLogJson->orderItem = $orderItemLog->orderItem;
            $orderItemLogsJson []= $orderItemLogJson;
        }
        
        return $orderItemLogsJson;
    }

}
