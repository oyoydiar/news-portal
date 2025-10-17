<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Category: <?= ucfirst($category) ?></title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h2>Top Headlines - <?= ucfirst($category) ?></h2>
  <a href="index.php">← Back to Home</a>

  <?php if (!empty($data['articles'])): ?>
    <?php foreach ($data['articles'] as $article): ?>
      <div class="article">
        <?php if (!empty($article['urlToImage'])): ?>
          <img src="<?= $article['urlToImage'] ?>" alt="News image">
        <?php endif; ?>

        <div class="article-content">
          <h2><a href="<?= $article['url'] ?>" target="_blank"><?= htmlspecialchars($article['title']) ?></a></h2>
          <div class="meta">
            Source: <?= htmlspecialchars($article['source']['name'] ?? '-') ?> |
            Author: <?= htmlspecialchars($article['author'] ?? '-') ?> |
            Published: <?= htmlspecialchars($article['publishedAt'] ?? '-') ?>
          </div>
          <div class="description">
            <?= htmlspecialchars($article['description'] ?? '') ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p>No news found for this category.</p>
  <?php endif; ?>
</body>
</html>