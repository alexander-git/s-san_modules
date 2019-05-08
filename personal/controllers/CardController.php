<?php

namespace app\modules\personal\controllers;

use Yii;

use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\UploadedFile;
use yii\base\Model;
use app\modules\personal\models\Card;
use app\modules\personal\models\DocsList;
use app\modules\personal\models\CardDocs;
use app\modules\personal\models\search\CardSearch;
use app\modules\personal\models\search\DocsListCardSearch;
use app\modules\personal\models\form\DocUploadForm;

class CardController extends DefaultController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function getListsForRender()
    {
        return [
            'departmentsList' => $this->getDepartmentsList(),
            'settingsPostsList' => $this->getSettingsPostsList(),
            'haveList' => $this->getHaveList(),
            'yesNoList' => $this->getYesNoList(),
        ];
    }
    
    public function actionIndex()
    {
        $searchModel = new CardSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'departmentsList' => $this->getDepartmentsList(),
            'settingsPostsList' => $this->getSettingsPostsList(),
            'haveList' => $this->getHaveList(),
            'yesNoList' => $this->getYesNoList(),
        ]);
    }
        
    public function actionCreate()
    {
        $model = new Card();

        if ($model->load(Yii::$app->request->post())) {
            $success = Card::createCard($model);
            if ($success) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        
        return $this->render('create', [
            'model' => $model,
        ]);
    }
    
    public function actionView($id)
    {
        $searchModel = new DocsListCardSearch();
        $dataProvider = $searchModel->search($id, Yii::$app->request->queryParams);
        
        return $this->render('view', [
            'model' => $this->findCardModel($id),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'yesNoList' => $this->getYesNoList(),
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findCardModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $success = Card::updateCard($model);
            if ($success) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        
        return $this->render('update', [
            'model' => $model,
        ]);
    }
    
    public function actionUpdateDocsList($id)
    {
        $model = $this->findCardModel($id);
        
        // Выберем обычные документы.
        $docsListDefaultModels = DocsList::find()
            ->where(['type' => DocsList::TYPE_DEFAULT])
            ->indexBy('id')
            ->all();
        
        $docsIdsDefault = array_keys($docsListDefaultModels);
        
        $cardDocsModels = CardDocs::find()
            ->with(['docsList'])
            ->where(['card_id' => $id])
            ->andWhere(['in', 'docs_id', $docsIdsDefault])
            ->indexBy('docs_id')
            ->all();
        
        $allDocsListDefault = DocsList::find()
            ->where(['in', 'id', $docsIdsDefault])
            ->indexBy('id')
            ->all();
        
        foreach (array_diff_key($allDocsListDefault, $cardDocsModels) as $docsList) {
            $cardDocs = new CardDocs();
            $cardDocs->docs_id = $docsList->id;
            $cardDocs->card_id = $id;
            $cardDocsModels[$docsList->id] = $cardDocs;
        }
        
        if (
            Model::loadMultiple($cardDocsModels, Yii::$app->request->post()) && 
            Model::validateMultiple($cardDocsModels)
        ) {
            foreach ($cardDocsModels as $cardDocsModel) {
                $cardDocsModel->card_id = $model->id;
                if ($cardDocsModel->validate()) {
                    if ($cardDocsModel->check) {
                        $cardDocsModel->save();
                    } else {
                        $cardDocsModel->delete();
                    }
                }
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }
    
        // Отсортируем по id.
        ksort($cardDocsModels);
        
        return $this->render('updateDocsList', [
            'model' => $model,
            'cardDocsModels' => $cardDocsModels,
            'docsListDefaultModels' => $docsListDefaultModels,
        ]);
    }

    public function actionDelete($id)
    {
        $cardModel = $this->findCardModel($id);
        $success = Card::deleteCard($cardModel, $this->getDocumentsPath());
        if (!$success) {
            Yii::$app->session->setFlash('error', 'Произогла ошибка');
        }

        return $this->redirect(['index']);
    }
    
    public function actionCardDocsCreate($docsId, $cardId)
    {
        $cardModel = $this->findCardModel($cardId);
        $docsListModel = $this->findDocsListModel($docsId);
        
        if ($docsListModel->isTypeDefault) {
            $model = new CardDocs();
            $success = CardDocs::createDocDefault($model, $docsId, $cardId);
            if (!$success) {
                Yii::$app->session->setFlash('error', 'Произошла ошибка!');
            }
            return $this->redirect(['view', 'id' => $cardId]);
        }
        
        if (!$docsListModel->isTypeLoadable) {
            throw new \LogicException();
        }
        
        $cardDocsModel = new CardDocs();
        $docUploadFormModel = new DocUploadForm();
        
        if (Yii::$app->request->isPost) {
            $docUploadFormModel->file = UploadedFile::getInstance($docUploadFormModel, 'file');
            $success = CardDocs::createDocLoadable(
                $cardDocsModel, 
                $docUploadFormModel,
                $docsId, 
                $cardId, 
                $this->getDocumentsPath()
            );
            if ($success) {
                return $this->redirect(['view', 'id' => $cardId]);  
            }
        }
        
        return $this->render('cardDocsCreate', [
            'cardModel' => $cardModel,
            'docsListModel' => $docsListModel,
            'docUploadFormModel' => $docUploadFormModel,
        ]);
    }
    
    public function actionCardDocsUpdate($docsId, $cardId)
    {
        $cardModel = $this->findCardModel($cardId);
        $cardDocsModel = $this->findCardDocsModel($docsId, $cardId);
        $docsListModel = $cardDocsModel->docsList;
        $docUploadFormModel = new DocUploadForm();
        
        if (!$docsListModel->isTypeLoadable) {
            throw new ForbiddenHttpException();
        }
        
        if (Yii::$app->request->isPost) {
            $docUploadFormModel->file = UploadedFile::getInstance($docUploadFormModel, 'file');
            $success = CardDocs::updateDocLoadable(
                $cardDocsModel, 
                $docUploadFormModel,
                $this->getDocumentsPath()
            );
            if ($success) {
                return $this->redirect(['view', 'id' => $cardId]);  
            }
        }
        
        return $this->render('cardDocsUpdate', [
            'cardModel' => $cardModel,
            'cardDocsModel' => $cardDocsModel,
            'docsListModel' => $docsListModel,
            'docUploadFormModel' => $docUploadFormModel,
            'documentsUrl' => $this->getDocumentsUrl(),
        ]);
    }
    
    public function actionCardDocsView($docsId, $cardId)
    {
        $cardDocsModel = $this->findCardDocsModel($docsId, $cardId);
        if (!$cardDocsModel->docsList->isTypeLoadable) {
            throw new ForbiddenHttpException();
        }
        
        return $this->render('cardDocsView', [
            'cardDocsModel' => $cardDocsModel,
            'documentsUrl' => $this->getDocumentsUrl(),
        ]); 
    }
    
    public function actionCardDocsDelete($docsId, $cardId)
    {
        $cardDocsModel = $this->findCardDocsModel($docsId, $cardId);
        $success = CardDocs::deleteDoc($cardDocsModel, $this->getDocumentsPath());
        if (!$success) {
            Yii::$app->session->setFlash('error', 'Произошла ошибка!');
        }
        return $this->redirect(['view', 'id' => $cardId]);    
    }

    public function actionUpdateMedbook($cardId)
    {
        $cardModel = $this->findCardModel($cardId);
        $medbookModel = $cardModel->medbook;
        
        if ($medbookModel === null) {
            throw new ForbiddenHttpException();
        }
        
        if (
            $medbookModel->load(Yii::$app->request->post()) &&
            $medbookModel->save()
        ) {
            return $this->redirect(['view', 'id' => $cardId]);    
        }
        
        return $this->render('updateMedbook', [
            'cardModel' => $cardModel,
            'medbookModel' => $medbookModel,
        ]);
    }
       
    private function findCardModel($id)
    {
        $model = Card::findOne(['id' => $id]);
        if ($model === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        
        return $model;
    }
    
    private function findCardDocsModel($docsId, $cardId)
    {
        $model = CardDocs::findOne(['docs_id' => $docsId, 'card_id' => $cardId]);
        if ($model === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        
        return $model;
    }
    
    private function findDocsListModel($id)
    {
        $model = DocsList::findOne(['id' => $id]);
        if ($model === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        
        return $model;
    }
        
}
