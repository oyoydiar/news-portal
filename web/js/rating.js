$(document).ready(function() {
  $(document).on('click', '.rating-btn', function(e) {
    e.preventDefault();
    e.stopPropagation();

    const button = $(this);
    const ratingType = button.data('rating-type');
    const newsUrl = button.data('news-url');

    if (!newsUrl) {
      showNotification('Error: Invalid news data', 'error');
      return;
    }

    button.prop('disabled', true);

    $.ajax({
      url: '/rating/rate',
      method: 'POST',
      dataType: 'json',
      data: {
        _csrf: yii.getCsrfToken(),
        news_url: newsUrl,
        rating_type: ratingType
      },
      success: function(response) {
        button.prop('disabled', false);
        
        if (response.success) {
          updateRatingCounts(newsUrl, response.counts, response.user_rating);
          showNotification('Rating submitted!', 'success');
        } else {
          showNotification('Failed: ' + response.message, 'error');
        }
      },
      error: function(xhr, status, error) {
        button.prop('disabled', false);
        showNotification('Error submitting rating', 'error');
      }
    });
  });

  function updateRatingCounts(newsUrl, counts, userRating) {
    $(`[data-news-url="${newsUrl}"]`).each(function() {
      const ratingSection = $(this).closest('.rating-section');
      
      const thumbsUpBtn = ratingSection.find('[data-rating-type="thumbs_up"]');
      const thumbsUpCount = ratingSection.find('.rating-count[data-type="thumbs_up"]');
      
      const thumbsDownBtn = ratingSection.find('[data-rating-type="thumbs_down"]');
      const thumbsDownCount = ratingSection.find('.rating-count[data-type="thumbs_down"]');
      
      thumbsUpCount.text(counts.thumbs_up);
      thumbsDownCount.text(counts.thumbs_down);
      
      thumbsUpBtn.removeClass('active');
      thumbsDownBtn.removeClass('active');
      
      thumbsUpBtn.find('i').removeClass('bi-hand-thumbs-up-fill').addClass('bi-hand-thumbs-up');
      thumbsDownBtn.find('i').removeClass('bi-hand-thumbs-down-fill').addClass('bi-hand-thumbs-down');
      
      if (userRating === 'thumbs_up') {
        thumbsUpBtn.addClass('active');
        thumbsUpBtn.find('i').removeClass('bi-hand-thumbs-up').addClass('bi-hand-thumbs-up-fill');
      } else if (userRating === 'thumbs_down') {
        thumbsDownBtn.addClass('active');
        thumbsDownBtn.find('i').removeClass('bi-hand-thumbs-down').addClass('bi-hand-thumbs-down-fill');
      }
    });
  }

  function showNotification(message, type) {
    if (typeof showNotification === 'function') {
      showNotification(message, type);
    } else {
      const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
      const notification = $(
        `<div class="alert ${alertClass} alert-dismissible fade show position-fixed top-0 end-0 m-3" style="z-index: 9999;">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>`
      );
      $('body').append(notification);
      setTimeout(() => notification.alert('close'), 3000);
    }
  }
});