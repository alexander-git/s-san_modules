<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\grid\ActionColumn;
use app\modules\personal\assets\ActionColumnAsset;
use app\modules\personal\models\search\QuestionSearch;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\personal\models\search\QuestionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Анкеты';
$this->params['breadcrumbs'][] = $this->title;

require_once __DIR__.'/_filtersHtml.php';

ActionColumnAsset::register($this);

?>
<div class="personal-question-index">

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
                'attribute' => 'post_id',
                'value' => 'postName',
                'filter' => $settingsPostsList,
            ],
            [
                'attribute' => 'birthday',
                'filter' => $birthdayFilterHtml,
            ],
            [
                'attribute' => 'city',
                'filter' => $citiesList,
            ],
            /*
            [
                'attribute' => 'about_us_id',
                'value' => 'aboutUsValueName',
                'filter' => $aboutUsValuesList,
            ],
            */
            [
                'attribute' => 'med_book',
                'value' => function ($model, $key, $index, $column) use ($haveList) {
                    return $haveList[(int) $model->med_book];
                },
                'filter' => $haveList,
            ],
            [
                'attribute' => 'children',
                'value' => function ($model, $key, $index, $column) use ($haveList) {
                    return $haveList[(int) $model->children];
                },
                'filter' => $haveList,
            ],
            [
                'attribute' => 'smoking',
                'format' => 'boolean',
                'filter' => $yesNoList,
            ],
            'date',
            [
                'attribute' => 'state',
                'value' => 'stateName',
                'filter' => QuestionSearch::getStatesArray(),
            ],
            [
                'class' => ActionColumn::className(),
                'template' => '{view} {delete}',
                'contentOptions' => ['class' => 'actionColumn'],
            ],
        ],
    ]); ?>
    
</div>
