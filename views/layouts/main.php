<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use app\assets\BootstrapIconsAsset;

AppAsset::register($this);
BootstrapIconsAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag([
  'name' => 'viewport',
  'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no',
]);
$this->registerMetaTag([
  'name' => 'description',
  'content' => $this->params['meta_description'] ?? '',
]);
$this->registerMetaTag([
  'name' => 'keywords',
  'content' => $this->params['meta_keywords'] ?? '',
]);
$this->registerLinkTag([
  'rel' => 'icon',
  'type' => 'image/x-icon',
  'href' => Yii::getAlias('@web/favicon.ico'),
]);
?>
<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head(); ?>
</head>
<body class="d-flex flex-column h-100 bg-light">
<?php $this->beginBody(); ?>

<header id="header" class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?= Yii::$app
      ->homeUrl ?>">My News Portal</a>
  </div>
</header>

<main id="main" class="flex-shrink-0" role="main">
  <div class="container">
      <?php if (!empty($this->params['breadcrumbs'])): ?>
          <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
      <?php endif; ?>

      <?= Alert::widget() ?>
      <?= $content ?>
  </div>
</main>

<footer id="footer" class="mt-auto py-3 bg-white border-top">
  <div class="container text-center text-muted small">
    &copy; My News Portal ver 1.0 <?= date('Y') ?>
  </div>
</footer>

<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>
