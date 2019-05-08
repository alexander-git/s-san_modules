<?php

use yii\web\View;

$js = <<<JS
    var beforeSubmitHandler = function() {
        var form = $(this);
        
        if(form.find('.has-error').length) {
            return false;
        }

        $.ajax({
            url: form.attr('action'),
            type: 'post',
            data: form.serialize(),
            success: function(data) {
                $('#'+'$modalContentId').html(data.message);
                $.pjax.reload({'container' : '#'+'$gridViewContainerId'});
            }
        });
   
        return false;
    };

    $(document).on('beforeSubmit', '#'+'$createUpdateFormId', beforeSubmitHandler);
JS;

$this->registerJs($js, View::POS_READY);