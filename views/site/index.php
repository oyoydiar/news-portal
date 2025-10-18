<?php

use yii\helpers\Url;
use app\models\Bookmark;

$title = 'Top Headlines - United States';
$currentCategory = Yii::$app->request->get('category');
$keyword = Yii::$app->request->get('keyword');

$categories = [
  'all' => 'All',
  'business' => 'Business',
  'entertainment' => 'Entertainment',
  'sports' => 'Sports',
  'general' => 'General',
  'health' => 'Health',
  'science' => 'Science',
  'technology' => 'Technology',
];

$isGuest = Yii::$app->user->isGuest;
$bookmarkedUrls = [];
if (!$isGuest) {
    $bookmarkedUrls = Bookmark::find()
        ->select('url')
        ->where(['user_id' => Yii::$app->user->id])
        ->column();
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="fw-bold mb-0">
    <i class="bi bi-newspaper me-2"></i><?= $title ?>
  </h2>
  
  <div class="d-flex align-items-center ">
    <form id="newsForm" action="<?= Url::to([
      'my-news-portal',
    ]) ?>" method="get" class="d-flex gap-2 me-3">
      <div class="input-group" style="width: 280px;">
        <span class="input-group-text bg-white border-end-0">
          <i class="bi bi-search text-muted"></i>
        </span>
        <input
          id="keyword"
          type="text"
          name="keyword"
          class="form-control border-start-0"
          placeholder="Search news..."
          value="<?= $keyword ?>"
        />
        <?php if ($currentCategory && $currentCategory !== 'all'): ?>
          <input type="hidden" name="category" value="<?= $currentCategory ?>">
        <?php endif; ?>
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-arrow-right"></i>
        </button>
      </div>
    </form>

    <?php if ($isGuest): ?>
      <a href="<?= Url::to([
        'my-news-portal/register',
      ]) ?>" class="btn btn-primary btn-sm d-flex align-items-center" title="Login" style="height: 38px;" >
        <i class="bi bi-box-arrow-in-right me-1"></i>
      </a>
    <?php else: ?>
      <div class="dropdown">
        <button class="btn btn-outline-secondary btn-sm dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="height: 38px;">
          <i class="bi bi-person-circle me-1"></i>
          <?= Yii::$app->user->identity->name ?? 'User' ?>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li>
            <span class="dropdown-item-text small text-muted">
              <i class="bi bi-person me-2"></i>Logged in as<br>
              <strong><?= Yii::$app->user->identity->name ?? 'User' ?></strong>
            </span>
          </li>
          <li><hr class="dropdown-divider"></li>
          <li>
            <a class="dropdown-item" href="<?= Url::to(['/bookmarks']) ?>">
              <i class="bi bi-bookmark me-2"></i>My Bookmarks
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="<?= Url::to([
              'my-news-portal/logout',
            ]) ?>">
              <i class="bi bi-box-arrow-right me-2"></i>Logout
            </a>
          </li>
        </ul>
      </div>
    <?php endif; ?>
  </div>
</div>

<nav class="mb-4">
  <ul class="nav nav-pills justify-content-center flex-wrap gap-2">
    <?php
    $categoryIcons = [
      'all' => 'bi-grid',
      'business' => 'bi-briefcase',
      'entertainment' => 'bi-film',
      'sports' => 'bi-trophy',
      'general' => 'bi-globe',
      'health' => 'bi-heart',
      'science' => 'bi-robot',
      'technology' => 'bi-cpu',
    ];

    foreach ($categories as $slug => $label):

      $isActive =
        $currentCategory === $slug || ($slug === 'all' && !$currentCategory);

      if ($slug === 'all') {
        $url = Url::to(['/my-news-portal']);
      } else {
        $url = Url::to(['/my-news-portal/' . $slug]);
      }

      if (!empty($keyword)) {
        $url .=
          (strpos($url, '?') === false ? '?' : '&') .
          'keyword=' .
          urlencode($keyword);
      }
      ?>
      <li class="nav-item">
        <?php if ($isActive): ?>
          <span class="nav-link active fw-bold d-flex align-items-center" style="cursor: default;">
            <i class="<?= $categoryIcons[$slug] ?> me-1"></i>
            <?= htmlspecialchars($label) ?>
          </span>
        <?php else: ?>
          <a href="<?= $url ?>" class="nav-link d-flex align-items-center" id="cat-<?= $slug ?>">
            <i class="<?= $categoryIcons[$slug] ?> me-1"></i>
            <?= htmlspecialchars($label) ?>
          </a>
        <?php endif; ?>
      </li>
    <?php
    endforeach;
    ?>
  </ul>
