function validateSymmetry() {
  var sheet = SpreadsheetApp.getActiveSpreadsheet().getActiveSheet();
  if (sheet.getName() != sheets.real.adjacencies && sheet.getName() != sheets.test.adjacencies) {
    var ui = SpreadsheetApp.getUi(); // Same variations.

    ui.alert('Error!  Can only be run on a adjacency list!');
    return;
  }
  var range = sheet.getDataRange();
  var numRows = range.getNumRows();
  var numCols = range.getNumColumns();
  range = sheet.getRange(2, adj_cols['tbl_start'] + 1, numRows - 1, numCols - (adj_cols['tbl_start'] + 1));
  var values = range.getValues();

  for (var i = 0; i < values.length; i++) {
    for (var j = 0; j < values[i].length; j++) {

      if (i == j) continue;

      if (values[i][j] != values[j][i]) {
        var cell = null;
        var val_ij = parseInt(values[i][j]);
        var val_ji = parseInt(values[j][i]);

        if (isNaN(val_ij)) val_ij = 0;
        if (isNaN(val_ji)) val_ji = 0;

        Logger.log('(%s,%s)=%s, (%s,%s)=%s', i, j, val_ij, j, i, val_ji);
        if (val_ij >= 1 && val_ji < 1) {
          cell = sheet.getRange(j + 2, adj_cols['tbl_start'] + 1 + i);
        } else if (val_ji >= 1 && val_ij < 1) {
          cell = sheet.getRange(i + 2, adj_cols['tbl_start'] + 1 + j);
        }
        if (cell !== null) {
          Logger.log('setting value to red');
          cell.setBackground('#FF0000');
          cell.setValue('ERROR');
        }
      }

    }
  }
}

// vim: sw=2 ts=2 expandtab :
