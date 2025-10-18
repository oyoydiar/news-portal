<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
  'id' => 'my-news-portal',
  'name' => 'My News Portal',
  'timeZone' => 'Asia/Jakarta',
  'basePath' => dirname(__DIR__),
  'bootstrap' => ['log'],
  'aliases' => [
    '@bower' => '@vendor/bower-asset',
    '@npm' => '@vendor/npm-asset',
  ],
  'components' => [
    'request' => [
      // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
      'cookieValidationKey' => 'QTCsA1gLn7zuoKXXvaZ1ybkUkhaysOqA',
    ],
    'cache' => [
      'class' => 'yii\caching\FileCache',
    ],
    'user' => [
      'identityClass' => 'app\models\User',
      'enableAutoLogin' => true,
    ],
    'errorHandler' => [
      'errorAction' => 'site/error',
    ],
    'mailer' => [
      'class' => \yii\symfonymailer\Mailer::class,
      'viewPath' => '@app/mail',
      // send all mails to a file by default.
      'useFileTransport' => true,
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
    'db' => $db,
    'urlManager' => [
      'enablePrettyUrl' => true,
      'showScriptName' => false,
      'enableStrictParsing' => false,
      'rules' => [
        // Rating routes
        'rating/rate' => 'rating/rate',
        'rating/counts' => 'rating/get-counts',

        // Bookmark routes
        'bookmarks' => 'bookmark/index',
        'bookmark/add' => 'bookmark/add',
        'bookmark/remove' => 'bookmark/remove',
        'bookmark/remove-by-url' => 'bookmark/remove-by-url',

        // Auth routes
        'my-news-portal/register' => 'auth/register',
        'my-news-portal/login' => 'auth/login',
        'my-news-portal/logout' => 'auth/logout',

        // Portal News routes
        'my-news-portal' => 'site/index',
        'my-news-portal/<category:[\w-]+>' => 'site/index',
      ],
    ],
    'defaultRoute' => 'site/index',
    'formatter' => [
      'class' => 'yii\i18n\Formatter',
      'timeZone' => 'Asia/Jakarta',
      'dateFormat' => 'php:d-m-Y',
      'datetimeFormat' => 'php:d-m-Y H:i:s',
      'timeFormat' => 'php:H:i:s',
    ],
  ],
  'params' => $params,
];

if (YII_ENV_DEV) {
  // configuration adjustments for 'dev' environment
  $config['bootstrap'][] = 'debug';
  $config['modules']['debug'] = [
    'class' => 'yii\debug\Module',
    // uncomment the following to add your IP if you are not connecting from localhost.
    //'allowedIPs' => ['127.0.0.1', '::1'],
  ];

  $config['bootstrap'][] = 'gii';
  $config['modules']['gii'] = [
    'class' => 'yii\gii\Module',
    // uncomment the following to add your IP if you are not connecting from localhost.
    //'allowedIPs' => ['127.0.0.1', '::1'],
  ];
}

return $config;
