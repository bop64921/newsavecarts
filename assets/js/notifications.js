/**
 * Save Carts - Notifications Utility
 * WooCommerce-like inline notices with a tiny API.
 * 
 * Usage:
 *   SaveCartsNotices.init('.save-carts-notices'); // optional (auto-detects if omitted)
 *   SaveCartsNotices.success('Cart saved successfully.');
 *   SaveCartsNotices.error('Something went wrong.');
 *   SaveCartsNotices.show('Custom message', true);  // true=success, false=error
 *   SaveCartsNotices.clear();                       // remove existing notices
 */

(function (window, $) {
  'use strict';

  const DEFAULT_CONTAINER = '.save-carts-notices';
  let containerSelector = DEFAULT_CONTAINER;

  function getContainer() {
    const $c = $(containerSelector);
    return $c.length ? $c : null;
  }

  function createNotice(message, isSuccess) {
    const cls = isSuccess ? 'woocommerce-message' : 'woocommerce-error';
    const $n = $('<div>')
      .addClass(cls)
      .attr({
        role: 'status',
        'aria-live': 'polite',
        'aria-atomic': 'true'
      })
      .text(message);
    return $n;
  }

  const SaveCartsNotices = {
    /**
     * Optionally choose where to render notices.
     * @param {string} selector CSS selector for the container
     */
    init(selector) {
      if (selector && typeof selector === 'string') {
        containerSelector = selector;
      }
    },

    /**
     * Show a notice (success or error).
     * @param {string|HTMLElement|jQuery} message
     * @param {boolean} isSuccess
     * @param {number} autoHideMs  (default 4000; 0 = don't autohide)
     */
    show(message, isSuccess = true, autoHideMs = 0) {
      const $container = getContainer();
      if (!$container) {
        // Fallback: alert if we don't have a container
        if (typeof message === 'string') alert(message);
        return;
      }

      // Remove previous notices
      $container.find('.woocommerce-message, .woocommerce-error').remove();

      // Accept HTML/jQuery nodes too
      let $notice;
      if (message instanceof $ || (message && message.nodeType === 1)) {
        $notice = $('<div>').addClass(isSuccess ? 'woocommerce-message' : 'woocommerce-error')
                            .append(message);
      } else {
        $notice = createNotice(String(message), isSuccess);
      }

      $container.prepend($notice);

      if (autoHideMs > 0) {
        setTimeout(() => {
          $notice.fadeOut(150, () => $notice.remove());
        }, autoHideMs);
      }
    },

    /**
     * Convenience wrappers
     */
    success(msg, autoHideMs = 4000) {
      this.show(msg, true, autoHideMs);
    },

    error(msg, autoHideMs = 4000) {
      this.show(msg, false, autoHideMs);
    },

    /**
     * Remove existing notices immediately.
     */
    clear() {
      const $container = getContainer();
      if ($container) {
        $container.find('.woocommerce-message, .woocommerce-error').remove();
      }
    }
  };

  // Expose globally
  window.SaveCartsNotices = SaveCartsNotices;

})(window, jQuery);