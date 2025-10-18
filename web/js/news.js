$(document).ready(function () {
  var categoryLinks = $('[id^=cat-]');
  var form = $('#newsForm');

  categoryLinks.on('click', function () {
    $('#keyword').val('');
    $('[id^=cat-]').css({ 'font-weight': '', 'text-decoration': '' }); // reset semua
    $(this).css({ 'font-weight': 'bold', 'text-decoration': 'underline' }); // aktifkan yang diklik
  });

  form.on('submit', function (e) {
    e.preventDefault();

    let pathParts = window.location.pathname.split('/');
    let category = pathParts[pathParts.length - 1];
    if (category === 'my-news-portal' || category === '') category = 'all';

    let action = '/my-news-portal';
    if (category && category !== 'all')
      action += '/' + encodeURIComponent(category);

    $(this).attr('action', action);
    this.submit();
  });
});
