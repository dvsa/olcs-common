/**
 * @NOTE This could potentially be DRYed up, as this is going to be fairly common behaviour throughout LVA
 */
OLCS.ready(function() {
  "use strict";

  // @TODO OLCS.crudTableHandler(); ???
  var editButton = OLCS.conditionalButton({
    container: "form [data-group*='table']",
    label: "Edit",
    predicate: function (length, callback) {
      callback(length !== 1);
    }
  });

  var deleteButton = OLCS.conditionalButton({
    container: "form [data-group*='table']",
    label: "Delete",
    predicate: function (length, callback) {
      callback(length < 1);
    }
  });

  var F = OLCS.formHelper;

  $(document).on("click", ".table__header button, .table__wrapper input[type=submit]", function(e) {
    e.preventDefault();

    var button = $(this);
    var form   = $(this).parents("form");

    /**
     * We manually handle rendering the modal because we need to intercept
     * any errors triggered when the user clicks a CRUD button. This isn't
     * ideal because we have to inspect the HTML (nasty) but in the absence
     * of a proper JSON payload or a better status code, it's our only
     * choice
     */
    function handleCrudAction(data) {
      // if we find any errors, completely re-render our main body
      if (F.containsErrors(data.body)) {
        return F.render(".js-body", data.body);
      }

      // otherwise clear any we might have had previouosly
      F.clearErrors();

      var options = {
        success: OLCS.normaliseResponse({
          followRedirects: false,
          callback: handleCrudResponse
        })
      };

      OLCS.formModal($.extend(data, options));
    }

    /**
     * We manually handle any responses or redirects
     * inside the modal; this means we have to do a bit more
     * heavy lifting than is ideal, but it gives us the flexibility
     * we need
     */
    function handleCrudResponse(response) {
      if (response.status === 200) {
        // always render; could be a clean form (if we clicked add another),
        // could be riddled with errors
        F.render(".modal__content", response.body);

        if (F.containsErrors(response.body)) {
          // if we have errors then there's no need to go any further; there's
          // no chance we need to refresh our parent page
          return;
        }
      }

      // if the original response was a redirect then be sure to respect
      // that by closing the modal
      if (response.status === 302) {
        OLCS.modal.hide();
      }

      var scrollTop = $(window).scrollTop();
      $.get(window.location.href, OLCS.normaliseResponse(function(inner) {
        F.render(".js-body", inner.body);
        $(window).scrollTop(scrollTop);
      }));
    }

    // make sure any backend code sniffing button presses isn't disappointed
    F.pressButton(form, button);

    // hook everything up and submit the form
    OLCS.formAjax({
      form: form,
      success: OLCS.normaliseResponse(handleCrudAction)
    });
  });

  OLCS.eventEmitter.on("render", function() {
    editButton.check();
    deleteButton.check();
    OLCS.formInit();
  });
});
