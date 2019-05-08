<?php

namespace app\modules\personal\controllers;

use Yii;

use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use app\modules\personal\models\PersonalApi;
use app\modules\personal\models\Question;
use app\modules\personal\models\QuestionHistory;
use app\modules\personal\models\search\QuestionSearch;
use app\modules\personal\models\search\QuestionHistorySearch;
use app\modules\personal\models\AboutUsValue;

class QuestionController extends DefaultController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'make-medbook' => ['post'],
                    'make-medbook-complete' => ['post'],
                ],
            ],
        ];
    }
    
    public function getListsForRender()
    {
        return [
            'settingsPostsList' => $this->getSettingsPostsListWithEmptyItem(),
            'aboutUsValuesList' => $this->getAboutUsValuesListWithEmptyItem(),
            'citiesList' => $this->getCitiesListWithEmptyItem(),
            'haveList' => $this->getHaveList(),
            'yesNoList' => $this->getYesNoList(),
        ];
    }

    public function actionIndex()
    {
        $searchModel = new QuestionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'settingsPostsList' => $this->getSettingsPostsList(),
            'aboutUsValuesList' => $this->getAboutUsValuesList(),
            'citiesList' => $this->getCitiesList(),
            'yesNoList' => $this->getYesNoList(),
            'haveList' => $this->getHaveList(),
        ]);
    }

    public function actionCreate()
    {
        $questionModel = new Question();
        $questionModel->scenario = Question::SCENARIO_CREATE;
                
        if ($questionModel->load(Yii::$app->request->post())) {
            $success = Question::createQuestion($questionModel);
            if ($success) {
                return $this->redirect(['view', 'id' => $questionModel->id]);
            }
        } 
        
        return $this->render('create', [
            'questionModel' => $questionModel,
        ]);
    }
    
    public function actionCallback($id) 
    {
        $questionModel = $this->findModel($id);
        $questionHistoryModel = new QuestionHistory();
        $questionModel->scenario = Question::SCENARIO_CALLBACK;
        $questionHistoryModel->scenario = QuestionHistory::SCENARIO_TEXT_REQUIRED;
        
        if (
            !$questionModel->isCreated && 
            !$questionModel->isReserve && 
            !$questionModel->isCallback
        ) {
            throw new ForbiddenHttpException();
        }
        
        $post = Yii::$app->request->post();
        if (
            $questionModel->load($post) &&
            $questionHistoryModel->load($post)
        ) {
            $success = Question::callbackQuestion($questionModel, $questionHistoryModel);
            if ($success) {
                return $this->redirect(['view', 'id' => $questionModel->id]);
            }
        }
        
        return $this->render('callback', [
            'questionModel' => $questionModel,
            'questionHistoryModel' => $questionHistoryModel,
        ]);
    }
    
    
    public function actionReserve($id) 
    {
        $questionModel = $this->findModel($id);
        $questionHistoryModel = new QuestionHistory();
        $questionModel->scenario = Question::SCENARIO_RESERVE;
        $questionHistoryModel->scenario = QuestionHistory::SCENARIO_TEXT_REQUIRED;
        
        if (
            !$questionModel->isCreated && 
            !$questionModel->isCallback &&
            !$questionModel->isInterview 
        ) {
            throw new ForbiddenHttpException();
        }
        
        $post = Yii::$app->request->post();
        if (
            $questionModel->load($post) &&
            $questionHistoryModel->load($post)
        ) {
            $success = Question::reserveQuestion($questionModel, $questionHistoryModel);
            if ($success) {
                return $this->redirect(['view', 'id' => $questionModel->id]);
            }
        }
        
        return $this->render('reserve', [
            'questionModel' => $questionModel,
            'questionHistoryModel' => $questionHistoryModel,
        ]);
    }
    
    public function actionInterview($id)
    {
        $questionModel = $this->findModel($id);
        $questionHistoryModel = new QuestionHistory();
        $questionModel->scenario = Question::SCENARIO_INTERVIEW;
        $questionHistoryModel->scenario = QuestionHistory::SCENARIO_TEXT_REQUIRED;
        
        if (
            !$questionModel->isCreated && 
            !$questionModel->isCallback &&
            !$questionModel->isReserve &&
            !$questionModel->isInterview 
        ) {
            throw new ForbiddenHttpException();
        }
        
        $post = Yii::$app->request->post();
        if (
            $questionModel->load($post) &&
            $questionHistoryModel->load($post)
        ) {
            $success = Question::interviewQuestion($questionModel, $questionHistoryModel);
            if ($success) {
                return $this->redirect(['view', 'id' => $questionModel->id]);
            }
        }
        
        return $this->render('interview', [
            'questionModel' => $questionModel,
            'questionHistoryModel' => $questionHistoryModel,
        ]);
    }
    
    public function actionReject($id)
    {
        $questionModel = $this->findModel($id);
        $questionHistoryModel = new QuestionHistory();
        $questionModel->scenario = Question::SCENARIO_REJECT;
        $questionHistoryModel->scenario = QuestionHistory::SCENARIO_TEXT_REQUIRED;
        
        if ($questionModel->isRejected) {
            throw new ForbiddenHttpException();
        }
        
        $post = Yii::$app->request->post();
        if (
            $questionModel->load($post) &&
            $questionHistoryModel->load($post)
        ) {
            $success = Question::rejectQuestion($questionModel, $questionHistoryModel);
            if ($success) {
                return $this->redirect(['view', 'id' => $questionModel->id]);
            }
        }
        
        return $this->render('reject', [
            'questionModel' => $questionModel,
            'questionHistoryModel' => $questionHistoryModel,
        ]);
    }
    
    public function actionAccept($id)
    {
        $questionModel = $this->findModel($id);
        $questionHistoryModel = new QuestionHistory();
        $questionModel->scenario = Question::SCENARIO_ACCEPT;
        $questionHistoryModel->scenario = QuestionHistory::SCENARIO_TEXT_REQUIRED;
        
        if (!(
            $questionModel->isCreated ||
            $questionModel->isCallback ||
            $questionModel->isReserve ||
            $questionModel->isInterview  ||
            ($questionModel->isMakeMedbook && $questionModel->med_book)
        )) {
            throw new ForbiddenHttpException();
        }
        
        $post = Yii::$app->request->post();
        if (
            $questionModel->load($post) &&
            $questionHistoryModel->load($post)
        ) {
            $success = Question::acceptQuestion($questionModel, $questionHistoryModel);
            if ($success) {
                return $this->redirect(['view', 'id' => $questionModel->id]);
            }
        }
        
        return $this->render('accept', [
            'questionModel' => $questionModel,
            'questionHistoryModel' => $questionHistoryModel,
        ]);
    }
    
    public function actionMakeMedbook($id)
    {
        $questionModel = $this->findModel($id);

        if (!$questionModel->isInterview || $questionModel->med_book) {
            throw new ForbiddenHttpException();
        }
                
        $success = Question::makeMedbookQuestion($questionModel);
        if ($success) {
            Yii::$app->session->setFlash('success', 'Операция выполнена успешно.');
        } else {
            Yii::$app->session->setFlash('error', 'Произошла ошибка.');
        }
        return $this->redirect(['view', 'id' => $questionModel->id]);
    }
    
    public function actionMakeMedbookComplete($id)
    {
        $questionModel = $this->findModel($id);
        
        if (!$questionModel->isMakeMedbook || $questionModel->med_book) {
            throw new ForbiddenHttpException();
        }
        
        $success = Question::makeMedbookCompleteQuestion($questionModel);
        if ($success) {
            Yii::$app->session->setFlash('success', 'Операция выполнена успешно.');
        } else {
            Yii::$app->session->setFlash('error', 'Произошла ошибка.');
        }
        
        return $this->redirect(['view', 'id' => $questionModel->id]);
    }
    
    public function actionReturnToCreateState($id)
    {
        $questionModel = $this->findModel($id);
        $questionHistoryModel = new QuestionHistory();
        
        if ($questionModel->isCreated) {
            throw new ForbiddenHttpException();
        }
        
        $questionHistoryModel->scenario = QuestionHistory::SCENARIO_TEXT_REQUIRED;
        
        if ($questionHistoryModel->load(Yii::$app->request->post())) {
            $success = Question::returnToCreateStateQuestion(
                $questionModel, 
                $questionHistoryModel
            );
            if ($success) {
                return $this->redirect(['view', 'id' => $questionModel->id]); 
            }
        }
        
        return $this->render('returnToCreateState', [
            'questionModel' => $questionModel,
            'questionHistoryModel' => $questionHistoryModel,
        ]);
    }
    
               
    public function actionView($id)
    {
        $searchModel = new QuestionHistorySearch($id);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('view', [
            'model' => $this->findModel($id),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUpdate($id)
    {
        $questionModel = $this->findModel($id);
        $questionModel->scenario = Question::SCENARIO_UPDATE;
        
        if (
            $questionModel->load(Yii::$app->request->post()) && 
            $questionModel->save()
        ) {
            return $this->redirect(['view', 'id' => $questionModel->id]);
        }
        
        return $this->render('update', [
            'questionModel' => $questionModel,
        ]);
    }

   
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    private function findModel($id)
    {
        $model = Question::findOne(['id' => $id]);
        if ($model === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        } 
        
        return $model;
    }
    
    private function getAboutUsValuesList()
    {
        $aboutUsValues = AboutUsValue::find()->all();
        return ArrayHelper::map($aboutUsValues, 'id', 'name');
    }
    
    private function getAboutUsValuesListWithEmptyItem()
    {
        $aboutUsValues = AboutUsValue::find()->all();
        $aboutUsValuesList = ArrayHelper::map($aboutUsValues, 'id', 'name');
        return ArrayHelper::merge([null => ''], $aboutUsValuesList);
    }
    
    private function getCitiesList()
    {
        return  PersonalApi::getCitiesList();
    }
    
    private function getCitiesListWithEmptyItem()
    {
        return ArrayHelper::merge([null => ''], PersonalApi::getCitiesList());
    }
    
}
