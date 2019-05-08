<?php

use yii\db\Migration;

class m160904_094115_addInitialCategoryStationValues extends Migration
{
    public function up()
    {
        $this->batchInsert('{{%orders_category_station}}', ['city_id', 'category_id', 'station_id'], [
            [2, 7, 1],
            [2,	8, 1],
            [2,	9, 1],
            [2,	10, 1],
            [2,	11, 1],
            [2,	12, 1],
            [2,	13, 1],
            [2,	14, 2],
            [2,	15, 1],
            [2,	16, 4],
            [2,	17, 4],
            [2,	18, 4],
            [2,	19, 4],
            [2,	20, 4],
            [2,	21, 3],
            [2,	22, 3],
            [2,	23, 3],
            [2,	24, 3],
        ]);
    }

    public function down()
    {   
    }
    
}
