(function ($, Drupal) {
  Drupal.behaviors.shoppingCart = {
    attach: function () {

      var changeQuantity = function (selector, a) {
        var oldVal = +$(selector).val();
        var newVal = oldVal + a;
        if (newVal < 0) {
          newVal = 0;
        }
        $(selector).val(String(newVal));
        return newVal;
      }

      var changeQuantityCart = function (a, fieldSelector, context) {
        var index = $(context).index(fieldSelector);
        var selector = '#edit-edit-quantity-' + index;
        changeQuantity(selector, a);

        // changeTotalFieldCart
        var parent = $(context).parents('tr');
        var defaultPrice = parent.children('.views-field-commerce-unit-price').text();
        defaultPrice = +defaultPrice.split('$')[1];
        var selectorVal = +$(selector).val();
        var totalPrice = defaultPrice * selectorVal;
        parent.children('.views-field-commerce-total').text('$' + totalPrice.toFixed(2));

        $('#edit-submit').trigger('click');
      }

      $(document).ready(function () {
        $('.plus').once('plusClick').click(function () {
          changeQuantityCart(1, '.plus', this);
          $('#edit-submit').trigger('click');
        });
        $('.minus').once('minusClick').click(function () {
          changeQuantityCart(-1, '.minus', this);
          $('#edit-submit').trigger('click');
        });
        $('.form-text').once('changeValue').change(function () {
          $('#edit-submit').trigger('click');
        });
        $('.button.delete-order-item').click(function () {
          var index = $(this).index('.button.delete-order-item');
          var selector = '#edit-edit-quantity-' + index;
          $(selector).val('0');
          $('#edit-submit').trigger('click');
        });
      });
    }
  };
})(jQuery, Drupal);