</nav>

<div id="newsContainer">
  <?php if ($data['status'] !== 'ok'): ?>
    <div class="alert alert-danger text-center">
      <h5 class="fw-bold">
        <i class="bi bi-exclamation-triangle me-2"></i>Error
      </h5>
      <p>Code: <?= $data['code'] ?></p>
      <p><?= htmlspecialchars($data['message'] ?? 'Something went wrong') ?></p>
      <button class="btn btn-outline-danger mt-2" onclick="location.reload()">
        <i class="bi bi-arrow-clockwise me-1"></i>Reload
      </button>
    </div>

  <?php

    elseif (empty($data['articles'])): ?>
    <div class="alert alert-warning text-center">
      <i class="bi bi-search display-4 d-block mb-3 text-muted"></i>
      <p class="mb-0">No news found <?= $keyword
        ? "for '<b>" . htmlspecialchars($keyword) . "</b>'"
        : '' ?>.</p>
      <?php if ($keyword): ?>
        <a href="<?= Url::to([
          'my-news-portal',
        ]) ?>" class="btn btn-outline-warning btn-sm mt-2">
          <i class="bi bi-arrow-left me-1"></i>Clear Search
        </a>
      <?php endif; ?>
    </div>

  <?php else: ?>
  <div class="row">
    <?php foreach ($data['articles'] as $article): ?>
      <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100 shadow-sm position-relative news-card">
          <?php if (!$isGuest): ?>
            <div class="position-absolute top-0 end-0 p-2" style="z-index: 10;">
              <button class="btn btn-sm bookmark-btn <?= 
                in_array($article['url'], $bookmarkedUrls) ? 'btn-success' : 'btn-light' 
              ?>" 
                data-article='<?=
                  $articleJson = json_encode([
                    'url' => $article['url'] ?? '',
                    'title' => $article['title'] ?? '',
                    'description' => mb_substr($article['description'] ?? '', 0, 200),
                    'image' => $article['urlToImage'] ?? '',
                    'source' => $article['source']['name'] ?? '',
                    'author' => $article['author'] ?? '',
                    'published_at' => $article['publishedAt'] ?? ''
                  ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_UNESCAPED_SLASHES);
                  echo $articleJson;
                ?>'
                title="<?= in_array($article['url'], $bookmarkedUrls) ? 'Remove bookmark' : 'Bookmark this article' ?>">
                <i class="bi <?= in_array($article['url'], $bookmarkedUrls) ? 'bi-bookmark-fill' : 'bi-bookmark' ?>"></i>
              </button>
            </div>
          <?php endif; ?>

          <div style="height: 180px; overflow: hidden;">
            <?php if (!empty($article['urlToImage'])): ?>
              <img 
                src="<?= htmlspecialchars($article['urlToImage']) ?>" 
                class="card-img-top w-100 h-100"
                alt="News image"
                style="object-fit: cover;"
                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
              >
            <?php endif; ?>
            
            <div class="w-100 h-100 bg-gradient <?= empty(
              $article['urlToImage']
            )
              ? 'd-flex'
              : 'd-none' ?>" 
                 style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
              <div class="text-center text-white w-100 h-100 d-flex flex-column align-items-center justify-content-center">
                <i class="bi bi-newspaper fs-1 mb-2"></i>
                <p class="small mb-0 fw-bold">NEWS</p>
                <p class="small mb-0"><?= htmlspecialchars(
                  $article['source']['name'] ?? 'Source',
                ) ?></p>
              </div>
            </div>
          </div>

          <div class="card-body d-flex flex-column">
            <h6 class="card-title mb-2">
              <a href="<?= htmlspecialchars($article['url']) ?>" target="_blank" class="text-decoration-none text-dark news-title">
                <?= htmlspecialchars($article['title']) ?>
              </a>
            </h6>
            <div class="text-muted mb-2 small">
              <div class="d-flex align-items-center mb-1">
                <i class="bi bi-building me-1"></i>
                <strong>Source:</strong> <?= htmlspecialchars(
                  $article['source']['name'] ?? '-',
                ) ?>
              </div>
              <div class="d-flex align-items-center mb-1">
                <i class="bi bi-person me-1"></i>
                <strong>Author:</strong> <?= htmlspecialchars(
                  $article['author'] ?? '-',
                ) ?>
              </div>
              <div class="d-flex align-items-center">
                <i class="bi bi-clock me-1"></i>
                <strong>Published:</strong> <?= htmlspecialchars(
                  $article['publishedAt'] ?? '-',
                ) ?>
              </div>
            </div>
            <p class="card-text mt-auto small"><?= htmlspecialchars(
              $article['description'] ?? '',
            ) ?></p>
          </div>

          <div class="card-footer bg-transparent border-top-0 pt-0">
            <div class="d-flex justify-content-between align-items-center">
              <a href="<?= Url::to(
                $article['url'],
              ) ?>" class="d-flex align-items-center" title="Login" target="_blank">
                <small class="text-muted">
                  <i class="bi bi-link-45deg me-1"></i>Read full article here
                </small>
              </a>
            </div>
          </div>
          <div class="rating-section border-top p-2">
            <div class="d-flex align-items-center justify-content-end gap-2">
              <button class="btn btn-sm rating-btn <?= $isGuest ? 'guest-rating' : 'btn-outline-success' ?> <?= isset($userRatings[$article['url']]) && $userRatings[$article['url']] === 'thumbs_up' ? 'active' : '' ?>" 
                data-rating-type="thumbs_up" 
                data-news-url="<?= htmlspecialchars($article['url']) ?>"
                title="<?= $isGuest ? 'Please login to rate' : 'Thumbs Up' ?>"
                <?= $isGuest ? 'disabled' : '' ?>>
                <i class="bi bi-hand-thumbs-up<?= isset($userRatings[$article['url']]) && $userRatings[$article['url']] === 'thumbs_up' ? '-fill' : ''?>"></i>
                <span class="rating-count" data-type="thumbs_up">
                  <?= $ratingCounts[$article['url']]['thumbs_up'] ?? 0 ?>
                </span>
              </button>

              <button class="btn btn-sm rating-btn <?= $isGuest ? 'guest-rating' : 'btn-outline-danger' ?> <?= isset($userRatings[$article['url']]) && $userRatings[$article['url']] === 'thumbs_down' ? 'active' : '' ?>" 
                data-rating-type="thumbs_down" 
                data-news-url="<?= htmlspecialchars($article['url']) ?>"
                title="<?= $isGuest ? 'Please login to rate' : 'Thumbs Down' ?>"
                <?= $isGuest ? 'disabled' : '' ?>>
                <i class="bi bi-hand-thumbs-down<?= isset($userRatings[$article['url']]) && $userRatings[$article['url']] === 'thumbs_down' ? '-fill' : ''?>"></i>
                <span class="rating-count" data-type="thumbs_down">
                  <?= $ratingCounts[$article['url']]['thumbs_down'] ?? 0 ?>
                </span>
              </button>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<?php
  $this->registerJsFile('@web/js/bookmark.js', [
    'depends' => [\yii\web\JqueryAsset::class],
  ]);

  $this->registerJsFile('@web/js/news.js', [
    'depends' => [\yii\web\JqueryAsset::class],
  ]);


  $this->registerJsFile('@web/js/rating.js', [
    'depends' => [\yii\web\JqueryAsset::class],
  ]);
?>
