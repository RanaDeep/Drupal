(function ($) {
  Drupal.behaviors.animationAddToCart = {
    attach: function () {

      $(document).ready(function () {
        $('.add-to-cart-button .form-submit').once('animateMovingProduct').click(function (e) {
          var img = $(e.target).parents('.add-to-cart-form').siblings('.product-image');
          $(img)
            .clone()
            .css({ 'position': 'absolute', 'z-index': '11100', top: $(img).offset().top, left: $(img).offset().left })
            .appendTo("body")
            .animate({
              opacity: 0.5,
              left: $(".my-cart-info").offset().left,
              top: $(".my-cart-info").offset().top,
              width: 20
            }, 1000, function () {
              $(this).remove();
            });

          setTimeout(function () {
            $('.my-cart-info').click();
          }, 1000);
        });
        if (matchMedia('(max-width: 480px)').matches) {
          $('.not-front .page-product-item .commerce-add-to-cart .form-submit').click(function (e) {
            var input = $('#edit-quantity');
            var wrap = $('<strong></strong>').text('+' + input.val());
            $(wrap)
              .css({
                'font-size': '30px',
                'position': 'absolute',
                'color': '#424242',
                'z-index': '11100',
                top: $(input).offset().top,
                left: $(input).offset().left
              })
              .appendTo("body")
              .animate({
                opacity: 0.5,
                left: $(".menu-open").offset().left,
                top: $(".menu-open").offset().top
              }, 1000, function () {
                $(this).remove();
                $('.my-cart-info').click();
              });
          });
        }

        $('.region-header .header-cart-block .view-empty').once('changeCartPrice').each(function () {
          $('.region-header .header-cart-block .my-cart-info').toggleClass('empty');
        });

      });

      $('.my-cart-info').once('toggleHeaderCart').click(function () {
        $('.region-header .header-cart-block .view-content').slideToggle(200);
        if ($('.region-header .header-cart-block .view-empty')) {
          $('.region-header .header-cart-block .view-empty').slideToggle(200);
        }
      });

      $(document).once('clickOutsideCart').click(function (e) {
        if ($('.region-header .header-cart-block .view-empty').is(':visible')) {
          if (!$(e.target).closest('.header-cart-block').length) {
            $('.region-header .header-cart-block .view-empty').slideToggle(200);
          }
        }
        if ($('.region-header .header-cart-block .view-content').is(':visible')) {
          if (!$(e.target).closest('.header-cart-block').length) {
            $('.region-header .header-cart-block .view-content').slideToggle(200);
          }
        }
      });
    },
  }
})(jQuery);
