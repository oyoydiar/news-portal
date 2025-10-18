$(document).ready(function () {
  $(document).on('click', '.bookmark-btn', function (e) {
    e.preventDefault();
    e.stopPropagation();

    const button = $(this);
    const articleDataString = button.data('article');
    
    let articleData;
    
    try {
      let cleanJsonString = articleDataString;
      
      if (articleDataString.includes('}{')) {
        const firstJsonEnd = articleDataString.indexOf('}{') + 1;
        cleanJsonString = articleDataString.substring(0, firstJsonEnd);
      }
      
      articleData = JSON.parse(cleanJsonString);
      
      if (!articleData.url || !articleData.title) {
        throw new Error('Missing required article data');
      }
    } catch (error) {
      showNotification('Error: Invalid article data format', 'error');
      return;
    }

    const originalIcon = button.find('i').attr('class');
    const isRemoveAction = button.hasClass('btn-success');
    
    button.prop('disabled', true);
    button.find('i').removeClass().addClass('bi bi-hourglass-split');

    const url = isRemoveAction ? '/bookmark/remove-by-url' : '/bookmark/add';

    $.ajax({
      url: url,
      method: 'POST',
      dataType: 'json',
      data: {
        _csrf: yii.getCsrfToken(),
        article: articleData,
      },
      success: function (response) {
        button.prop('disabled', false);
        
        if (response.success) {
          if (isRemoveAction) {
            button.find('i').removeClass().addClass('bi bi-bookmark');
            button.removeClass('btn-success').addClass('btn-light');
            button.attr('title', 'Bookmark this article');
            showNotification('Bookmark removed successfully!', 'success');
          } else {
            button.find('i').removeClass().addClass('bi bi-bookmark-fill');
            button.addClass('btn-success').removeClass('btn-light');
            button.attr('title', 'Remove bookmark');
            showNotification('Article bookmarked successfully!', 'success');
          }
        } else {
          button.find('i').removeClass().addClass(originalIcon);
          showNotification('Failed: ' + response.message, 'error');
        }
      },
      error: function (xhr, status, error) {
        button.prop('disabled', false);
        button.find('i').removeClass().addClass(originalIcon);
        showNotification('Server error: Failed to process bookmark', 'error');
      },
    });
  });

  $(document).on('click', '.remove-bookmark', function(e) {
    e.preventDefault();
    e.stopPropagation();
    
    const button = $(this);
    const bookmarkId = button.data('id');
    const card = button.closest('.col-md-6'); 
    
    button.prop('disabled', true);
    button.find('i').removeClass('bi-x-lg').addClass('bi-hourglass-split');
    
    $.ajax({
      url: '/bookmark/remove',
      method: 'POST',
      dataType: 'json',
      data: {
        _csrf: yii.getCsrfToken(),
        id: bookmarkId
      },
      success: function(response) {
        if (response.success) {
          card.fadeOut(300, function() {
            $(this).remove();
            reorganizeLayout();
            if ($('.col-md-6').length === 0) setTimeout(() => location.reload(), 500);
          });
          showNotification('Bookmark removed successfully', 'success');
        } else {
          showNotification('Failed: ' + response.message, 'error');
          button.prop('disabled', false);
          button.find('i').removeClass('bi-hourglass-split').addClass('bi-x-lg');
        }
      },
      error: function(xhr, status, error) {
        showNotification('Error removing bookmark', 'error');
        button.prop('disabled', false);
        button.find('i').removeClass('bi-hourglass-split').addClass('bi-x-lg');
      }
    });
  });

  function showNotification(message, type) {
    $('.alert.position-fixed').alert('close');
    
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const iconClass = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle';
    
    const notification = $(
      '<div class="alert ' + alertClass + ' alert-dismissible fade show position-fixed top-0 end-0 m-3" style="z-index: 9999; max-width: 400px;">' +
        '<i class="bi ' + iconClass + ' me-2"></i>' +
        message +
        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
      '</div>'
    );

    $('body').append(notification);

    setTimeout(function () {
      notification.alert('close');
    }, 3000);
  }

  function reorganizeLayout() {
    const container = $('.row');
    const items = $('.col-md-6');
    
    if (items.length > 0) {
      container.html('');
      items.each(function() {
        container.append($(this));
      });
    }
  }
});