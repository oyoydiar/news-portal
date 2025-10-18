<?php
$dotenvFile = __DIR__ . '/../.env.local';
if (file_exists($dotenvFile)) {
  foreach (file($dotenvFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    if (strpos($line, '=') !== false) {
      [$key, $value] = explode('=', $line, 2);
      $_ENV[trim($key)] = trim($value);
      putenv(trim($key) . '=' . trim($value));
    }
  }
}

// comment out the following two lines when deployed to production
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/web.php';

(new yii\web\Application($config))->run();
