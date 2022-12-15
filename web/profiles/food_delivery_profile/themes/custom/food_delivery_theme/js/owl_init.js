(function ($, Drupal) {

  $(document).ready(function () {
    $(".clients .owl-carousel").owlCarousel({
      autoPlay: 3000,
      stopOnHover: true,
    });
    $(".slider .owl-carousel").owlCarousel({
      autoPlay: 3000,
      singleItem: true,
      stopOnHover: true,
    });
  });

})(jQuery);
