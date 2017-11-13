<?php

namespace welib\modules\weapi\controllers;

use Yii;
use yii\web\Response;

use welib\modules\weapi\controllers\common\BaseController;

/**
 * Default controller for the `weapi` module
 */
class DefaultController extends BaseController
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public $modelClass = 'welib\modules\weapi\model';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_HTML;
        return $behaviors;
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionGet()
    {
        $echostr = $this->get("echostr");
        if($echostr){
            return  $echostr;
//            exit;
        }
    }
}
