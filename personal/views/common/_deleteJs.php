<?php

use yii\web\View;

$confirmMessage = Yii::t('yii', 'Are you sure you want to delete this item?');

$js = <<<JS
    var  deleteItemHandler = function(e) {
        var a = $(this); 
        var href = a.attr('href');
        
        if (!confirm('$confirmMessage')) {
            return false;
        }

        $.ajax({
            url: href,
            type: 'post',
            success: function(data) {
                $.pjax.reload({'container' : '#'+'$gridViewContainerId'});
            }
        });
   
        e.preventDefault();
    };

    $(document).on('click', '.deleteItem', deleteItemHandler);
JS;

$this->registerJs($js, View::POS_READY);