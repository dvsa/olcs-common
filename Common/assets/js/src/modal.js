var OLCS = OLCS || {};

/*
OLCS.modal.init({
  trigger: '.js-modal',
  selector: '.modal',
  content: '#main'
});
*/

OLCS.modal = (function(document, $, undefined) {

  'use strict';

  /**
   * local variable declarations
   * and public export
   */
  var exports = {};

  /**
   * private interface
   */
  function hide(selector) {
    $(selector).hide();
    $(selector).prev().hide();
  }

  function show(data, wrapper, selector, content) {
    if (content) {
      data = $(data).find(content).html();
    }
    $(selector).append(data);

    $(wrapper).prev().show();
    $(wrapper).show();
  }

  /**
   * public interface
   */
  exports.init = function(options) {
    var trigger  = options.trigger;
    var selector = options.selector;
    var wrapper  = options.wrapper || options.selector + '__wrapper';
    var cache    = {};

    hide(wrapper);

    $(document).on('click', trigger, function(e) {
      e.preventDefault();

      var key = $(this).attr('href');

      if (cache[key]) {
        return show(cache[key], wrapper, selector, options.content);
      }

      $.ajax({
        url: key,
        success: function(data) {
          cache[key] = data;
          show(data, wrapper, selector, options.content);
        }
      });
    });

    $(document).on('click', selector + '__close', function(e) {
      e.preventDefault();
      hide(wrapper);
    });
  };

  return exports;

}(document, window.jQuery));
