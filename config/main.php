<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-welib',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'welib\controllers',
    'modules' => [
        'weapi' => [
            'class' => 'welib\modules\weapi\Module',
        ],
    ],
    'components' => [
        'request' => [
//            'csrfParam' => '_csrf-welib',
            'enableCsrfValidation' => false,
        ],
//        'response' => [
//            'format' => yii\web\Response::FORMAT_XML,
//            'charset' => 'UTF-8',
//            // ...
//        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-welib', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the welib
            'name' => 'advanced-welib',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            //'enableStrictParsing' => true,  // 启用时会出现无法访问制定动作
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => [
                        'weapi/default'
                    ],

                    "extraPatterns"=>[
                        "Get get"       => "get", // 微信处理方法
                        "Get get-token" => "get-token",  //查看当前token
                        "Post update-file" => "update-file",
                    ],
                ],

            ],
        ],
    ],
    'params' => $params,
];
