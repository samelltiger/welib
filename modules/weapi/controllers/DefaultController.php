<?php

namespace welib\modules\weapi\controllers;

use Yii;

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

    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * 收到微信服务器发来的信息，进行出了力的类
     */
    public function actionGet()
    {
        $echostr = $this->get("echostr");
        if($echostr){
            echo  $echostr;
            exit;
        }
    }
}
