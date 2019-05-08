<?php

namespace app\modules\cashdesks\controllers;


class ServiceController extends DefaultController
{
    
    public function actionIndex()
    {
        return $this->render('index');
    }
    
}
