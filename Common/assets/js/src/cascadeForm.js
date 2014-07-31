var OLCS = OLCS || {};

/**
 * Cascade Form
 *
 * This component should be bound to a form in which it each section
 * (usually defined by a top-level fieldset) relates to the one which
 * follows it; that is the content of the following fieldset is affected
 * in some way by the input received in the current one.
 */

OLCS.cascadeForm = (function(document, $, undefined) {

  "use strict";

  return function init(options) {
    var form = $(options.form);
    var previousFieldset;
    var cascade = options.cascade || true;
    var onSubmit = options.submit;
    //var ignoreElements = options.ignoreElements || ["id", "version"];

    /**
     * by using a closure we ensure this function is safe
     * to bind inside loops etc
     */
    function clearFieldset(target) {
      /**
       * the actual event handler simply finds all inputs in the
       * target fieldset and clears them out
       *
       * @TODO only checkboxes are supported at the moment, easy to
       * change though
       */
      return function clear() {
        var elems = $(target).find(":input");
        $.each(elems, function(i, elem) {
          elem = $(elem);
          if (elem.is(":checked")) {
            elem.removeAttr("checked");
          }
        });
        // ensure the change notification cascades down the line
        $(target).trigger("change");
      };
    }

    /**
     * iterate over the form, checking the relevant rulesets
     * we generally expect each ruleset to apply to a fieldset
     * but allow for exceptions. Once we've found a fieldset or
     * element, we invoke its predicate which can either be a
     * bool or function
     */
    function checkForm() {
      for (var fieldset in options.rulesets) {
        var ruleset = options.rulesets[fieldset];

        if (!$.isPlainObject(ruleset)) {
          triggerRule(fieldset, "*", ruleset);
          continue;
        }

        for (var selector in ruleset) {
          var rule = ruleset[selector];
          triggerRule(fieldset, selector, rule);
        }
      }
    }

    /**
     * invoke a rule against an element or fieldset. The
     * end result will be the showing or hiding of the
     * relevant element
     */
    function triggerRule(group, selector, rule) {
      var show;
      var elem;

      if ($.isFunction(rule)) {
        show = rule.call(form);
      } else {
        show = rule;
      }

      elem = findContainer(group, selector);

      if (show) {
        elem.show();
      } else {
        elem.hide();
      }
    }

    /**
     * find a container or element based on a group (i.e. a fieldset)
     * and selector. Takes a special asterisk(*) argument to represent
     * the group itself rather than a child
     */
    function findContainer(group, selector) {
      if (selector === "*") {
        return form.find("[name^="+group+"]").parents("fieldset:last");
      }

      // the only other selector we support for now is a name=value pair,
      // e.g. "get me element X with value Y. This is *very* specifically
      // for the first use case of this component; add to it as necessary
      if (selector.search("-") !== -1) {
        var parts = selector.split("=");
        var str = "[name=" + group + "\\[" + parts[0] + "\\]][value=" + parts[1] + "]";
        return form.find(str).parents("label:last");
      }

      throw new Error("Unsupported selector: '" + selector + "'");
    }

    /*
     * first things first, find the top-level fieldsets and bind some
     * handlers such that when they change, the event cascades to
     * all subsequent fieldsets emptying them out
     */
    if (cascade) {
      for (var fieldset in options.rulesets) {
        var current = findContainer(fieldset, "*");
        if (previousFieldset) {
          $(previousFieldset).on(
            "change",
            clearFieldset(current)
          );
        }
        previousFieldset = current;
      }
    }

    if (onSubmit) {
      form.on("submit", onSubmit.bind(form));
    }

    // always append a hidden element to the form so we know it's a JS submission
    form.append("<input type='hidden' name='js-submit' value='1' />");

    checkForm();

    $(document).on("change", form, checkForm);
  };

}(document, window.jQuery));
