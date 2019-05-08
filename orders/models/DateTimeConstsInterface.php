<?php

namespace app\modules\orders\models;

interface DateTimeConstsInterface
{
    const DATE_FORMAT = 'd-m-Y';
    const DATE_FORMAT_YII = 'php:d-m-Y';
   
    const TIME_FORMAT = 'H:i:s';
    const TIME_FORMAT_YII = 'php:H:i:s';
    
    const TIME_SHORT_FORMAT = 'H:i';
    const TIME_SHORT_FORMAT_YII = 'php:H:i';

}