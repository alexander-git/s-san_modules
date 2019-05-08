<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\widgets\MaskedInput;
use kartik\date\DatePicker;
use app\modules\personal\assets\ScheduleAsset;
use app\modules\personal\assets\ScheduleCssAsset;
use app\modules\personal\assets\MustacheAsset;


/* @var $this yii\web\View */

$this->title = $departmentName;
$this->params['breadcrumbs'][] = [
    'label' => 'График работы',
    'url' => ['index'],
];
$this->params['breadcrumbs'][] = $this->title;


ScheduleAsset::register($this);
ScheduleCssAsset::register($this);
MustacheAsset::register($this);

// Шарина границ ячеек таблицы в пикселях. Нужно для правильного вычисления 
// длинны интервалов. При изменении её в стилях, также нужно внести изменения сюда.
$borderWidth = 1;

$loadUrlTemplate = Url::to(['load', 'departmentId' => '__departmentId__', 'date' => '__date__']);
$createWorkTimeUrlTemplate = Url::to(['create-work-time']);
$updateWorkTimeUrlTemplate = Url::to(['update-work-time', 'id' => '__workTimeId__']);
$deleteWorkTimeUrlTemplate = Url::to(['delete-work-time', 'id' => '__workTimeId__']);

$scheduleParasmJs = <<<JS
    {
        'departmentId' : $departmentId,
        'borderWidth' : $borderWidth,
        'loadUrlTemplate' : '$loadUrlTemplate',
        'createWorkTimeUrlTemplate' : '$createWorkTimeUrlTemplate',
        'updateWorkTimeUrlTemplate' : '$updateWorkTimeUrlTemplate',
        'deleteWorkTimeUrlTemplate' : '$deleteWorkTimeUrlTemplate',
    }
JS;

$timePickerPluginOptions = [
    'showMeridian' => false,
    'minuteStep' => 1, 
];

?>
<div class="personal-schedule-schedule">
     <?php Modal::begin([
        'id' => 'schedule__modal',
        'header' => '<h4>Укажите время</h4>',
        'size' => 'modal-sm',
        'toggleButton' => false,
        'options' => [
            'data-select' => 'schedule__modal',
        ],
    ]); ?>
        <p class="text-center">
            <label class="control-label">От</label>
            <?=  MaskedInput::widget([
                'name' => 'from', 
                'options' => [
                    'data-select' => 'schedule__timeInputFrom',
                ],
                'mask' => '99:99',
            ]) ?>
        </p>
        
        <p class="text-center">
            <label class="control-label">До</label>
            <?= MaskedInput::widget([
                'name' => 'to', 
                'options' => [
                    'data-select' => 'schedule__timeInputTo',
                    'readonly' => false,
                ],
                'mask' => '99:99',
            ]) ?> 
        </p>
        
        <p class="alert-danger" data-select="schedule__timeInputErrorMessage" style="display : none;">
        </p>
        
        <br />
        <p class="text-center">
            <?= Html::button('Сохранить', [
                'data-select' => 'schedule__timeSaveButton',
                'class' => 'btn btn-success'
            ]) ?>
            &nbsp;&nbsp;&nbsp;
            <?= Html::button('Отменить', [
                'data-select' => 'schedule__timeCancelButton',
                'class' => 'btn btn-primary',
            ]) ?>
        </p>
        
    <?php Modal::end(); ?>
    
    <div class="schedulePanel">
        <?= Html::button(Html::tag('span', '', ['class' => 'glyphicon glyphicon-arrow-right']), [
            'class' => 'btn btn-primary pull-right',
            'data-select' => 'schedule__nextDateButton',
            'style' => 'margin-left : 10px;'
        ]); ?>

        <div class="pull-right">
        <?= DatePicker::widget([
            'name' => 'dateInput',
            'type' => DatePicker::TYPE_INPUT,
            'value' => $date,
            'options' => [
                'data-select' => 'schedule__dateInput'
            ],
            'pluginOptions' => [
                'format' => 'dd-mm-yyyy',
                'autoclose' => true,
            ],
        ]) ?>
        </div>

        <?= Html::button(Html::tag('span', '', ['class' => 'glyphicon glyphicon-arrow-left']), [
            'class' => 'btn btn-primary pull-right',
            'data-select' => 'schedule__previousDateButton',
            'style' => 'margin-right : 10px;'
        ]); ?>
    </div>
    <br /><br/>

    <table class="schedule">
        <thead>
            <tr class="schedule__headTop">
                <td class="schedule__headName" rowspan = "2">
                    Сотрудник
                </td>
                <?php for ($i = 0; $i <= 23; $i++) :  ?>
                <td class="schedule__headTime">
                    <div class="schedule__headTimeLabel">
                        <?= ($i === 0 ? '00:00' : $i) ?>
                    </div>
                </td>
                <?php endfor; ?>
            </tr>
            <tr class="schedule__headBottom">
                <?php for ($i = 0; $i <= 23; $i++) :  ?>
                <td></td>
                <?php endfor; ?>
            </tr>
        </thead>
        <tbody class="schedule__body" data-select="schedule__body">
        </tbody>
    </table>
    
    <script data-select="schedule__groupNameRowTemplate" type="x-tmpl-mustache">
        <tr class="schedule__groupName">
            <td class="schedule__groupNameCell">              
                {{groupName}}
            </td>
            <?php for ($i = 0; $i <= 23; $i++) : ?>
                <td class="schedule__groupNameEmptyCell" >              
                </td>
            <?php endfor; ?>
        </tr>
    </script>
    <script data-select="schedule__rowTemplate"  type="x-tmpl-mustache">
        <tr data-select-row="{{userId}}" data-user-id="{{userId}}">
        </tr>
    </script>
    <script data-select="schedule__nameCellTemplate" type="x-tmpl-mustache">        
        <td class="schedule__name">              
            {{name}}
            <br />
            <span class="schedule__amountTime" data-select-amount-time="{{userId}}"></span> 
        </td>
    </script>
    <script data-select="schedule__cellTemplate" type="x-tmpl-mustache">        
        <td class="schedule__time" data-select-cell="{{userId}}_{{time}}" data-user-id="{{userId}}" data-time="{{time}}">
            <div class="schedule__workTimeContainer" data-select-work-time-container="{{userId}}_{{time}}">
            </div>
        </td>
    </script>

    <script data-select="schedule__workTimeTemplate" type="x-tmpl-mustache">        
        <div class="schedule__workTime bg-primary" data-select-work-time="{{userId}}_{{from}}_{{to}}" data-work-time-id="{{workTimeId}}" data-from="{{from}}" data-to="{{to}}" data-user-id="{{userId}}" data-begin="{{begin}}" data-end="{{end}}" >
            <div class="schedule__workTimeInterval" title="{{from}} - {{to}}">
                {{from}} - {{to}}
            </div>
            <div class="schedule__workTimeDelete"  data-work-time-id="{{workTimeId}}" data-select="schedule__deleteWorkTime">
                <span class="glyphicon glyphicon-remove"></span>
            </div>
        </div>
    </script>
    
</div>
<?php
// Расположим внизу, чтобы kartik\DatePicker уже был инициализированным.
$this->registerJs("ScheduleScript.init($scheduleParasmJs);", View::POS_READY);

?>