jQuery(document).ready(function ($) {
  const form = $(".save-cart-form");

  if (!form.length) return;

  // NEW: initialize notices container (uses the same wrapper you already target)
  if (jQuery('.save-carts-notices').length) {
  SaveCartsNotices.init('.save-carts-notices');
} else {
  SaveCartsNotices.init('.woocommerce-form-coupon-toggle');
}

  form.on("submit", function (e) {
    e.preventDefault();

    const cartName = $("#cart_name").val();

    // OLD:
    // if (!cartName.trim()) {
    //   mostrarMensaje("Debes escribir un nombre para el carrito.", false);
    //   return;
    // }

    // NEW:
    if (!cartName.trim()) {
      SaveCartsNotices.show("Please enter a cart name.", false);
      return;
    }

    $.ajax({
      type: "POST",
      url: save_cart_ajax_obj.ajax_url,
      data: {
        action: "save_cart_ajax",
        cart_name: cartName,
        nonce: save_cart_ajax_obj.nonce,
      },
      success: function (response) {
        const success = response.success;
        const message = response.data?.message || "No message returned.";
        const mostrarAcciones = response.data?.show_actions || false;

        // OLD:
        // mostrarMensaje(message, success, mostrarAcciones);

        // NEW:
        SaveCartsNotices.show(message, success);

        // NEW: append "Empty the cart" + "Continue shopping" buttons to the success notice
        if (success && mostrarAcciones) {
          const $notice = $(".woocommerce-form-coupon-toggle .woocommerce-message").last();

          const emptyBtn = $("<button>")
            .text("Empty the cart")
            .addClass("button")
            .css({ marginLeft: "10px" })
            .on("click", function () {
              $.post(
                save_cart_ajax_obj.ajax_url,
                {
                  action: "clear_saved_cart_ajax",
                  nonce: save_cart_ajax_obj.nonce,
                }
              ).done(function (response) {
                if (response.success && response.data?.redirect_url) {
                  window.location.href = response.data.redirect_url;
                } else {
                  SaveCartsNotices.show(response.data?.message || "Error clearing cart.", false);
                }
              }).fail(function () {
                SaveCartsNotices.show("Unexpected error while clearing the cart.", false);
              });
            });

          const continueBtn = $('<button>')
            .text('Continue shopping')
            .addClass('button')
            .css({ marginLeft: '10px' })
            .on('click', function () {
              window.location.href = save_cart_ajax_obj.shop_url;
            });

          $notice.append("<br><br>").append(emptyBtn).append(continueBtn);
        }
      },
      error: function () {
        // OLD:
        // mostrarMensaje("Error inesperado al guardar el carrito.", false);

        // NEW:
        SaveCartsNotices.show("Unexpected error while saving the cart.", false);
      },
    });
  });

  // OLD: legacy inline notice function (now replaced by SaveCartsNotices)
  /*
  function mostrarMensaje(texto, exito, mostrarAcciones = false) {
    const container = form.closest(".woocommerce-form-coupon-toggle");
    container.find(".woocommerce-error, .woocommerce-message").remove();

    const clase = exito ? "woocommerce-message" : "woocommerce-error";
    const msg = $("<div>").addClass(clase).text(texto);

    if (mostrarAcciones && exito) {
      const vaciarBtn = $("<button>")
        .text("Empty the cart")
        .addClass("button")
        .css({ marginLeft: "10px" })
        .on("click", function () {
          $.post(
            save_cart_ajax_obj.ajax_url,
            {
              action: "clear_saved_cart_ajax",
              nonce: save_cart_ajax_obj.nonce,
            },
            function (response) {
              if (response.success && response.data.redirect_url) {
                window.location.href = response.data.redirect_url;
              } else {
                alert(response.data?.message || "Error clearing cart.");
              }
            }
          ).fail(function (xhr, status, error) {
            alert("Unexpected error while clearing the cart.");
            console.error("❌ AJAX error:", error);
          });
        });

      const seguirBtn = $('<button>')
        .text('Continue shopping')
        .addClass('button')
        .css({ marginLeft: '10px' })
        .on('click', function () {
          window.location.href = save_cart_ajax_obj.shop_url;
        });

      msg.append("<br><br>").append(vaciarBtn).append(seguirBtn);
    }

    container.prepend(msg);
  }
  */
});


// ===== Delete Cart (no popups) =====

jQuery(document).on("click", ".delete-cart-button", function (e) {
  e.preventDefault();

  const $btn = jQuery(this);
  const cartId = $btn.data("cart-id");

  if (!cartId) {
    SaveCartsNotices.show("Invalid cart id.", false, 0); // 0 = no autohide
    return;
  }

  // OLD (browser confirm + alert):
  // if (!confirm("Are you sure you want to delete this cart?")) {
  //   return;
  // }

  // NEW: inline confirm with two buttons inside a WooCommerce-style notice
  SaveCartsNotices.clear();
  const $wrap = jQuery('<div class="sc-confirm-delete-wrap">');
  const $text = jQuery('<span>').text('Delete this cart? ');
  const $yes  = jQuery('<button type="button">').addClass('button').text('Delete');
  const $no   = jQuery('<button type="button">').addClass('button').css({ marginLeft: '8px' }).text('Cancel');

  $wrap.append($text).append($yes).append($no);

  // Show as error style (destructive) and keep it until user acts
  SaveCartsNotices.show($wrap, false, 0);

  // Cancel button
  $no.on('click', () => SaveCartsNotices.clear());

  // Confirm button
  $yes.on('click', function () {
    $yes.prop('disabled', true).text('Deleting…');

    const $row = $btn.closest('tr.saved-cart-row');
    const prevHtml = $btn.html();
    $btn.prop('disabled', true).html('…');

    jQuery.post(save_cart_ajax_obj.ajax_url, {
      action: 'delete_saved_cart_ajax',
      cart_id: cartId,
      nonce: save_cart_ajax_obj.nonce
    })
    .done(function (response) {
      if (response && response.success) {
        if ($row.length) $row.remove();
        SaveCartsNotices.show(response.data?.message || 'Cart deleted successfully.', true, 0);
      } else {
        SaveCartsNotices.show(response?.data?.message || 'Error deleting cart.', false, 0);
      }
    })
    .fail(function () {
      SaveCartsNotices.show('Unexpected error while deleting the cart.', false, 0);
    })
    .always(function () {
      $btn.prop('disabled', false).html(prevHtml);
    });
  });
});