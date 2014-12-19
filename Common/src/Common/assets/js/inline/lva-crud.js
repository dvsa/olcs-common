OLCS.ready(function() {
  "use strict";
  // @TODO: there is a later story to modalise LVA behaviour
  // internally. For now, the easiest way to suppress it
  // is to short circuit based on the app class. Not pretty,
  // but will be removed early 2015
  if (document.body.className.search("selfserve") === -1) {
    return;
  }
  OLCS.crudTableHandler();
});
