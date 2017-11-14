<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/14
 * Time: 13:37
 */

namespace welib\modules\weapi\controllers\common;


class weFun
{
    public static function getXML( ){
        $content = file_get_contents("php://input");
        $xml = simplexml_load_string($content);
        return $xml;
    }
}

//$xml = simplexml_load_string('<?xml version="1.0" encoding="UTF-8">
//<response>
//    <aaaa>value1</aaaa>
//    <bbb>value2</bbb>
//</response>');
//print_r($xml);

