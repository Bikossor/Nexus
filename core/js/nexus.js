$(document).ready(function() {
  var vid = document.getElementById("video");
  vid.volume = 0.01;

  $('a.jpg').click(function(e) {
    e.preventDefault();

    $('div.modal').addClass('visible');
    $('img.lb_image').css('display', 'block');
    $('img.lb_image').attr('src', this.href).on('load', function(e) {

    });
  });

  $('a.mp4').click(function(e) {
    e.preventDefault();

    $('div.modal').addClass('visible');
    $('video.lb_video').attr('src', this.href);
    $('video.lb_video').css('display', 'block');
  });

  /*Close the modal */
  $(document).keypress(function(e) {
    if (e.keyCode === 27) {
      $('div.modal').removeClass('visible');
      $('img.lb_image').attr('src', './nexus_core/Loading_icon.gif');
      $('img.lb_image').css('display', 'none');

      $('video.lb_video').attr('src', '');
      $('video.lb_video').css('display', 'none');
    }
  });
});
