OLCS.ready(function() {
  OLCS.tableHandler({
    table: ".table__form"
  });

  OLCS.tableSorter({
    table: ".table__form",
    // where we'll render any response data to
    container: ".table__form",
    // filter the data returned from the server to only
    // contain content within this element
    filter: ".table__form"
  });
});
