$(document).ready(function () {
  $('#cat-all').on('click', function () {
    $('#keyword').val('');
  });

  $('#searchForm').on('submit', function (e) {
    e.preventDefault();

    const params = new URLSearchParams(window.location.search);
    const category = params.get('category');

    let action = '/my-news-portal';
    if (category && category !== 'all') {
      action += '?category=' + encodeURIComponent(category);
    }

    $(this).attr('action', action);
    this.submit();
  });
});
