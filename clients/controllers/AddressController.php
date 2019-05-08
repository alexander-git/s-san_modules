<?php

namespace app\modules\clients\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use app\modules\clients\models\Client;
use app\modules\clients\models\Address;
use app\modules\clients\models\ClientAddress;
use app\modules\clients\models\search\AddressSearch;
use app\modules\clients\models\search\ClientAddressSearch;

class AddressController extends DefaultController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'client-address-delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new AddressSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'citiesList' => $this->getCitiesList(),
        ]);
    }

    public function actionView($id)
    {
        $searchModel = new ClientAddressSearch();
        $dataProvider = $searchModel->search($id, Yii::$app->request->queryParams);
        
        return $this->render('view', [
            'model' => $this->findAddressModel($id),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new Address();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'citiesList' => $this->getCitiesList(),
            ]);
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findAddressModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'citiesList' => $this->getCitiesList(),
            ]);
        }
    }

    public function actionDelete($id)
    {
        $this->findAddressModel($id)->delete();
        return $this->redirect(['index']);
    }
    
    public function actionClientAddressCreate($addressId)  
    {
        $excludedIds = $this->getExistsClientIdsForAddress($addressId);
        $clientsList = $this->getClientsList($excludedIds);
        
        $clientAddressModel = new ClientAddress();
        
        if ($clientAddressModel->load(Yii::$app->request->post())) {
            $clientAddressModel->addressId = (int) $addressId;
            if ($clientAddressModel->save()) {
                return $this->redirect(['view', 'id' => $addressId]);
            }
        }
        
        return $this->render('clientAddressCreate', [
            'addressModel' => $this->findAddressModel($addressId),
            'clientAddressModel' => $clientAddressModel,
            'clientsList' => $clientsList,
        ]);
    }
    
    public function actionClientAddressUpdate($clientId, $addressId)
    {
        $clientAddressModel = $this->findClientAddressModel($clientId, $addressId);
        
        if ($clientAddressModel->load(Yii::$app->request->post())) {
            $clientAddressModel->clientId = (int) $clientId;
            $clientAddressModel->addressId = (int) $addressId;
            if ($clientAddressModel->save()) {
                return $this->redirect(['view', 'id' => $addressId]);
            }
        }
        
        return $this->render('clientAddressUpdate', [
            'clientModel' =>  $this->findClientModel($clientId),
            'addressModel' => $this->findAddressModel($addressId),
            'clientAddressModel' => $clientAddressModel,
        ]);
    }
        
    public function actionClientAddressDelete($clientId, $addressId)
    {
        $this->findClientAddressModel($clientId, $addressId)->delete();
        return $this->redirect(['view', 'id' => $addressId]);  
    }
 
    private function findClientModel($id)
    {
        $model = Client::findOne(['id' => $id]);
        if ($model === null) {
             throw new NotFoundHttpException('The requested page does not exist.');
        }
        
        return $model;
    }

    private function findAddressModel($id)
    {
        $model = Address::findOne(['id' => $id]);
        if ($model === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        
        return $model;
    }
    
    private function findClientAddressModel($clientId, $addressId)
    {
        $model = ClientAddress::findOne(['clientId' => $clientId, 'addressId' => $addressId]);
        if ($model === null) {
             throw new NotFoundHttpException('The requested page does not exist.');
        }
        
        return $model;
    }
    
    private function getClientsList($excludedIds = []) 
    {
        $query = Client::find()
            ->select(['id', 'name', 'fullname']);
        
        if (count($excludedIds) > 0) {
            $query->where(['not in', 'id', $excludedIds]);
        }
        
        $clients = $query->all();
        return ArrayHelper::map($clients, 'id', 'fullname');
    }
    
    private function getExistsClientIdsForAddress($addressId)
    {
        return ClientAddress::find()
            ->select('clientId')
            ->where(['addressId' => $addressId])
            ->column();
    }
        
}
