<?php

namespace welib\modules\weapi\controllers;

use yii\rest\ActiveController;

/**
 * Default controller for the `weapi` module
 */
class DefaultController extends ActiveController
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public $modelClass = 'welib\modules\weapi\model';
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionTest()
    {
        return ["aaaa","bbbbb"];
        return $this->render('index');
    }
}
