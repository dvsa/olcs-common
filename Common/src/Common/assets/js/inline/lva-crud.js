/**
 * @NOTE This could potentially be DRYed up, as this is going to be fairly common behaviour throughout LVA
 */
OLCS.ready(function() {
  "use strict";

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

  $(document).on("click", ".table__header button", function(e) {
    e.preventDefault();

    var button    = $(this);
    var form      = $(this).parents("form");
    var container = $(this).parents("fieldset");
    var group     = container.attr("data-group");

    /**
     * We manually handle rendering the modal because we need to intercept
     * any errors triggered when the user clicks a CRUD button. This isn't
     * ideal because we have to inspect the HTML (yuck) but in the absence
     * of a proper JSON payload or a better status code, it's our only
     * choice
     */
    function renderModal(data) {
      // if we find any errors, completely re-render our main body
      if ($("<div>").html(data.body).find(".validation-summary").length) {
        return $(".js-body").html(data.body);
      }

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
      if (response.status !== 302) {
        return $(".modal__content").html(response.body);
      }

      $.get(response.location, OLCS.normaliseResponse(function(response) {
        $(container).html(
          $(response.body).find("fieldset[data-group=" + group + "]").html()
        );

        OLCS.modal.hide();
      }));
    }

    // make sure any backend code sniffing button presses isn't disappointed
    F.pressButton(form, button);

    // hook everything up and submit the form
    OLCS.formAjax({
      form: form,
      success: OLCS.normaliseResponse(renderModal)
    });
  });

  OLCS.eventEmitter.on("hide:modal", function() {
    editButton.check();
    deleteButton.check();
  });
});
