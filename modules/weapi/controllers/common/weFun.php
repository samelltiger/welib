<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/14
 * Time: 13:37
 */

namespace welib\modules\weapi\controllers\common;

use Yii;

class weFun
{
    /**
     *  将http数据流数据转为xml对象返回
     * @return \SimpleXMLElement
     */
    public static function getXML( ){
        $content = file_get_contents("php://input");
        $xml = simplexml_load_string($content);
        return $xml;
    }

    /**
     *  输出 $return_str ,并在退出之前执行一个自定义函数
     * @param string $return_str
     * @param callable|Array $callable
     * @param  Array  $arr_value
     */
    public static  function returnSuccess( $return_str= "success" , $callable=null,$arr_value=[]){
        echo $return_str;
        if(is_callable($callable)){
            $callable();
        }elseif(is_array($callable)){
            call_user_func($callable);
        }
        exit;
    }

    public static function getAccessToken( ){
        // https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=APPID&secret=APPSECRET
        $cache = Yii::$app->cache;
        $exists = $cache->exists("access_token");
        if($exists){
            return [$cache->get("access_token"),$cache->get("access_token_expires_in")];
        }else{
            $appid = Yii::$app->params['wechat']["appid"];
            $appsecret = Yii::$app->params['wechat']["appsecret"];

            $url = sprintf( "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s", $appid , $appsecret );
//            $content = file_get_contents($url);

            //初始化一个curl会话
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $content = curl_exec($ch);
            curl_close($ch);

            $res_arr = json_decode($content,true);
            if( isset($res_arr["access_token"]) ){
                $access_token = $res_arr["access_token"];
                $expires_in   = $res_arr["expires_in"];

                $cache->set( "access_token",$access_token , $expires_in );
                $cache->set("access_token_expires_in",$expires_in,$expires_in);
                return [$access_token,$expires_in];
            }else{
                return [false,false];
            }
        }


    }
}

//$xml = simplexml_load_string('<?xml version="1.0" encoding="UTF-8">
//<response>
//    <aaaa>value1</aaaa>
//    <bbb>value2</bbb>
//</response>');
//print_r($xml);

