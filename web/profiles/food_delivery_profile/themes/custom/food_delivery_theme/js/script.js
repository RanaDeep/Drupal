(function ($, Drupal) {

  function setGridMode(view) {
    if (!view.hasClass('grid-mode')) {
      view.addClass('grid-mode');
      $('.grid-display').addClass('active');
    }
  }

  function resetMode(view) {
    if (view.hasClass('inline-mode')) {
      view.removeClass('inline-mode');
      view.addClass('grid-mode');
    }
  }

  function changeToGridDisplay() {
    var $element = $('.grid-display');
    if (!$element.hasClass('active')) {
      $element.addClass('active');
      $('.inline-display').removeClass('active');
      if (!$element.parent().parent().hasClass('grid-mode')) {
        $element.parent().parent().addClass('grid-mode');
        $element.parent().parent().removeClass('inline-mode');
      }
    }
  }

  function changeToInlineDisplay() {
    var $element = $('.inline-display');
    if (!$element.hasClass('active')) {
      $element.addClass('active');
      $('.grid-display').removeClass('active');
      if (!$element.parent().parent().hasClass('inline-mode')) {
        $element.parent().parent().addClass('inline-mode');
        $element.parent().parent().removeClass('grid-mode');
      }
    }
  }

  Drupal.behaviors.foodDeliveryBehavior = {
    attach: function () {
      $(document).ready(function () {
        $('#slider .owl-item .additional-block-over-slide .view-product-of-the-day .views-row')
          .once('addProductOfTheDayLabel')
          .prepend('<div class="day-product-label">Product of the day</div>');
      });
      $('.filter-button').once('toggleFilter').click(function (e) {
        e.preventDefault();
        e.target.classList.toggle('open');
        $('.sidebar').slideToggle(200);
        if ($(window).width() < 1024 && $(window).width() > 768 ) {
          $('.frontpage-mobile-filters').slideToggle({
            duration: 200,
            start: function () {
              jQuery(this).css('display','flex');
            }
          });
        } else {
          $('.frontpage-mobile-filters').slideToggle({
            duration: 200,
            start: function () {
              jQuery(this).css('display', 'flex');
            }
          });
        }
      });
      $(window).once('removeInlineGridClasses').resize(function () {
        if ($(document).width() <= 1024) {
          resetMode($('.region-content .view-featured'));
          resetMode($('.region-content .view-new'));
          resetMode($('.region-content .view-sale'));
          resetMode($('.view-catalog'));
          changeToGridDisplay();
        }
      });
      $(document).once('hideMenuOnEscape').keydown(function (e) {
        var $menu = $('#menu');
        if (e.key === 'Escape' && $menu.is(':visible')) {
          $menu.fadeOut(300);
        }
      });
      $('#footer #footer-columns > .region:not(.region-footer-first) h2').once('toggleFooterMenu').click(function () {
        if ($(window).width() <= 768) {
          $(this).parent().children('.content').slideToggle(200);
          $(this).toggleClass('open-accordion');
        }
      });
      $('.view-categories')
        .once('capitalizeCategoriesMenuItems')
        .css('textTransform', 'capitalize');
      $('.view-categories h3')
        .once('hideCategoriesMenuParentChild')
        .click(function () {
          $(this).siblings('ul').toggle('fast');
        })
        .siblings('ul')
        .toggle('fast');
      $('.header .header-cart-block .view-footer')
        .once('moveFooterToContent')
        .appendTo('.header .header-cart-block .view-content');
      $('.menu-open')
        .once('openMainMenu')
        .click(function () {
          $('#menu').fadeIn(300);
          $('#block-food-delivery-theme-main-menu').css('display', 'block');
          $('html,body').css('overflow', 'hidden');
        });
      $('.menu-close')
        .once('closeMainMenu')
        .click(function () {
          $('#menu').fadeOut(300);
          $('#block-food-delivery-theme-main-menu').css('display', 'none');
          $('html,body').css('overflow', '');
        });
      $('.view-catalog').once('setGridMode').each(function () {
        setGridMode($(this));
      });
      $('.region-content .view-featured').once('setGridMode', setGridMode($(this)));
      $('.region-content .view-new').once('setGridMode', setGridMode($(this)));
      $('.region-content .view-sale').once('setGridMode', setGridMode($(this)));
      $('.grid-display').once('changeToGridDisplay').click(function () {
        changeToGridDisplay();
      });
      $('.inline-display').once('changeToInlineDisplay').click(function () {
        changeToInlineDisplay();
      });
      $('.faq-question').once('addCollapsedClass').each(function () {
        $(this).addClass('collapsed');
      });
    }
  };
})(jQuery, Drupal);

(function ($) {
  // After click on show/hide button.
  $('.faq-question').after().click(function () {
    $(this).closest('.faq').find('.faq-answer').slideToggle(200);
    $(this).toggleClass('expanded collapsed');
  });
})(jQuery);

(function ($) {
  Drupal.behaviors.linksToHowItWorks = {
    attach: function () {

      function scrollToBlock(id = false) {
        if (id) {
          var $element = $('div#' + id);
          if (id === $element.attr('id')) {

            $element
              .parents('.question-answer-wrapper')
              .siblings('.show-hide-button')
              .trigger('click');

            $('html, body').stop().animate({
              'scrollTop': $element.offset().top - ($element.height() + 110)
            }, 1500, 'swing', function () {});
          }
        }
      }

      $(document).ready(function () {
        var id = window.location.hash.substr(1);
        scrollToBlock(id);
      });

      $('nav[role="navigation"] a').once('scrollToBlock').click(function () {
        var id = window.location.hash.substr(1);
        scrollToBlock(id);
      });

    }
  };
})(jQuery);

(function ($, Drupal) {

  Drupal.behaviors.addToCartForm = {
    attach: function () {

      const totalPriceString = $('.page-product-item .group-right .field--name-price').text();
      const $totalPriceContainer = $('.page-product-item .group-right .total-price');
      const $input = $('.page-product-item .group-right .product-item-form .form-number');

      const changeQuantity = function (element, a) {
        let oldVal = +element.val();
        let newVal = oldVal + a;
        if (newVal < 1) {
          newVal = 1;
        }
        changeTotalPrice(newVal, $totalPriceContainer);
        return newVal;
      };

      const changeTotalPrice = function (multiplier, container) {
        const totalPriceValue = parseFloat(totalPriceString.substr(1, totalPriceString.length)) * multiplier;
        const totalPriceCurrency = totalPriceString.substr(0, 1);
        const totalPriceStr = totalPriceCurrency + String(totalPriceValue);

        container.text(totalPriceStr);
      };

      $(document).ready(function () {

        $totalPriceContainer.once('setPrice').each(function () {
          changeTotalPrice(1, $totalPriceContainer);
        });

        $input.once('addButtons').each(function () {
          const $plusButton = $('<span></span>').addClass('plus').text('+');
          const $minusButton = $('<span></span>').addClass('minus').text('-');
          $minusButton.insertBefore($(this));
          $plusButton.insertAfter($(this));
        });

        $input.once('validateQuantity').keyup(function () {
          $(this).val($(this).val().replace(/[^0-9\.]/g,''));
        });

        $('.page-product-item .group-right .product-item-form .minus').once('reduceQuantity').click(function () {
          const quantity = changeQuantity($input, -1);
          $input.val(String(quantity));
        });

        $('.page-product-item .group-right .product-item-form .plus').once('increaseQuantity').click(function () {
          const quantity = changeQuantity($input, 1);
          $input.val(String(quantity));
        });
      });

    },
  };

})(jQuery, Drupal)
