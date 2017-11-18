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
        /*<xml>  接收信息
<ToUserName><![CDATA[toUser]]></ToUserName>
<FromUserName><![CDATA[fromUser]]></FromUserName>
<CreateTime>12345678</CreateTime>
<MsgType><![CDATA[image]]></MsgType>
<Image>
<MediaId><![CDATA[media_id]]></MediaId>
</Image>
</xml>*/
        $xml = [
            "ToUserName"      =>  $xml->FromUserName,
            "FromUserName"    =>  $xml->ToUserName,
            "CreateTime"      =>  time(),
            "MsgType"         =>  "image",
            "Image"           =>  [
                "child"       =>[
                    "MediaId"     =>  $xml->Image->MediaId
                ]
            ],
        ];
        weFun::returnSuccess( weFun::generoterXmlByArray($xml) );
    }

    public static function dealVoiceMsg( $xml ){
        /*<xml>
<ToUserName><![CDATA[toUser]]></ToUserName>
<FromUserName><![CDATA[fromUser]]></FromUserName>
<CreateTime>12345678</CreateTime>
<MsgType><![CDATA[voice]]></MsgType>
<Voice>
<MediaId><![CDATA[media_id]]></MediaId>
</Voice>
</xml>*/
        weFun::returnSuccess();
    }

    public static function dealVideoMsg( $xml ){
        /*<xml>
<ToUserName><![CDATA[toUser]]></ToUserName>
<FromUserName><![CDATA[fromUser]]></FromUserName>
<CreateTime>12345678</CreateTime>
<MsgType><![CDATA[video]]></MsgType>
<Video>
<MediaId><![CDATA[media_id]]></MediaId>
<Title><![CDATA[title]]></Title>
<Description><![CDATA[description]]></Description>
</Video>
</xml>
         * */
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