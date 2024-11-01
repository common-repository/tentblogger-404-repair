jQuery(function($) {

  $('.repair-link').click(function(evt) {
    evt.preventDefault();
    var sUrl = $(this).attr('class').split(' ')[1];
    window.location = window.location + '&remove_page=' + sUrl;
  });
  
  $('.repair-all-link').click(function(evt) {
    evt.preventDefault();
    window.location = window.location + '&remove_page=all';
  });
  
});