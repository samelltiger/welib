<?php

namespace welib\modules\weapi\controllers;

use Yii;
use yii\web\UploadedFile;

use welib\modules\weapi\controllers\common\BaseController;
use welib\modules\weapi\controllers\common\weFun;
use welib\modules\weapi\controllers\common\DealWx;

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
     * 收到微信服务器发来的信息，进行处理的类
     */
    public function actionGet()
    {
        $echostr = $this->get("echostr");
        if($echostr){
            echo  $echostr;
            exit;
        }

        $xml = weFun::getXML();
        $msg_type = ["text","image",'voice','video','shortvideo','location','link'];
        if($xml->MsgType == "event"){
            return $this->dealEvent($xml);
        }elseif( in_array($xml->MsgType , $msg_type ) ){
            return $this->dealMessage( $xml );
        }
        return ["aaaa"=>"value1","bbb"=>"value2"];
    }

    /**
     *  处理微信的事件
     * @param $xml
     * @return string
     */
    public function dealEvent($xml ){
        if(strtolower($xml->Event) == "subscribe"){
            $ret =[
                "ToUserName"    => $xml->FromUserName,
                "FromUserName"  => $xml->ToUserName,
                "CreateTime"    => [
                    "encode"    => false,
                    "value"     => time(),
                ],
                "MsgType"       => "text",
                "Content"       => "欢迎关注我们的微信公众号",
            ];
            weFun::returnSuccess(weFun::generoterXmlByArray( $ret ));
        }

    }

    /**
     * 处理接收消息
     * @param $xml
     */
    public function  dealMessage( $xml ){
        // "text","image",'voice','video','shortvideo','location','link'
        switch ($xml->MsgType ){
            case "text":
                return DealWx::dealTextMsg( $xml );
                break;
            case "image":
                return DealWx::dealImageMsg( $xml );
                break;
            case "voice":
                return DealWx::dealVoiceMsg( $xml );
                break;
            case "video":
                return DealWx::dealVideoMsg( $xml );
                break;
            case "shortvideo":
                return DealWx::dealShortvideoMsg( $xml );
                break;
            case "location":
                return DealWx::dealLocationMsg( $xml );
                break;
            case "link":
                return DealWx::dealLinkMsg( $xml );
                break;
        }
    }

    /**
     * api查看当前AccessToken及有效时间
     * @return array|void
     */
    public function  actionGetToken( ){
        $pwd = $this->get("pwd");
        if( $pwd==Yii::$app->params["pwd"] ){
            list($token,$expires) = weFun::getAccessToken();
            return ["accessToken"   => $token,
                "expires_in"         => $expires];
        }

        return ;
    }

    /**
     * 接受来自前端的文件，并保存到 update 目录下
     */
    public function actionUpdateFile( ){
        $file = UploadedFile::getInstanceByName("test");
        $path = "./update";

        if(null!==$file){
            if( !file_exists($path)){
                mkdir($path);
            }
            $file_path = $path."/".$file->name;

            if(!file_exists($file_path)){
                $file->saveAs($file_path);
            }else{
                $rand = mt_rand(1,500);
                if(!file_exists($path."/".$rand)){
                    mkdir($path."/".$rand);
                }
                $file_path = $path."/".$rand."/".$file->name;
                $file->saveAs($file_path);
            }

            $t = time();
            Yii::$app->db->createCommand()->insert("file",[
                'filename' => $file->name,
                'path' => $file_path,
                'file_size' => $file->size,
                'type' => $file->type,
                'create_time' => $t,
                'update_time' => $t,
            ])->execute();
            return ["success"=>true,"imageUrl" => $_SERVER['SERVER_NAME']."/".str_replace("./","",$file_path) ];
        }
        return ["success"=>false];
    }

    //用户授权接口：获取access_token、openId等；获取并保存用户资料到数据库
    public function actionAccesstoken()
    {
        $code = $_GET["code"];
        $state = $_GET["state"];
        $appid = Yii::$app->params['wechat']['appid'];
        $appsecret = Yii::$app->params['wechat']['appsecret'];
        $request_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.
            $appsecret.'&code='.$code.'&grant_type=authorization_code';
        //初始化一个curl会话
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = $this->response($result);
        //获取token和openid成功，数据解析
        $access_token = $result['access_token'];
        $refresh_token = $result['refresh_token'];
        $openid = $result['openid'];
        //请求微信接口，获取用户信息
        $userInfo = $this->getUserInfo($access_token,$openid);
        $user_check = WechatUser::find()->where(['openid'=>$openid])->one();
        if ($user_check) {
            //更新用户资料
            $user_check->nickname = $userInfo['nickname'];
            $user_check->sex = $userInfo['sex'];
            $user_check->headimgurl = $userInfo['headimgurl'];
            $user_check->country = $userInfo['country'];
            $user_check->province = $userInfo['province'];
            $user_check->city = $userInfo['city'];
            $user_check->access_token = $access_token;
            $user_check->refresh_token = $refresh_token;
            $user_check->update();
        } else {
            //保存用户资料
            $user = new WechatUser();
            $user->nickname = $userInfo['nickname'];
            $user->sex = $userInfo['sex'];
            $user->headimgurl = $userInfo['headimgurl'];
            $user->country = $userInfo['country'];
            $user->province = $userInfo['province'];
            $user->city = $userInfo['city'];
            $user->access_token = $access_token;
            $user->refresh_token = $refresh_token;
            $user->openid = $openid;
            $user->save();
        }
        //前端网页的重定向
        if ($openid) {
            return $this->redirect($state.$openid);
        } else {
            return $this->redirect($state);
        }
    }

    //从微信获取用户资料
    public function getUserInfo($access_token,$openid)
    {
        $request_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
        //初始化一个curl会话
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = $this->response($result);
        return $result;
    }
    //获取用户资料接口
    public function actionUserinfo()
    {
        if(isset($_REQUEST["openid"])){
            $openid = $_REQUEST["openid"];
            $user = WechatUser::find()->where(['openid'=>$openid])->one();
            if ($user) {
                $result['error'] = 0;
                $result['msg'] = '获取成功';
                $result['user'] = $user;
            } else {
                $result['error'] = 1;
                $result['msg'] = '没有该用户';
            }
        } else {
            $result['error'] = 1;
            $result['msg'] = 'openid为空';
        }
        return $result;
    }
}
