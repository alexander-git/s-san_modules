<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\grid\ActionColumn;
use app\modules\personal\models\Card;
use app\modules\personal\assets\ActionColumnAsset;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\personal\models\search\CardSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Карточки сотрудников';
$this->params['breadcrumbs'][] = $this->title;

require_once __DIR__.'/_filtersHtml.php';

ActionColumnAsset::register($this);
?>
<div class="personal-card-index">    
    <p>
        <?= Html::a('Создать', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => SerialColumn::className()],
            'name',
            [
                'attribute' => 'depart_id',
                'value' => 'departmentName',
                'filter' => $departmentsList,
            ],
            [
                'attribute' => 'post_id',
                'value' => 'postName',
                'filter' => $settingsPostsList,
            ],
            [
                'attribute' => 'birthday',
                'filter' => $birthdayFilterHtml,
            ],
            [
                'attribute' => 'rate',
                'filter' => $rateFilterHtml,
            ],
            //'phone',
            //'address',
            [
                'attribute' => 'med_book',
                'value' => function ($model, $key, $index, $column) use ($haveList) {
                    return $haveList[(int) $model->med_book];
                },
                'filter' => $haveList,
            ],
            /*
            [
                'attribute' => 'student',
                'format' => 'boolean',
                'filter' => $yesNoList,
            ],

            [
                'attribute' => 'docs_ok',
                'format' => 'boolean',
                'filter' => $yesNoList,
            ],
            [
                'attribute' => 'date_employment',
                'filter' => $dateEmploymentFilterHtml,
            ],
            [
                'attribute' => 'date_obt_input',
                'filter' => $dateObtInputFilterHtml,
            ],
            [
                'attribute' => 'date_obt_first',
                'filter' => $dateObtFirstFilterHtml,
            ], 
            */
            [
                'attribute' => 'state', 
                'value' => 'stateName',
                'filter' => Card::getStatesArray(),
            ],
            [
                'class' => ActionColumn::className(),
                'contentOptions' => ['class' => 'actionColumn'],
            ],
        ],
    ]); ?>
</div>
