<?php

namespace app\modules\orders\behaviors;

use yii\base\Behavior;
use yii\helpers\Json;

class ProductsPreparationBehavior extends Behavior
{
    
    public $productImageUrlPrefix = '';
    public $productImageUrlSuffix = '';
    
    public function prepareProductsToRender($products, $properties, $values)
    {
        $this->prepareProductsImageUrl($products);
        $this->prepareProductsOptionData($products, $properties, $values);
        
        return $products;
    }
    
    public function prepareProductsImageUrl($products)
    {
        foreach ($products as $product) {
            $this->prepareProductImageUrl($product);
        }
        return $products;
    }
    
    public function divideProductsByCategories($products)
    {
        $result = [];
        foreach ($products as $product) {
            $categoryId = $product->main_category_id;
            if (!isset($result[$categoryId])) {
                $result[$categoryId] = [];
            }
            $result[$categoryId] []= $product;
            
        }
        return $result;
    }
    
    private function prepareProductImageUrl($product)
    {
        $imageFilename = $product->imageFilename;
        if (!empty($imageFilename)) {
            $dotPosition = strpos($imageFilename , '.');
            $imageExtension = substr($imageFilename, $dotPosition + 1);
            $imageName = substr($imageFilename, 0, $dotPosition);
            $prefix = $this->productImageUrlPrefix;
            $suffix = $this->productImageUrlSuffix;
            $imageUrl = $prefix.$imageName.$suffix.'.'.$imageExtension;
            $product->imageUrl = $imageUrl;
        } else {
            $product->imageUrl = null;
        }
        
        return $product;
    }
    
    private function prepareProductsOptionData(&$products, $properties, $values) 
    {
        foreach ($products as $product) {
            $this->prepareProductOptionData($product, $properties, $values);
        }
        return $products;
    }
    
    private static $count = 0;
    
    private function prepareProductOptionData($product, $properties, $values)
    {
        $product->hasOptions = false;
        $product->optionData = null;
        $product->prices = null;
        $product->optionIds = null;
        $product->initialPrice = $product->price;
        
        if (empty($product->option_generate)) {
            return;
        }
        $optionGenearate = Json::decode($product->option_generate, true);
        if (count($optionGenearate['values']) === 0) {
            return; 
        }
        
        $product->hasOptions = true;
        $product->optionsData  = [];
        $optionsValues = $optionGenearate['values'];
        foreach ($optionsValues as $propertyId => $propertyValues) {
            $propertyValueIds = array_keys($propertyValues);
            $optionsDataItem = new \stdClass();
            $optionsDataItem->propertyId = $propertyId;
            $optionsDataItem->propertyName = $properties[$propertyId]->name;
            
            $optionsDataItem->values = [];
            foreach ($propertyValueIds as $valueId) {
                $optionsDataItem->values[$valueId] = $values[$valueId]->name;
                if (!isset($optionsDataItem->selected)) {
                    $optionsDataItem->selected = $valueId;                    
                }
            }
            $product->optionsData []= $optionsDataItem;
        }
        
        $baseSlugLength = strlen($product->slug.'-');
        $prices = [];
        $optionIds = [];
        foreach ($product->options as $option) {
            $optionsSetKey = substr($option->slug, $baseSlugLength);
            $prices[$optionsSetKey] = $option->price;          
            $optionIds[$optionsSetKey] = $option->id;
        }
        
        $product->prices = $prices;
        $product->optionIds = $optionIds;
        
        $initialProperties = [];
        foreach ($product->optionsData as $optionsDataItem) {
            $initialProperties []= $optionsDataItem->selected;
        }
        $initialOptionsKey = implode('-', $initialProperties);

        //if (!isset($prices[$initialOptionsKey])) {
        //    $product->initialPrice = $product->price;
        //    ++self::$count;
        //    \Yii::error($product->name.' : '.self::$count);
        //    return $product;
        //}
        
        $product->initialPrice = $prices[$initialOptionsKey];
        
        return $product;
    }
    

}