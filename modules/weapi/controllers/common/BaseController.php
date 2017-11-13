<?php
namespace  welib\modules\weapi\controllers\common;

use phpDocumentor\Reflection\Types\Boolean;
use Yii;

class BaseController extends \welib\controllers\common\BaseController;
{
    public $modelClass = 'welib\models\Goods';
    // public $needCheckAction = [];
    // public $notCheckAction = [ "index" , "login","create"];

    /**
     * 检查请求接口方法是否需要带token
     */
     public function checkAccess($action, $model = null, $params = [])
     {
         $allowAction = Yii::$app->params['allowAction'];

         if ( \in_array( $action , (array)$allowAction) ) {
             return;
         }

         throw new \yii\web\ForbiddenHttpException(sprintf('You can only %s articles that you\'ve created.', $action));
     }

    /**
     *  判断是否是从微信服务器传过来的请求
     * @param $action
     * @return Boolean
     */
    public function beforeAction($action){
        // 获取配置的默认允许的 action ，这些不需要 token 便可访问
        $allowAction = Yii::$app->params['token'];

        if ( \in_array( $action->id , (array)$allowAction) ) {
            return parent::beforeAction($action);
        }

        $token = $this->get("token");
        $cache = Yii::$app -> cache;
        $email = $cache -> get( $token ) ;

        if( !$email ){
            $url = Yii::$app->params["redirectUrl"]."?errcode=411&role=api";
            header("HTTP/1.1 301");
            header("location: ".$url);
            die;
            // return $this->redirect( Yii::$app->params["redirectUrl"]."?errcode=411&role=api"); // token过期，重定向到登录页面
        }

        // 只要使用了一次，就将 token 重新设置并重新计时为30分钟
        $cache -> delete( $token );
        $cache -> set( $token , $email , 30*60 );

        // throw new \yii\web\ForbiddenHttpException(sprintf('You can only %s articles that you\'ve created.', $action->id));
        return parent::beforeAction($action);

    }

    public function actionErr(){
        $code = $this->get("errcode");
        $errCode = Yii::$app->params["errCode"] ;
        return $this->renderJson([ ] , 0 , 200 , \array_key_exists($code , $errCode) ? $errCode[$code] : $errCode[0]);
    }

    /**
     *  生成一个6位数的验证码！
     */
    public function getVerifyCode()
    {
        $letters = '0123456789';
        $code = '';
        for ($i = 0; $i < 6; ++$i) {
            $code .= $letters[mt_rand(0, 9)];
        }

        return $code;
    }

    /**
     * 生成一个32位的token
     */
    public function getToken()
    {
        $letters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_0123456789';
        $code = '';
        $str_length = strlen($letters) - 1;

        for ($i = 0; $i < 32; ++$i) {
            $code .= $letters[mt_rand(0 , $str_length )];
        }

        return $code;
    }

    /**
     *  通过邮箱发送验证码
     */
    public function SendVerifyCode( $email , $code )
    {
        if($code){
            $conent = file_get_contents( "http://cseclmail/?r=site/send&email=$email&code=$code" );
            $success = json_decode( $conent , true) ;
            if( isset( $success['success'] ) && $success['success'] === "sucessful" ){
                return true;
            }else
                return false;
        }

        return false;
    }
}

?>