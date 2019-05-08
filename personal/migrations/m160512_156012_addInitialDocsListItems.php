<?php

use yii\db\Migration;
use app\modules\personal\models\DocsList;

class m160512_156012_addInitialDocsListItems extends Migration
{              
    public function up()
    {
        $this->batchInsert('{{%personal_docs_list}}', ['name', 'type'], [
            ['Заявление о приёме на работу', DocsList::TYPE_DEFAULT],
            ['Трудовая книжка', DocsList::TYPE_DEFAULT],
            ['Соглашение о стажировке', DocsList::TYPE_DEFAULT],
            ['Приказ о стажировке', DocsList::TYPE_DEFAULT],
            ['Индивидуальный материальный договор', DocsList::TYPE_DEFAULT],
            ['Коллективный материальный договор', DocsList::TYPE_DEFAULT],
            ['Приказ о приёме на работу', DocsList::TYPE_DEFAULT],
            ['Паспорт', DocsList::TYPE_LOADABLE],
            ['ИНН', DocsList::TYPE_LOADABLE],
            ['Страховое свидетельство', DocsList::TYPE_LOADABLE],
            ['Свидетельство о рождении ребёнка', DocsList::TYPE_LOADABLE],
        ]);
    }

    public function down()
    {   
        $this->delete('{{%personal_docs_list}}', [
            'name' => [
                'Заявление о приёме на работу',
                'Трудовая книжка', 
                'Соглашение о стажировке',
                'Приказ о стажировке',
                'Индивидуальный материальный договор',
                'Коллективный материальный договор',
                'Приказ о приёме на работу',
                'Паспорт',
                'ИНН',
                'Страховое свидетельство',
                'Свидетельство о рождении ребёнка',
            ],
        ]);
    }
}
