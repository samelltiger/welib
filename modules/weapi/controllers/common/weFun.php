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

    /**
     * 获取 AccessToken及过期时间，如果文件缓存中没有accessToken 则通过微信api重新获取
     * @return array
     */
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

    /**
     * 将array 转成xml字符串
     * eg：
     *  assoc_arr = [
     *  "ToUserName" =>  $xml->FromUserName,
     *  "FromUserName" =>  $xml->ToUserName,
     *  "CreateTime" =>
     *      "child"  =>  [
     *          "encode" => false,
     *          "value"  => time()
     *  ],
     *  "MsgType" =>  "text",
     *  "Content" =>  $xml->Content,
     *  "MsgId" =>  $xml->MsgId,
     *  ]
     * @param array $assoc_arr
     * @param bool $isChild
     * @return string
     */
     public static function generoterXmlByArray( Array $assoc_arr , $isChild = false ){
         // '<xml>
         //<ToUserName><![CDATA[%s]]></ToUserName>
         //<FromUserName><![CDATA[%s]]></FromUserName>
         //<CreateTime>%s</CreateTime>
         //<MsgType><![CDATA[%s]]></MsgType>
         //<Content><![CDATA[%s]]></Content>
         //</xml>'

         $xml = "<xml>";
         if( $isChild ){
             $xml = "";
         }

         if( is_array( $assoc_arr ) ){
             foreach ( $assoc_arr as $key => $value ){
                 if( is_array( $value ) && isset( $value['child'] ) ){
                     $xml .= "<".$key.">".self::generoterXmlByArray( $value['child'] ,true )."</{$key}>";
                 }elseif( (is_array( $value ) && isset( $value['encode'] ) && $value['encode'] ) || !is_array( $value ) ) {
                     $xml .= "<".$key."><![CDATA[".( is_array( $value ) ? $value['value']:$value )."]]></{$key}>";
                 }else{
                     $xml .= "<".$key.">".$value['value']."</{$key}>";
                 }
             }
         }

         return $isChild ? $xml :$xml."</xml>";
      }

     public static function getMediaFromWx( $media_id=false ){
         //https://api.weixin.qq.com/cgi-bin/media/get?access_token=ACCESS_TOKEN&media_id=MEDIA_ID
         $url = "https://api.weixin.qq.com/cgi-bin/media/get?access_token=%s&media_id=%s";
         $token = weFun::getAccessToken()[0];
         if($token){
             $ch = curl_init( sprintf($url,$token,$media_id) );
             curl_setopt($ch,CURL);
         }
      }
}

//$xml = simplexml_load_string('<?xml version="1.0" encoding="UTF-8">
//<response>
//    <aaaa>value1</aaaa>
//    <bbb>value2</bbb>
//</response>');
//print_r($xml);

