<?php

namespace app\modules\picker\components\grid;

use yii\grid\DataColumn;

class TotalColumn  extends DataColumn
{

    private $total = 0;
    
    public function getDataCellValue($model, $key, $index) {
        $value = parent::getDataCellValue($model, $key, $index);
        if (is_numeric($value)) {
            $this->total += $value;
        }
        return $value;
    }
    
    public function renderFooterCellContent()
    {
        return $this->grid->formatter->format($this->total, $this->format);
    }
    
}