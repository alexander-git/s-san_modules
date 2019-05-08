<?php

namespace app\modules\personal\models;

use Yii;

/**
 * This is the model class for table "{{%personal_card_docs}}".
 *
 * @property integer $card_id
 * @property integer $docs_id
 * @property integer $check
 * @property string $file
 *
 * @property Card $card
 * @property DocsList $docsList
 */
class CardDocs extends \yii\db\ActiveRecord
{    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%personal_card_docs}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['card_id', 'required'],
            ['card_id', 'integer'],
            [
                'card_id', 
                'exist', 
                'skipOnError' => true, 
                'targetClass' => Card::className(), 
                'targetAttribute' => ['card_id' => 'id']
            ],
            ['card_id', 'unique', 'targetAttribute' => ['card_id', 'docs_id']],
            
            ['check', 'required'],
            ['check', 'boolean'],
            ['check', 'default', 'value' => false],
            
            ['docs_id', 'required'],
            ['docs_id', 'integer'],
            [
                'docs_id', 
                'exist', 
                'skipOnError' => true, 
                'targetClass' => DocsList::className(), 
                'targetAttribute' => ['docs_id' => 'id']
            ],
            
            ['file', 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'card_id' => 'Карта',
            'docs_id' => 'Документ',
            'check' => 'Наличие',
            'file' => 'Путь',
        ];
    }

    public function getFileUrl($documentsUrl) 
    {   
        return $documentsUrl.'/'.$this->file;
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCard()
    {
        return $this->hasOne(Card::className(), ['id' => 'card_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocsList()
    {
        return $this->hasOne(DocsList::className(), ['id' => 'docs_id']);
    }
    
    private function getFilename($docUploadForm)
    {
        return $this->card_id.'_'.$this->docs_id.'.'.$docUploadForm->file->extension;
    }
    
    public static function createDocDefault($cardDocsModel, $docsId, $cardId) 
    {
        $cardDocsModel->docs_id = $docsId;
        $cardDocsModel->card_id = $cardId;
        $cardDocsModel->check = true;
        $cardDocsModel->file = null;
        return $cardDocsModel->save();
    }
    
    public static function createDocLoadable(
        $cardDocs,
        $docUploadForm, 
        $docsId, 
        $cardId,
        $documentsPath
    ) {
        $transaction = CardDocs::getDb()->beginTransaction();
        try {
            $cardDocs->docs_id = $docsId;
            $cardDocs->card_id = $cardId;
            $cardDocs->check = true;

            if (!$docUploadForm->validate()) {
                $transaction->rollBack();
                return false;
            }

            $filename = $cardDocs->getFilename($docUploadForm);
            $cardDocs->file = $filename;
            
            if (!$cardDocs->save()) {
               $transaction->rollBack();
               return false;
            }

            $fullFilename = $documentsPath.'/'.$filename;

            if(!$docUploadForm->upload($fullFilename)) {
                $transaction->rollBack();
                return false;
            }
            
            $transaction->commit();
            return true;
        }
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
    
    public static function updateDocLoadable(
        $cardDocs,
        $docUploadForm, 
        $documentsPath
    ) {
        $transaction = CardDocs::getDb()->beginTransaction();
        try {
            if (!$docUploadForm->validate()) {
                $transaction->rollBack();
                return false;
            }

            $oldFullFilename = $documentsPath.'/'.$cardDocs->file;
            
            $filename = $cardDocs->getFilename($docUploadForm);
            $cardDocs->file = $filename;
            
            if (!$cardDocs->save()) {
               $transaction->rollBack();
               return false;
            }

            $fullFilename = $documentsPath.'/'.$filename;

            if (!unlink($oldFullFilename)) {
                $transaction->rollBack();
                return false;
            }
            
            if(!$docUploadForm->upload($fullFilename)) {
                $transaction->rollBack();
                return false;
            }
            
            $transaction->commit();
            return true;
        }
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
            
    public static function deleteDoc($cardDocs, $documentsPath)
    {
        if ($cardDocs->docsList->isTypeDefault) {
            return $cardDocs->delete();
        } elseif ($cardDocs->docsList->isTypeLoadable) {
            return self::deleteDocLoadable($cardDocs, $documentsPath);
        } else {
            throw new \LogicException();
        }
    }
    
    private static function deleteDocLoadable($cardDocs, $documentsPath) 
    {
        $transaction = CardDocs::getDb()->beginTransaction();
        try {
            $file = $documentsPath.'/'.$cardDocs->file;
            if (!$cardDocs->delete()) {
                $transaction->rollBack();
                return false;
            }

            if (!unlink($file)) {
                $transaction->rollBack();
                return false;
            }

            $transaction->commit();
            return true;
        }
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
    
}
