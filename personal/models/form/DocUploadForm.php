<?php

namespace app\modules\personal\models\form;

use yii\base\Model;

class DocUploadForm extends Model
{
    public $file;
    
    public function rules()
    {
        return [
            [
                'file', 
                'file', 
                'skipOnEmpty' => false,
                'extensions' => 'png, jpg',
            ]
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'file' => 'Файл'
        ]; 
    }
    
    public function upload($fullFilename)
    {   
        return $this->file->saveAs($fullFilename);       
    }
    
}