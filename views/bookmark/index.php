<?php

use yii\helpers\Url;

$this->title = 'My Bookmarks';
?>

<div class="container">
	<div class="d-flex justify-content-between align-items-center mb-4">
		<h2 class="fw-bold mb-0">
			<i class="bi bi-bookmark-fill me-2"></i><?= $this->title ?>
		</h2>
		<a href="<?= Url::to(['/site/index']) ?>" class="btn btn-outline-primary">
			<i class="bi bi-arrow-left me-1"></i>Back to News
		</a>
	</div>

	<?php if (empty($bookmarks)): ?>
		<div class="text-center py-5">
			<i class="bi bi-bookmark display-4 text-muted mb-3"></i>
			<h4 class="text-muted">No bookmarks yet</h4>
			<p class="text-muted">Start bookmarking articles to see them here.</p>
			<a href="<?= Url::to(['/site/index']) ?>" class="btn btn-primary">
				<i class="bi bi-newspaper me-1"></i>Browse News
			</a>
		</div>
	<?php else: ?>
		<div class="row">
				<?php foreach ($bookmarks as $bookmark): ?>
					<div class="col-md-6 col-lg-4 mb-4">
						<div class="card h-100 shadow-sm position-relative news-card">
							<div class="position-absolute top-0 end-0 p-2" style="z-index: 20;">
								<button class="btn btn-sm btn-outline-danger remove-bookmark" 
												data-id="<?= $bookmark->id ?>"
												title="Remove bookmark">
									<i class="bi bi-x-lg"></i>
								</button>
							</div>

							<div style="height: 180px; overflow: hidden;">
								<?php if (!empty($bookmark->image_url)): ?>
									<img 
										src="<?= htmlspecialchars($bookmark->image_url) ?>" 
										class="card-img-top w-100 h-100"
										alt="News image"
										style="object-fit: cover;"
										onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
									>
								<?php endif; ?>
									
								<div class="w-100 h-100 bg-gradient <?= empty($bookmark->image_url) ? 'd-flex' : 'd-none' ?>" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
									<div class="text-center text-white w-100 h-100 d-flex flex-column align-items-center justify-content-center">
										<i class="bi bi-newspaper fs-1 mb-2"></i>
										<p class="small mb-0 fw-bold">NEWS</p>
										<p class="small mb-0"><?= htmlspecialchars($bookmark->source ?: 'Source') ?></p>
									</div>
								</div>
							</div>

							<div class="card-body d-flex flex-column">
								<h6 class="card-title mb-2">
									<?= htmlspecialchars($bookmark->title) ?>
								</h6>
								<div class="text-muted mb-2 small">
									<div class="d-flex align-items-center mb-1">
										<i class="bi bi-building me-1"></i>
										<strong>Source:</strong> <?= htmlspecialchars($bookmark->source ?: '-') ?>
									</div>
									<div class="d-flex align-items-center mb-1">
										<i class="bi bi-person me-1"></i>
										<strong>Author:</strong> <?= htmlspecialchars($bookmark->author ?: '-') ?>
									</div>
									<div class="d-flex align-items-center">
										<i class="bi bi-clock me-1"></i>
										<strong>Published:</strong> 
										<?php 
											if (!empty($bookmark->published_at)):
												$publishedDate = DateTime::createFromFormat('Y-m-d H:i:s', $bookmark->published_at);
												if ($publishedDate) {
													echo htmlspecialchars($publishedDate->format('d/m/Y H:i:s'));
												} else {
													echo '-';
												}
											else:
												echo '-';
											endif;
										?>
									</div>
								</div>
								<p class="card-text mt-auto small"><?= htmlspecialchars($bookmark->description ?: '') ?></p>
							</div>

							<div class="card-footer bg-transparent border-top-0 pt-0">
								<div class="d-flex justify-content-between align-items-center mb-2">
									<a href="<?= htmlspecialchars($bookmark->url) ?>" class="d-flex align-items-center" target="_blank">
										<small class="text-muted">
											<i class="bi bi-link-45deg me-1"></i>Read full article here
										</small>
									</a>
								</div>

								<div class="rating-section border-top pt-2">
									<div class="d-flex align-items-center justify-content-end gap-2">
										<button class="btn btn-sm <?= $isGuest ? 'btn-outline-secondary' : 'btn-outline-success' ?> rating-btn <?= $isGuest ? 'guest-rating' : '' ?> <?= isset($userRatings[$bookmark->url]) && $userRatings[$bookmark->url] === 'thumbs_up' ? 'active' : '' ?>" 
											data-rating-type="thumbs_up" 
											data-news-url="<?= htmlspecialchars($bookmark->url) ?>"
											title="<?= $isGuest ? 'Please login to rate' : 'Thumbs Up' ?>"
											<?= $isGuest ? 'disabled' : '' ?>>
											<i class="bi bi-hand-thumbs-up<?= isset($userRatings[$bookmark->url]) && $userRatings[$bookmark->url] === 'thumbs_up' ? '-fill' : ''?>"></i>
											<span class="rating-count" data-type="thumbs_up">
												<?= $ratingCounts[$bookmark->url]['thumbs_up'] ?? 0 ?>
											</span>
										</button>

										<button class="btn btn-sm <?= $isGuest ? 'btn-outline-secondary' : 'btn-outline-danger' ?> rating-btn <?= $isGuest ? 'guest-rating' : '' ?> <?= isset($userRatings[$bookmark->url]) && $userRatings[$bookmark->url] === 'thumbs_down' ? 'active' : '' ?>" 
											data-rating-type="thumbs_down" 
											data-news-url="<?= htmlspecialchars($bookmark->url) ?>"
											title="<?= $isGuest ? 'Please login to rate' : 'Thumbs Down' ?>"
											<?= $isGuest ? 'disabled' : '' ?>>
											<i class="bi bi-hand-thumbs-down<?= isset($userRatings[$bookmark->url]) && $userRatings[$bookmark->url] === 'thumbs_down' ? '-fill' : ''?>"></i>
											<span class="rating-count" data-type="thumbs_down">
												<?= $ratingCounts[$bookmark->url]['thumbs_down'] ?? 0 ?>
											</span>
										</button>
									</div>
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
	$this->registerJsFile('@web/js/rating.js', [
		'depends' => [\yii\web\JqueryAsset::class],
	]);
?>