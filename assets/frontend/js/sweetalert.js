/**
 * Frontend Dashboard - Native Modern Modal & Toast System
 * 100% Lightweight replacement for SweetAlert / SweetAlert2
 *
 * @package Frontend Dashboard
 */

(function (window, document) {
  'use strict';

  var activeModal = null;
  var timerInterval = null;
  var toastContainer = null;

  // SVG Icon Templates
  var ICONS = {
    success: '<div class="fed-modal-icon fed-modal-icon-success">' +
      '<svg class="fed-svg-checkmark" viewBox="0 0 52 52">' +
      '<circle class="fed-svg-circle" cx="26" cy="26" r="23" fill="none"/>' +
      '<path class="fed-svg-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>' +
      '</svg></div>',
    error: '<div class="fed-modal-icon fed-modal-icon-error">' +
      '<svg class="fed-svg-cross" viewBox="0 0 52 52">' +
      '<circle class="fed-svg-circle" cx="26" cy="26" r="23" fill="none"/>' +
      '<path class="fed-svg-x1" fill="none" d="M16 16 36 36"/>' +
      '<path class="fed-svg-x2" fill="none" d="M36 16 16 36"/>' +
      '</svg></div>',
    warning: '<div class="fed-modal-icon fed-modal-icon-warning">' +
      '<svg class="fed-svg-warning" viewBox="0 0 52 52">' +
      '<circle class="fed-svg-circle" cx="26" cy="26" r="23" fill="none"/>' +
      '<line class="fed-svg-line" x1="26" y1="15" x2="26" y2="29" stroke-width="3.5" stroke-linecap="round"/>' +
      '<circle class="fed-svg-dot" cx="26" cy="37" r="2.5"/>' +
      '</svg></div>',
    info: '<div class="fed-modal-icon fed-modal-icon-info">' +
      '<svg class="fed-svg-info" viewBox="0 0 52 52">' +
      '<circle class="fed-svg-circle" cx="26" cy="26" r="23" fill="none"/>' +
      '<circle class="fed-svg-dot" cx="26" cy="16" r="2.5"/>' +
      '<line class="fed-svg-line" x1="26" y1="23" x2="26" y2="37" stroke-width="3.5" stroke-linecap="round"/>' +
      '</svg></div>',
    question: '<div class="fed-modal-icon fed-modal-icon-question">' +
      '<svg class="fed-svg-info" viewBox="0 0 52 52">' +
      '<circle class="fed-svg-circle" cx="26" cy="26" r="23" fill="none"/>' +
      '<circle class="fed-svg-dot" cx="26" cy="37" r="2.5"/>' +
      '<path class="fed-svg-line" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" d="M20 20a6 6 0 0 1 11.5 2.5c0 3-4.5 4.5-4.5 7.5"/>' +
      '</svg></div>'
  };

  /**
   * Helper to ensure DOM modal elements exist.
   */
  function ensureModalDOM() {
    var backdrop = document.getElementById('fed-modal-backdrop');
    if (!backdrop) {
      backdrop = document.createElement('div');
      backdrop.id = 'fed-modal-backdrop';
      backdrop.className = 'fed-modal-backdrop';
      backdrop.innerHTML =
        '<div class="fed-modal-card" role="dialog" aria-modal="true">' +
          '<button type="button" class="fed-modal-close-btn" id="fed-modal-close-btn" aria-label="Close">' +
            '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">' +
              '<line x1="18" y1="6" x2="6" y2="18"></line>' +
              '<line x1="6" y1="6" x2="18" y2="18"></line>' +
            '</svg>' +
          '</button>' +
          '<div class="fed-modal-icon-wrap" id="fed-modal-icon-wrap"></div>' +
          '<div class="fed-modal-content">' +
            '<h3 class="fed-modal-title" id="fed-modal-title"></h3>' +
            '<div class="fed-modal-text" id="fed-modal-text"></div>' +
          '</div>' +
          '<div class="fed-modal-actions" id="fed-modal-actions">' +
            '<button type="button" class="fed-modal-btn fed-modal-btn-confirm" id="fed-modal-confirm-btn">OK</button>' +
            '<button type="button" class="fed-modal-btn fed-modal-btn-cancel" id="fed-modal-cancel-btn">Cancel</button>' +
          '</div>' +
          '<div class="fed-modal-timer-bar" id="fed-modal-timer-bar"></div>' +
        '</div>';
      document.body.appendChild(backdrop);
    }
    return backdrop;
  }

  /**
   * Helper to ensure Toast container exists.
   */
  function ensureToastContainer() {
    if (!toastContainer || !document.body.contains(toastContainer)) {
      toastContainer = document.getElementById('fed-toast-container');
      if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'fed-toast-container';
        toastContainer.className = 'fed-toast-container';
        document.body.appendChild(toastContainer);
      }
    }
    return toastContainer;
  }

  /**
   * Core Modal Controller
   */
  var fedModal = {
    /**
     * Main fire method (compatible with swal(options) / swal(title, text, type))
     */
    fire: function () {
      var args = Array.prototype.slice.call(arguments);
      var opts = {};

      if (typeof args[0] === 'object' && args[0] !== null) {
        opts = Object.assign({}, args[0]);
      } else if (typeof args[0] === 'string') {
        opts.title = args[0];
        if (typeof args[1] === 'string') {
          opts.text = args[1];
        }
        if (typeof args[2] === 'string') {
          opts.type = args[2];
        }
      }

      // Normalization
      var title = opts.title || '';
      var text = opts.text || (opts.html ? opts.html : '');
      var type = (opts.type || opts.icon || 'info').toLowerCase();
      var showConfirm = opts.showConfirmButton !== false;
      var showCancel = opts.showCancelButton === true;
      var confirmText = opts.confirmButtonText || (opts.confirmButton && opts.confirmButton.text) || 'OK';
      var cancelText = opts.cancelButtonText || 'Cancel';
      var confirmColor = opts.confirmButtonColor || '';
      var cancelColor = opts.cancelButtonColor || '';
      var timer = typeof opts.timer === 'number' && opts.timer > 0 ? opts.timer : null;
      var allowOutsideClick = opts.allowOutsideClick !== false;
      var allowEscapeKey = opts.allowEscapeKey !== false;
      var showCloseBtn = opts.showCloseButton === true;

      // Close any running instance
      fedModal.close();

      var backdrop = ensureModalDOM();
      var iconWrap = document.getElementById('fed-modal-icon-wrap');
      var titleEl = document.getElementById('fed-modal-title');
      var textEl = document.getElementById('fed-modal-text');
      var confirmBtn = document.getElementById('fed-modal-confirm-btn');
      var cancelBtn = document.getElementById('fed-modal-cancel-btn');
      var closeBtn = document.getElementById('fed-modal-close-btn');
      var timerBar = document.getElementById('fed-modal-timer-bar');

      // Populate Title
      if (title) {
        titleEl.innerHTML = title;
        titleEl.style.display = 'block';
      } else {
        titleEl.innerHTML = '';
        titleEl.style.display = 'none';
      }

      // Populate Text / HTML
      if (text) {
        textEl.innerHTML = text;
        textEl.style.display = 'block';
      } else {
        textEl.innerHTML = '';
        textEl.style.display = 'none';
      }

      // Populate Icon
      if (ICONS[type]) {
        iconWrap.innerHTML = ICONS[type];
        iconWrap.style.display = 'flex';
      } else {
        iconWrap.innerHTML = '';
        iconWrap.style.display = 'none';
      }

      // Action Buttons
      if (showConfirm) {
        confirmBtn.innerText = confirmText;
        confirmBtn.style.display = 'inline-flex';
        if (confirmColor) {
          confirmBtn.style.backgroundColor = confirmColor;
          confirmBtn.style.borderColor = confirmColor;
        } else {
          confirmBtn.style.backgroundColor = '';
          confirmBtn.style.borderColor = '';
        }
      } else {
        confirmBtn.style.display = 'none';
      }

      if (showCancel) {
        cancelBtn.innerText = cancelText;
        cancelBtn.style.display = 'inline-flex';
        if (cancelColor) {
          cancelBtn.style.backgroundColor = cancelColor;
          cancelBtn.style.borderColor = cancelColor;
          cancelBtn.style.color = '#ffffff';
        } else {
          cancelBtn.style.backgroundColor = '';
          cancelBtn.style.borderColor = '';
          cancelBtn.style.color = '';
        }
      } else {
        cancelBtn.style.display = 'none';
      }

      closeBtn.style.display = showCloseBtn ? 'flex' : 'none';

      // Timer Bar
      timerBar.style.width = '0%';
      timerBar.style.display = timer ? 'block' : 'none';

      // Promise Execution Handlers
      var resolveCallbacks = [];
      var rejectCallbacks = [];
      var state = 'pending';

      function doResolve(val) {
        if (state !== 'pending') return;
        state = 'fulfilled';
        fedModal.close();
        for (var i = 0; i < resolveCallbacks.length; i++) {
          try {
            resolveCallbacks[i](val);
          } catch (e) {
            console.error(e);
          }
        }
      }

      function doReject(reason) {
        if (state !== 'pending') return;
        state = 'rejected';
        fedModal.close();
        if (rejectCallbacks.length > 0) {
          for (var i = 0; i < rejectCallbacks.length; i++) {
            try {
              rejectCallbacks[i](reason);
            } catch (e) {
              console.error(e);
            }
          }
        } else {
          // If no reject callback was provided, resolve gracefully with { dismiss: reason }
          for (var j = 0; j < resolveCallbacks.length; j++) {
            try {
              resolveCallbacks[j]({ value: false, isConfirmed: false, isDismissed: true, dismiss: reason });
            } catch (e2) {
              console.error(e2);
            }
          }
        }
      }

      // Event Listeners
      function handleConfirm(e) {
        if (e) e.preventDefault();
        var res = { value: true, isConfirmed: true, isDismissed: false };
        // For simple boolean checks `if (result)`
        doResolve(res);
      }

      function handleCancel(e) {
        if (e) e.preventDefault();
        doReject('cancel');
      }

      function handleBackdrop(e) {
        if (allowOutsideClick && e.target === backdrop) {
          doReject('overlay');
        }
      }

      function handleKeydown(e) {
        if (allowEscapeKey && (e.key === 'Escape' || e.keyCode === 27)) {
          doReject('esc');
        }
      }

      confirmBtn.onclick = handleConfirm;
      cancelBtn.onclick = handleCancel;
      closeBtn.onclick = function () { doReject('close'); };
      backdrop.onclick = handleBackdrop;
      document.addEventListener('keydown', handleKeydown);

      // Show Backdrop & Modal Card
      requestAnimationFrame(function () {
        backdrop.classList.add('fed-modal-active');
        if (showConfirm) {
          confirmBtn.focus();
        }
      });

      // Timer Logic
      if (timer) {
        timerBar.style.transitionDuration = timer + 'ms';
        requestAnimationFrame(function () {
          timerBar.style.width = '100%';
        });

        var timerTimeout = setTimeout(function () {
          // Auto-close on timer
          if (rejectCallbacks.length > 0) {
            doReject('timer');
          } else {
            doResolve({ value: true, isConfirmed: false, isDismissed: true, dismiss: 'timer' });
          }
        }, timer);
      }

      activeModal = {
        backdrop: backdrop,
        handleKeydown: handleKeydown,
        timerTimeout: timerTimeout
      };

      // Construct Hybrid Promise
      var promise = {
        then: function (onFulfilled, onRejected) {
          if (typeof onFulfilled === 'function') {
            resolveCallbacks.push(onFulfilled);
          }
          if (typeof onRejected === 'function') {
            rejectCallbacks.push(onRejected);
          }
          return promise;
        },
        catch: function (onRejected) {
          if (typeof onRejected === 'function') {
            rejectCallbacks.push(onRejected);
          }
          return promise;
        },
        finally: function (callback) {
          if (typeof callback === 'function') {
            resolveCallbacks.push(callback);
            rejectCallbacks.push(callback);
          }
          return promise;
        }
      };

      return promise;
    },

    /**
     * Close currently open modal
     */
    close: function () {
      if (activeModal) {
        if (activeModal.timerTimeout) {
          clearTimeout(activeModal.timerTimeout);
        }
        if (activeModal.handleKeydown) {
          document.removeEventListener('keydown', activeModal.handleKeydown);
        }
        if (activeModal.backdrop) {
          activeModal.backdrop.classList.remove('fed-modal-active');
        }
        activeModal = null;
      }
    },

    /**
     * Toast notification (Bottom Right Floating Pill)
     */
    toast: function (options) {
      var opts = {};
      if (typeof options === 'string') {
        opts.title = options;
        if (arguments[1] === true || arguments[1] === 'error') {
          opts.type = 'error';
        } else {
          opts.type = 'success';
        }
      } else if (typeof options === 'object') {
        opts = Object.assign({}, options);
      }

      var container = ensureToastContainer();
      var type = (opts.type || opts.icon || 'success').toLowerCase();
      var message = opts.title || opts.message || opts.text || '';
      var duration = typeof opts.timer === 'number' ? opts.timer : 3500;

      var iconHtml = '<i class="fas fa-check-circle"></i>';
      if (type === 'error') {
        iconHtml = '<i class="fas fa-exclamation-circle"></i>';
      } else if (type === 'warning') {
        iconHtml = '<i class="fas fa-exclamation-triangle"></i>';
      } else if (type === 'info') {
        iconHtml = '<i class="fas fa-info-circle"></i>';
      }

      var toast = document.createElement('div');
      toast.className = 'fed-toast';
      toast.innerHTML =
        '<div class="fed-toast-icon fed-toast-icon-' + type + '">' + iconHtml + '</div>' +
        '<div class="fed-toast-body">' + message + '</div>' +
        '<button type="button" class="fed-toast-close" aria-label="Dismiss">' +
          '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>' +
        '</button>';

      container.appendChild(toast);

      // Animate in
      requestAnimationFrame(function () {
        toast.classList.add('fed-toast-show');
      });

      function removeToast() {
        toast.classList.remove('fed-toast-show');
        setTimeout(function () {
          if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
          }
        }, 300);
      }

      toast.querySelector('.fed-toast-close').onclick = removeToast;

      if (duration > 0) {
        setTimeout(removeToast, duration);
      }

      return toast;
    },

    // Convenience Shortcuts
    success: function (title, text) {
      return fedModal.fire({ title: title, text: text, type: 'success' });
    },
    error: function (title, text) {
      return fedModal.fire({ title: title, text: text, type: 'error' });
    },
    warning: function (title, text) {
      return fedModal.fire({ title: title, text: text, type: 'warning' });
    },
    info: function (title, text) {
      return fedModal.fire({ title: title, text: text, type: 'info' });
    },
    confirm: function (title, text) {
      return fedModal.fire({
        title: title,
        text: text,
        type: 'warning',
        showCancelButton: true
      });
    }
  };

  // Expose globally
  window.fedModal = fedModal;
  window.fed_toast = fedModal.toast;
  window.swal = fedModal.fire;
  window.sweetAlert = fedModal.fire;
  window.Sweetalert2 = fedModal.fire;

})(window, document);
