<?php

namespace app\modules\clients\controllers;

use Yii;

use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use app\modules\clients\models\Client;
use app\modules\clients\models\Address;
use app\modules\clients\models\ClientAddress;
use app\modules\clients\models\Bonuscard;
use app\modules\clients\models\BonuscardType;
use app\modules\clients\models\form\ClientForm;
use app\modules\clients\models\search\ClientSearch;
use app\modules\clients\models\search\AddressForClientSearch;
use app\modules\clients\exceptions\CanNotBeDeletedException;

class ClientController extends DefaultController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'address-delete' => ['post'],
                    'address-unbind' => ['post'],
                    'bonuscard-delete' => ['post'],
                ],
            ],
        ];
    }
    
    public function actionIndex()
    {
        $searchModel = new ClientSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'haveList' => $this->getHaveList(),
        ]);
    }

    public function actionView($id)
    {
        $searchModel = new AddressForClientSearch();
        $dataProvider = $searchModel->search($id, Yii::$app->request->queryParams);

        return $this->render('view', [
            'model' => $this->findClientModel($id),
            'citiesList' => $this->getCitiesList(),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new ClientForm();
        $model->scenario = ClientForm::SCENARIO_CREATE;

        if (
            $model->load(Yii::$app->request->post()) && 
            $model->save()
        ) {
            return $this->redirect(['view', 'id' => $model->id]);
        }
        
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findClientFormModel($id);
        $model->scenario = ClientForm::SCENARIO_UPDATE;
        
        if (
            $model->load(Yii::$app->request->post()) && 
            $model->save()
        ) {
            return $this->redirect(['view', 'id' => $model->id]);
        }
        
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findClientModel($id)->delete();
        return $this->redirect(['index']);
    }

    public function actionAddressView($clientId, $addressId)
    {
        $clientModel = $this->findClientModel($clientId);
        $addressModel = $this->findAddressModel($addressId);
        $clientAddressModel = $this->findClientAddressModel($clientId, $addressId);
        
        return $this->render('addressView', [
            'clientModel' => $clientModel,
            'addressModel' => $addressModel,
            'clientAddressModel' => $clientAddressModel,
        ]);
    }
    
    public function actionAddressCreate($clientId)
    {
        $clientModel = $this->findClientModel($clientId);
        $addressModel = new Address();
        $clientAddressModel = new ClientAddress();
        
        $post = Yii::$app->request->post();
        
        if (
            $addressModel->load($post) && 
            $clientAddressModel->load($post)
        ) {
            $success = Address::createAddressForClient($addressModel, $clientAddressModel, $clientId);
            if ($success) {
                return $this->redirect(['address-view', 'clientId' => $clientId, 'addressId' => $addressModel->id]);                
            }
        }
        
        return $this->render('addressCreate', [
            'clientModel' => $clientModel,
            'addressModel' => $addressModel,
            'clientAddressModel' => $clientAddressModel,
            'citiesList' => $this->getCitiesList(),
        ]);
    }

    public function actionAddressUpdate($clientId, $addressId)
    {
        $clientModel = $this->findClientModel($clientId);
        $addressModel = $this->findAddressModel($addressId);
        $clientAddressModel = $this->findClientAddressModel($clientId, $addressId);
        
        $post = Yii::$app->request->post();
        
        if ($addressModel->load($post) && $clientAddressModel->load($post)) {
            $success = Address::updateAddressForClient($addressModel, $clientAddressModel);
            if ($success) {
                return $this->redirect(['address-view', 'clientId' => $clientId, 'addressId' => $addressModel->id]);                
            }
        }
        
        return $this->render('addressUpdate', [
            'clientModel' => $clientModel,
            'addressModel' => $addressModel,
            'clientAddressModel' => $clientAddressModel,
            'citiesList' => $this->getCitiesList(),
        ]);
    }
    
    public function actionAddressUnbind($clientId, $addressId)
    {
        $this->findClientAddressModel($clientId, $addressId)->delete();
        return $this->redirect(['view', 'id' => $clientId]);
    }
    
    public function actionAddressDelete($clientId, $addressId)
    {
        $address = $this->findAddressModel($addressId);
        try {
            $success = Address::deleteAddressForClient($address, $clientId);
            if ($success) {
                return $this->redirect(['view', 'id' => $clientId]);
            }
        }
        catch (CanNotBeDeletedException $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect([
                'address-view', 
                'addressId' => $addressId, 
                'clientId' => $clientId,
            ]); 
        }
    }
    
    public function actionBonuscardCreate($clientId)
    {
        $clientModel = $this->findClientModel($clientId);
        $bonuscardModel = new Bonuscard();
        
        if ($clientModel->bonuscard !== null) {
            throw new ForbiddenHttpException();
        }
        
        if ($bonuscardModel->load(Yii::$app->request->post())) {
            $success = Bonuscard::createBonuscard($clientModel, $bonuscardModel);
            if ($success) {
                Yii::$app->session->setFlash('success', 'Создание бонусной карты успешно.');
                return $this->redirect(['view', 'id' => $clientModel->id]);
            }
        }
        
        return $this->render('bonuscardCreate', [
            'clientModel' => $clientModel,
            'bonuscardModel' => $bonuscardModel,
            'bonuscardTypesList' => $this->getBonuscardTypesList(),
        ]);
    }
    
    public function actionBonuscardUpdate($clientId)
    {
        $clientModel = $this->findClientModel($clientId);
        $bonuscardModel = $clientModel->bonuscard;
        
        if ($bonuscardModel === null) {
            throw new ForbiddenHttpException();
        }
        
        if (
            $bonuscardModel->load(Yii::$app->request->post()) &&
            $bonuscardModel->save()
        ) {
            Yii::$app->session->setFlash('success', 'Обновление бонусной карты успешно.');
            return $this->redirect(['view', 'id' => $clientModel->id]);
        }
        
        return $this->render('bonuscardUpdate', [
            'clientModel' => $clientModel,
            'bonuscardModel' => $bonuscardModel,
            'bonuscardTypesList' => $this->getBonuscardTypesList(),
        ]);
    }
    
    public function actionBonuscardDelete($clientId)
    {
        $clientModel = $this->findClientModel($clientId);
        $clientModel->bonuscard->delete();
        return $this->redirect(['view', 'id' => $clientModel->id]);
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
    
    private function findClientFormModel($id)
    {
        $model = ClientForm::findOne(['id' => $id]);
        if ($model === null) {
             throw new NotFoundHttpException('The requested page does not exist.');
        }
        
        return $model;
    }
    
    private function getBonuscardTypesList()
    {
        $bonuscardTypes = BonuscardType::find()->all();
        return ArrayHelper::map($bonuscardTypes, 'id', 'name');
    }
 
}
