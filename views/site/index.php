<?php

use yii\helpers\Url;

$currentCategory = Yii::$app->request->get('category');
$keyword = Yii::$app->request->get('keyword');
?>
<h2>Top Headlines - United States</h2>

<div style="margin-bottom: 20px;">
  <form id="searchForm" action="<?= Url::to(['site/index']) ?>" method="get"  >
    <input 
      id="keyword"
      type="text" 
      name="keyword" 
      placeholder="Search" 
      value="<?= Yii::$app->request->get('keyword') ?>"
    />
    <button type="submit">Search</button>
  </form>
</div>

<nav>
  <?php
    $categories = [
      'all' => 'All',
      'business' => 'Business',
      'entertainment' => 'Entertainment',
      'sports' => 'Sports',
      'general' => 'General',
      'health' => 'Health',
      'science' => 'Science',
      'technology' => 'Technology'
    ];
   ?>

    <div class="categories" style="margin-bottom: 10px;">
    <?php foreach ($categories as $slug => $label): ?>
      <?php
        $url = $slug === 'all' 
          ? Url::to(['/my-news-portal']) 
          : Url::to(['/my-news-portal/' . $slug]);
        
        if (!empty($keyword)) {
          $url .= '?keyword=' . urlencode($keyword);
        }
      ?>
      <a 
        id="cat-<?= $slug ?>"
        href="<?= $url ?>" 
        style="margin-right:8px; <?= $currentCategory === $slug || ($slug === 'all' && !$currentCategory) ? 'font-weight:bold;text-decoration:underline;' : '' ?>"
      >
        <?= htmlspecialchars($label) ?>
      </a>
    <?php endforeach; ?>
  </div>
</nav>
<hr />

<?php if ($data['status'] !== 'ok'): ?>
  <div style="color:red;">
    <p>Error</p>
    <p>Code: <?= $data['code'] ?></p>
    <p>Message: <?= htmlspecialchars($data['message'] ?? 'Something went wrong') ?></p>
    <button onclick="location.reload()">Reload</button>
  </div>

<?php elseif (empty($data['articles'])): ?>
  <p>No News Found <?= $keyword ? " for '<b>" . htmlspecialchars($keyword) . "</b>'" : '' ?>.</p>

<?php else: ?>
  <p><small>Total Results : <?= $data['totalResults'] ?></small></p>

  <?php foreach ($data['articles'] as $article): ?>
    <div class="article" style="display:flex; margin-bottom:20px;">
      <?php if (!empty($article['urlToImage'])): ?>
        <img 
          src="<?= htmlspecialchars($article['urlToImage']) ?>" 
          alt="image" 
          style="width:150px; height:100px; margin-right:10px;"
        >
      <?php endif; ?>

      <div>
        <a href="<?= htmlspecialchars($article['url']) ?>" target="_blank">
          <b><?= htmlspecialchars($article['title']) ?></b>
        </a><br/>
        <small>
          Source: <?= htmlspecialchars($article['source']['name'] ?? '-') ?> | 
          Author: <?= htmlspecialchars($article['author'] ?? '-') ?> | 
          Published: <?= htmlspecialchars($article['publishedAt'] ?? '-') ?>
        </small>
        <p><?= htmlspecialchars($article['description'] ?? '') ?></p>
      </div>
    </div>
  <?php endforeach; ?>
<?php endif; ?>

<?php
$this->registerJsFile('@web/js/search-news.js', [
  'depends' => [\yii\web\JqueryAsset::class]
]);
?>