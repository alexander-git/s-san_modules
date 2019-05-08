<?php

namespace app\modules\orders\controllers;

use Yii;
use yii\web\Response;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

use app\modules\orders\behaviors\ProductsPreparationBehavior;
use app\modules\orders\models\Order;
use app\modules\orders\helpers\DateHelper;
use app\modules\orders\models\Stage;
use app\modules\orders\models\PaymentType;
use app\modules\orders\models\OptionVal;
use app\modules\orders\models\LogRecord;
use app\modules\orders\models\OrdersApi;
use app\modules\orders\models\form\NewOrderForm;
use app\modules\orders\models\form\OrderAddressForm;
use app\modules\orders\models\search\OrderSearch;
use app\modules\orders\models\search\LogRecordSearch;

class OrderController extends DefaultController
{    
    public function behaviors()
    {
        return [
            'productsPreparation' => [
                'class' => ProductsPreparationBehavior::className(),
                'productImageUrlPrefix' => $this->module->productImageUrlPrefix,
                'productImageUrlSuffix' => $this->module->productImageUrlSuffix,
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'add-to-cart' => ['post'],
                    'pay-by-bonuses' => ['post'],
                    'delivery-price-upadate' => ['post'],
                    'delivery-date-time-upadate' => ['post'],
                ],
            ],
        ];
    }
    
    public function actionIndex()
    {
        $post = Yii::$app->request->post();
        $get = Yii::$app->request->queryParams;
        
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        $newOrderFormModel = new NewOrderForm();
        if (
            $newOrderFormModel->load($post) &&
            $newOrderFormModel->validate()
        ) {
            $orderModel = new Order();
            $orderModel->phone = $newOrderFormModel->phone;
            $orderModel->user_id = OrdersApi::getCurrentUserId();
            $orderModel->client_id = OrdersApi::getClientIdByPhone($newOrderFormModel->phone);
            
            $success = Order::createNewOrder($orderModel);
            if ($success) {
                return $this->redirect(['menu', 'orderId' => $orderModel->id]);   
                
            }    
        }
        
        if (isset($get['phoneNum'])) {
            $newOrderFormModel->phone = $get['phoneNum'];
        } 
        if (isset($post['phoneNum'])) {
            $newOrderFormModel->phone = $post['phoneNum'];
        }
        
        return $this->render('index', [
            'newOrderFormModel' => $newOrderFormModel,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'citiesList' => $this->getCitiesList(),
            'stagesList' => $this->getStagesList(),
            'paymentTypesList' => $this->getPaymentTypesList(),
            'newStageId' => Stage::getNewStageId(),
        ]);
    }

    public function actionNewOrderFormValidate() 
    {
        $newOrderFormModel = new NewOrderForm();
        if (
            Yii::$app->request->isAjax && 
            $newOrderFormModel->load(Yii::$app->request->post())
        ) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($newOrderFormModel);
        }
    }
    
    public function actionMenu($orderId)
    {
        $get = Yii::$app->request->queryParams;
        
        $orderModel = $this->findOrderModel($orderId);
        $citiesList = $this->getCitiesList();
                
        $cityId = $this->processAndGetCityId($orderModel);
        if ($cityId === null) {
            return $this->render('citySelect', [
                'orderModel' => $orderModel,
                'citiesList' => $citiesList
            ]);
        }
             
        $cityName = $citiesList[$cityId];
        $categories = ArrayHelper::index(OrdersApi::getCategoriesByCityName($cityName), 'id');
        $propertiesAndValues = OrdersApi::getPropertiesAndValues();
        $properties = ArrayHelper::index($propertiesAndValues->properties, 'id');
        $values = ArrayHelper::index($propertiesAndValues->values, 'id');
        
        $renderParams = [];
        $renderType = null;
        if (isset($get['categoryId'])) {
            $categoryId = (int) $get['categoryId'];
            $products = OrdersApi::getProductsByCategoryId($categoryId);
            $renderParams['categoryId'] = $categoryId;
            $renderType = 'category';
        } elseif (isset($get['search'])) {
            $name = $get['search'];
            $products = OrdersApi::getProductsByName($name, $cityName);
            $renderType = 'search';
        } else {
            $products = OrdersApi::getProducts($cityName);
            $renderType = 'all';
        }
        
        $this->prepareProductsToRender($products, $properties, $values);
        
        if ($renderType === 'category' || $renderType === 'all') {
            $products  = $this->divideProductsByCategories($products);
        }
                
        $renderParams['orderModel'] = $orderModel;
        $renderParams['cityName'] = $cityName;
        $renderParams['citiesList'] = $citiesList;
        $renderParams['categories'] = $categories;
        $renderParams['products'] = $products;
        $renderParams['renderType'] = $renderType;
        
        return $this->render('menu', $renderParams);
    }
        
    public function actionAddToCart($orderId)
    {
        if (!Yii::$app->request->isAjax) {
            throw new ForbiddenHttpException();
        }
        $post = Yii::$app->request->post();
        $productId = $post['productId'];
        
        $orderModel = $this->findOrderModel($orderId);
        
        if (!Order::addProductToOrder($orderModel, $productId)) {
            throw new \Exception('Произошла ошибка.');
        }
            
        $result = new \stdClass();
        $result->productsCount = $orderModel->items_count;
        $result->totalPrice = $orderModel->total_price;
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }
    
    public function actionCart($orderId) 
    {
        $orderModel = $this->findOrderModel($orderId);
        
        $post = Yii::$app->request->post();
        if (isset($post['saveCartButton'])) {
            if (isset($post['productCounts'])) {
                $productCounts = $post['productCounts'];
            } else {
                $productCounts = [];
            }
            if (Order::updateOrderItems($orderModel, $productCounts)) {
                return $this->redirect(['client-name', 'orderId' => $orderModel->id]);
            } else {
                Yii::$app->session->set('error', 'Произошла ошибка.');
            }
        }
        
        // Получим информацию о существующих продуктах, так как нам 
        // нужно отображать картинку и имя.
        $orderItems = $orderModel->getOrderItems()->all();
        $productIds = [];
        foreach ($orderItems as $orderItem) {
            $productIds []=  $orderItem->product_id;
        }
        if (count($productIds) > 0) { 
            $products = OrdersApi::getProductsByIds($productIds);
            $products = $this->prepareProductsImageUrl($products);
            $products = ArrayHelper::index($products, 'id');
        } else {
            $products = [];
        }
        
        return $this->render('cart', [
            'orderModel' => $orderModel,
            'orderItems' => $orderItems,
            'products' => $products,
        ]);
    }
    
    public function actionClientName($orderId)
    {
        $orderModel = $this->findOrderModel($orderId);
        $orderModel->scenario = Order::SCENARIO_NAME_INPUT;
        
        $clientModel = $this->getClientById($orderModel->client_id);
        
        if ($orderModel->load(Yii::$app->request->post())) {   
            if (Order::saveChangesInOrder($orderModel)) {
                return $this->redirect(['client-phone', 'orderId' =>$orderModel->id]);
            }
        }
        
        return $this->render('clientName', [
            'orderModel' => $orderModel,
            'clientModel' => $clientModel,
        ]);
    }
    
    public function actionClientPhone($orderId)
    {
        $orderModel = $this->findOrderModel($orderId);
        $orderModel->scenario = Order::SCENARIO_PHONE_INPUT;
        
        $clientModel = $this->getClientById($orderModel->client_id);
        
        if ($orderModel->load(Yii::$app->request->post())) {
            if (Order::saveChangesInOrder($orderModel)) {
                return $this->redirect(['client-address', 'orderId' =>$orderModel->id]);
            }
        }
        
        return $this->render('clientPhone', [
            'orderModel' => $orderModel,
            'clientModel' => $clientModel,
        ]);
    }
    
    public function actionClientAddress($orderId)
    {
        $orderModel = $this->findOrderModel($orderId);
        $orderModel->scenario = Order::SCENARIO_ADDRESS_INPUT;
        
        $cityName = OrdersApi::getCityNameById($orderModel->city_id);
        
        $orderAddressFormModel = new OrderAddressForm();
        if (!empty($orderModel->address_json)) {
            $orderAddressFormModel->fillOnJson($orderModel->address_json);
        }
        
        if (
            $orderAddressFormModel->load(Yii::$app->request->post()) &&
            $orderAddressFormModel->validate()
        ) {
            $orderModel->address = $orderAddressFormModel->getAddressAsSting($cityName);
            $orderModel->address_json = $orderAddressFormModel->getAddressJson($cityName);
            if (Order::saveChangesInOrder($orderModel)) {
                return $this->redirect(['bonuses', 'orderId' => $orderModel->id]);
            }
        }
        
        // Если адресс задавался не через эту форму ранее, то покажем его 
        // пользователю. В этом случае значение в address_json и поля в форме
        // $orderAddressFormModel будет пустыми. Хотя адрес всё равно уже 
        // существует.
        $needShowCurrentAddress = !empty($orderModel->address) &&
            empty($orderModel->address_json);
                
        return $this->render('clientAddress', [
            'orderModel' => $orderModel,
            'cityName' => $cityName,
            'orderAddressFormModel' => $orderAddressFormModel,
            'needShowCurrentAddress' => $needShowCurrentAddress,
        ]);
    }
    
    public function actionBonuses($orderId)
    {
        $orderModel = $this->findOrderModel($orderId);
        $bonuscardModel = $this->getBonuscardByClientId($orderModel->client_id);
               
        return $this->render('bonuses', [
            'orderModel' => $orderModel,
            'bonuscardModel' => $bonuscardModel,
        ]);
    }
    
    
    public function actionPayByBonuses($orderId)
    {
        $orderModel = $this->findOrderModel($orderId);
        $post = Yii::$app->request->post();
        $tax = $post['tax'];
        
        $success = Order::setTaxFromBonusesInOrder($orderModel, $tax);
        
        $response = new \stdClass();
        if ($success) {     
            $response->success = true;
            // Также перешлём сумму бонусов оставшуюся на счету, чтобы 
            // обновить её на странице.
            $bonuscard = OrdersApi::getBonuscardByClientId($orderModel->client_id);
            $response->bonuses = $bonuscard->bonuses; 
        } else {
            $response->success = false;
            $errorMessage = $this->getErrorMessageOnModel($orderModel);
            if ($errorMessage !== null) {
                $response->errorMessage = $errorMessage;
            }
        }
                 
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }
     
    public function actionInfo($orderId)
    {
        $orderModel = $this->findOrderModel($orderId);
        $orderModel->scenario = Order::SCENARIO_INFO_INPUT;
        $post = Yii::$app->request->post();
        
        if ($orderModel->load($post)) {
            $succsess = Order::saveInfoInOrder($orderModel);
            if ($succsess) {
                return $this->redirect(['summary', 'orderId' => $orderModel->id]); 
            }
        }
        
        $cityId = $orderModel->city_id;
        
        return $this->render('info', [
            'orderModel' => $orderModel,
            'paymentTypesList' => $this->getPaymentTypesList(),
            'dayButtonItems' => $this->getDayBottonItems(),
            'timeHourButtonItems' => $this->getTimeHourButtonItems($cityId),
            'timeMinuteButtonItems' => $this->getTimeMinuteButtonItems($cityId ),
            'minPossibleDeliveryTime' => $this->getMinPossibleDeliveryTime($cityId),
            'maxPossibleDeliveryTime' => $this->getMaxPossibleDeliveryTime($cityId),
        ]);
    }

    public function actionSummary($orderId)
    {        
        $orderModel = $this->findOrderModel($orderId);
        $logRecordModel = new LogRecord();
  
        if ($orderModel->deliveryInfo === null) {
            Order::updateDeliveryPriceInOrder($orderModel->id);
            Order::updateDeliveryDateTimeInOrder($orderModel->id);
            $orderModel = $this->findOrderModel($orderModel->id);
        } elseif (
            $orderModel->deliveryInfo !== null &&  
            $orderModel->deliveryInfo->price === null
        ) {
            Order::updateDeliveryPriceInOrder($orderModel->id);
            $orderModel = $this->findOrderModel($orderModel->id);
        }
        
        $post = Yii::$app->request->post();
        if (!$orderModel->isCanceled) {
            $isAccept = null; 
            if (isset($post['acceptButton'])) {
                $isAccept = true;
            } elseif (isset($post['cancelButton'])) {
                $logRecordModel->scenario = LogRecord::SCENARIO_COMMENT_REQUIRED;
                $isAccept = false;
            }
                       
            if ($logRecordModel->load($post) && $isAccept !== null) {
                $previousStageId = $orderModel->stage_id;
                if ($isAccept) {
                    $success = Order::acceptOrder($orderModel, $logRecordModel);
                } else {
                    $success = Order::cancelOrder($orderModel, $logRecordModel);
                }
                
                if ($success) {
                    return $this->redirect(['index']);
                } else {
                    // Восстановим состояние заказа так нам это нужно для 
                    // правильного отображения элементов на странице.
                    $orderModel->stage_id = $previousStageId;
                }
            }
        }
        
        return $this->render('summary', [
            'orderModel' => $orderModel,
            'logRecordModel' => $logRecordModel,
        ]);
    }
    
    public function actionDeliveryPriceUpdate($orderId)
    {
        $orderModel = $this->findOrderModel($orderId);
        Order::updateDeliveryPriceInOrder($orderModel->id);
        return $this->redirect(['summary', 'orderId' => $orderModel->id]);
    }
    
    public function actionDeliveryDateTimeUpdate($orderId)
    {
        $orderModel = $this->findOrderModel($orderId);
        Order::updateDeliveryDateTimeInOrder($orderModel->id);
        return $this->redirect(['summary', 'orderId' => $orderModel->id]);
    }
              
    public function actionView($orderId)
    {
        $orderModel = $this->findOrderModel($orderId);
        
        $searchModel = new LogRecordSearch();
        $dataProvider = $searchModel->search($orderId, Yii::$app->request->queryParams);
        $logRecordsCount = $orderModel->getLogRecords()->count();
        
        return $this->render('view', [
            'orderModel' => $orderModel,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'stagesList' => $this->getStagesList(),
            'logRecordsCount' => $logRecordsCount,
        ]);
    }
    
    public function actionUpdate($orderId)
    {
        $orderModel = $this->findOrderModel($orderId);
        $orderModel->scenario = Order::SCENARIO_UPDATE;
        
        if ($orderModel->load(Yii::$app->request->post())) {
            $success = Order::updateOrder($orderModel);
            if ($success) {
                return $this->redirect(['view', 'orderId' => $orderId]);
            }
        }
        
        return $this->render('update', [
            'orderModel' => $orderModel,
            'paymentTypesList' => $this->getPaymentTypesList(),
            'citiesList' => $this->getCitiesList(),
        ]);
    }
    

    public function actionDelete($orderId)
    {
        $this->findOrderModel($orderId)->delete();
        return $this->redirect(['index']);
    } 
    
    
    private function processAndGetCityId($orderModel)
    {
        $get = Yii::$app->request->queryParams;
        // Если город передан в get-параметре установим его у заказа 
        // и запомним.
        if (isset($get['cityId'])) {
            $cityId = (int) $get['cityId'];
            $success = Order::setCityInOrder($orderModel, $cityId);
            if ($success) {
                // Запомниаем id города. Чтобы в будущем при создании
                // нового заказа этот город был выбран по умолчанию.
                Yii::$app->session->set('cityId', $cityId);
                return $cityId;
            }    
        }
        
        if ($orderModel->city_id !== null) {
            return $orderModel->city_id;
        }
        
        $sessionCityId = Yii::$app->session->get('cityId', null);
        if ($sessionCityId !== null) {
            // В сессии сохранён город - установим его у нового заказа.
            $success = Order::setCityInOrder($orderModel, $sessionCityId);
            if (!$success) {
                return null;
            }
        }
        
        return $sessionCityId;
    }
    
    private function findOrderModel($id)
    {
        $model = Order::findOne(['id' => $id]);
        if ($model === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        } 
        
        return $model;
    }
        
    private function getStagesList()
    {
        $stages = Stage::find()->orderBy(['sort' => SORT_ASC])->all();
        return ArrayHelper::map($stages, 'id', 'name');
    }
    
    private function getPaymentTypesList()
    {
        $paymentTypes = PaymentType::find()->orderBy(['sort' => SORT_ASC])->all();
        return ArrayHelper::map($paymentTypes, 'id', 'name');
    }
        
    private function getClientById($id)
    {
        if ($id === null) {
            return null;
        }
        return OrdersApi::getClientById($id);
    }
    
    private function getBonuscardByClientId($clientId)
    {
        if ($clientId === null) {
            return null;
        }
        return OrdersApi::getBonuscardByClientId($clientId);    
    }
        
    private function getDayBottonItems()
    {
        $date = new \DateTime();
        $i = 1;
        $result = [];
        for ($i = 1; $i <= 5; $i++) {
            $key = $date->format('d-m');
            $value = $date->format(Order::DATE_FORMAT);
            $result[$key] = $value;
            DateHelper::incDayInDate($date);
        }
        return $result;
    }
    
    public function getTimeHourButtonItems($cityId)
    {
        $minHours = OptionVal::getMinPossibleDeliveryTimeHoursValue($cityId);
        $maxHours = OptionVal::getMaxPossibleDeliveryTimeHoursValue($cityId);
        if ($minHours === null || $maxHours === null) {
            return [];
        }
        
        $result = [];
        for ($i = $minHours; $i <= $maxHours; $i++) {
            if ($i < 10) {
                $value = '0'.$i;
            } else {
                $value = $i;
            }
            $result[$i] = $value;
        }
        return $result;
    }
    
    public function getTimeMinuteButtonItems()
    {
        $result = [];
        $result['0'] = '00';
        for ($i = 1; $i <= 5; $i++) {
            $key = $i * 10;
            $result[$key] = $key;
        }
        return $result;
    }
    
    private function getMinPossibleDeliveryTime($cityId)
    {
        return OptionVal::getMinPossibleDeliveryTimeValue($cityId);
    }
    
    private function getMaxPossibleDeliveryTime($cityId)
    {
        return OptionVal::getMaxPossibleDeliveryTimeValue($cityId);
    }
    
    private function getErrorMessageOnModel($model)
    {
        $errors = $model->getFirstErrors();
        if (count($errors) === 0) {
            $errorMessage = null;
        } else {
            $errorMessage = $errors[0];
        }
        return $errorMessage;
    }
    
    /*
    // Промокоды.
    public function actionSendPromoCode($orderId)
    {
        $orderModel = $this->findOrderModel($orderId);
        $post = Yii::$app->request->post();
        if (!isset($post['code'])) {
            throw new ForbiddenHttpException();
        }

        if (count($orderModel->orderItems) === 0) {
            // Заказ не должен быть пустым.
            throw new ForbiddenHttpException();
        }
        
        // Должен быть клиент с бонусной картой.
        //if ($orderModel->client_id === null) {
        //    throw new ForbiddenHttpException();
        //}
        //$bonuscard = $this->getBonuscardByClientId($orderModel->client_id);
        //if ($bonuscard === null) {
        //    throw new ForbiddenHttpException();
        //} 
        

        $orderInfo = $this->prepareOrderInfoForSendBonusesCode($orderModel);
        
        $resultJson = OrdersApi::sendPromoCode($post['code'], $orderInfo);
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $response = new \stdClass();
        $response->success = true;
 
        return $response;
    }
     * 
    private function prepareOrderInfoForSendBonusesCode($orderModel)
    {
        $orderInfo = new \stdClass();
        $orderInfo->id = $orderModel->id;
        $orderInfo->items_count = $orderModel->items_count;
        $orderInfo->total_price = $orderModel->total_price;
        $orderInfo->orderItems = $orderModel->orderItems;
        return $orderInfo;
    }
    */
      
}
