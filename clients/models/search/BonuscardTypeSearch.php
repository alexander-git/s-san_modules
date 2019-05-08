<?php

namespace app\modules\clients\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use app\modules\clients\models\Bonuscard;
use app\modules\clients\models\BonuscardType;


class BonuscardTypeSearch extends BonuscardType
{
    public $discountFrom;
    public $discountTo;
    public $bonusquanFrom;
    public $bonusquanTo;
    public $minmoneyFrom;
    public $minmoneyTo;
    public $bonuscardsCountFrom;
    public $bonuscardsCountTo;
    
    public function rules()
    {
        return [
            [
                ['discount', 'bonusquan', 'minmoney'], 
                'integer',
                'min' => 0,
            ],
            ['name', 'safe'],
            $this->getBaseFieldsIntervalRules(),
            $this->getBonuscardsCountIntervalRules(),
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        echo $this->bonuscardsCountFrom;
        
        $query = BonuscardType::find()
            ->select([
                "bt.*", 
                new Expression("COUNT(`b`.`id`) AS bonuscardsCount") 
            ])
            ->from(['bt' => BonuscardType::tableName()])
            ->joinWith([
                'bonuscards' => function($q) {
                    $q->from(['b' => Bonuscard::tableName()]);
                },
            ], false)
            ->groupBy(['bt.id']);


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'id',
                    'name',
                    'discount',
                    'bonusquan',
                    'minmoney',
                    'bonuscardsCount',
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'bt.name', $this->name]);
        $this->addBaseFieldsIntervalConditionsToQuery($query);
        $this->addBonuscardCountIntervalConditionsToQuery($query);
        
        return $dataProvider;
    }
    
    protected function getBaseFieldsIntervalRules()
    {
        return [
            [
                'discountFrom',
                'discountTo',
                'bonusquanFrom',
                'bonusquanTo',
                'minmoneyFrom',
                'minmoneyTo',
            ],
            'integer',
            'min' => 0,
        ];
    }
    
    protected function getBonuscardsCountIntervalRules()
    {
        return [
            [
                'bonuscardsCountFrom',
                'bonuscardsCountTo',
            ],
            'integer',
            'min' => 0,
        ];
    }
    
    protected function addBaseFieldsIntervalConditionsToQuery($query)
    {
        $query->andFilterWhere(['>=', 'bt.discount', $this->discountFrom])
            ->andFilterWhere(['<=', 'bt.discount', $this->discountTo])
            ->andFilterWhere(['>=', 'bt.bonusquan', $this->bonusquanFrom])
            ->andFilterWhere(['<=', 'bt.bonusquan', $this->bonusquanTo])
            ->andFilterWhere(['>=', 'bt.minmoney', $this->minmoneyFrom])
            ->andFilterWhere(['<=', 'bt.minmoney', $this->minmoneyTo]);   
                
        return $query;
    }
    
    protected function addBonuscardCountIntervalConditionsToQuery($query)
    {
        if (!empty($this->bonuscardsCountFrom)) {
            $query->andHaving(['>=', 'bonuscardsCount', $this->bonuscardsCountFrom]);
        }
        if (!empty($this->bonuscardsCountTo)) {
            $query->andHaving(['<=', 'bonuscardsCount', $this->bonuscardsCountCountTo]);
        }
        
        return $query; 
    }
    
}
