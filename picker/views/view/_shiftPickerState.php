<?php

$info = $this->context->getShiftPickerStateInfo();
?>


<p class="alert alert-info">
    Смена открыта <?= $info->shiftPickerDateOpen ?> 
    <?php if ($info->isMainPicker) : ?> 
        (<span>вы главный комплектовщик</span>)
    <?php endif; ?>
</p>
