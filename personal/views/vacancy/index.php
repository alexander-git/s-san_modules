<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\grid\ActionColumn;
use app\modules\personal\assets\ActionColumnAsset;
use app\modules\personal\models\Vacancy;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\personal\models\search\Vacancy */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Вакансии';
$this->params['breadcrumbs'][] = $this->title;

require __DIR__.'/_filtersHtml.php';

ActionColumnAsset::register($this);

?>
<div class="personal-vacancy-index">
    <p>
        <?= Html::a('Создать', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => SerialColumn::className()],

            [
                'attribute' => 'post_id',
                'value' => 'postName',
                'filter' => $settingsPostsList,
            ],
            [
                'attribute' => 'depart_id',
                'value' => 'departmentName',
                'filter' => $departmentsList,
            ],
            [
                'attribute' => 'date_create',
                'format' => ['datetime', 'php:d-m-Y H:i:s'],
                'filter' => $dateCreateFilterHtml,
            ],
            [
                'attribute' => 'date_public',
                'format' => ['datetime', 'php:d-m-Y H:i:s'],
                'filter' => $datePublicFilterHtml,
            ], 
            [
                'attribute' => 'state',
                'value' => 'stateName',
                'filter' => Vacancy::getStatesArray(),
            ],
            [
                'attribute' => 'user_id',
                'value' => 'userName',
                'filter' => $usersList,
            ],
            [
                'class' => ActionColumn::className(),
                'contentOptions' => ['class' => 'actionColumn'],
            ],
        ],
    ]); ?>
</div>
