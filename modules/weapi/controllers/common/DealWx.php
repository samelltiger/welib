<?php
namespace  welib\modules\weapi\controllers\common;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/17
 * Time: 12:02
 */

use yii\base\Model;
use welib\modules\weapi\controllers\common\weFun;

class DealWx extends Model
{
    public static function dealTextMsg( $xml ){
        /*<xml>
 <ToUserName><![CDATA[toUser]]></ToUserName>
 <FromUserName><![CDATA[fromUser]]></FromUserName>
 <CreateTime>1348831860</CreateTime>
 <MsgType><![CDATA[text]]></MsgType>
 <Content><![CDATA[this is a test]]></Content>
 <MsgId>1234567890123456</MsgId>
 </xml>
         */
        $xml = weFun::getXML();
        $ret = [
            "ToUserName" =>  $xml->FromUserName,
            "FromUserName" =>  $xml->ToUserName,
            "CreateTime" =>  [
                "encode" => false,
                "value"  => time()
            ],
            "MsgType" =>  "text",
            "Content" =>  $xml->Content,
            "MsgId" =>  $xml->MsgId,
        ];

         weFun::returnSuccess(weFun::generoterXmlByArray($ret));
    }

    public static function dealImageMsg( $xml ){
        weFun::returnSuccess();
    }

    public static function dealVoiceMsg( $xml ){
        weFun::returnSuccess();
    }

    public static function dealVideoMsg( $xml ){
        weFun::returnSuccess();
    }

    public static function dealShortvideoMsg( $xml ){
        weFun::returnSuccess();
    }

    public static function dealLocationMsg( $xml ){
        weFun::returnSuccess();
    }

    public static function dealLinkMsg( $xml ){
        weFun::returnSuccess();
    }
}