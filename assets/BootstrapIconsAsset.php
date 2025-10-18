<?php

namespace app\assets;

use yii\web\AssetBundle;

class BootstrapIconsAsset extends AssetBundle
{
  public $sourcePath = '@webroot/assets/bootstrap-icons';

  public $css = ['bootstrap-icons.css'];

  public $publishOptions = [
    'forceCopy' => YII_DEBUG,
  ];
}
