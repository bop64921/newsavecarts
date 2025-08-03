jQuery(document).ready(function($) {
    const form = $('.save-cart-form');

    if (!form.length) return;

    form.on('submit', function(e) {
        e.preventDefault();

        const cartName = $('#cart_name').val();

        if (!cartName.trim()) {
            mostrarMensaje('Debes escribir un nombre para el carrito.', false);
            return;
        }

        $.ajax({
            type: 'POST',
            url: save_cart_ajax_obj.ajax_url,
            data: {
                action: 'save_cart_ajax',
                cart_name: cartName,
                nonce: save_cart_ajax_obj.nonce
            },
            success: function(response) {
                const success = response.success;
                const message = response.data?.message || 'Guardado sin mensaje.';
                const mostrarAcciones = response.data?.show_actions || false; // 👈 Nuevo
                mostrarMensaje(message, success, mostrarAcciones); // 👈 Pasa el flag
            },
            error: function(xhr, status, error) {
                mostrarMensaje('Error inesperado al guardar el carrito.', false);
                console.error('❌ AJAX error:', error);
            }
        });
    });

    function mostrarMensaje(texto, exito, mostrarAcciones = false) {
        const container = form.closest('.woocommerce-form-coupon-toggle');
        container.find('.woocommerce-error, .woocommerce-message').remove();

        const clase = exito ? 'woocommerce-message' : 'woocommerce-error';
        const msg = $('<div>').addClass(clase).text(texto);

        // 👇 Si queremos mostrar botones adicionales
        if (mostrarAcciones && exito) {
            const vaciarBtn = $('<button>')
                .text('Empty the cart')
                .addClass('button')
                .css({ marginLeft: '10px' })
                .on('click', function () {
                    $.post(save_cart_ajax_obj.ajax_url, {
                        action: 'vaciar_carrito',
                        nonce: save_cart_ajax_obj.nonce
                    }, function () {
                        location.reload(); // o redirigir a donde tú quieras
                    });
                });

            const seguirBtn = $('<button>')
                .text('Continue shopping')
                .addClass('button')
                .css({ marginLeft: '10px' })
                .on('click', function () {
                    msg.fadeOut();
                });

            msg.append('<br><br>').append(vaciarBtn).append(seguirBtn);
        }

        container.prepend(msg);
    }
});