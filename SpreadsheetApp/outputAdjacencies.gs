// Sheet we're using
var sheets = {
  real: {
    adjacencies: 'Adjacencies',
    territory_defs: 'Province names and IDs',
  },
  test: {
    adjacencies: 'TEST.Adjacencies',
    territory_defs: 'TEST.Province names and IDs',
  },
};

function onOpen() {
  var spreadsheet = SpreadsheetApp.getActiveSpreadsheet();
  var entries = [{
    name: "Export Territories",
    functionName: "exportTerritories"
  }, {
    name: "Validate Symmetry",
    functionName: "validateSymmetry"
  }];
  spreadsheet.addMenu("Diplmacy", entries);
};


/**
 * ////////////////////////////////////////
 * // Hash map
 * ////////////////////////////////////////
 */
function Map() {
  this.mem = {};
  this.keys = []; // no point in having length with keys (duplicate).. afterthough...
  this.length = 0;
}
Map.prototype.insert = function(val) {
  var key = this.map(val);
  if (typeof this.mem[key] === 'undefined') {
    //Logger.log("[%s] Inserting: key=%s, val=%s", this.length, key, val.toString());

    this.mem[key] = val;
    this.keys.push(key);
    this.length++;
  }
};
Map.prototype.map = function(val) {
  var hash = val.toString();
  hash = hash.replace(/\W/g, '');
  hash = 'k' + hash;
  return hash;
};
Map.prototype.clear = function() {
  this.mem = {};
  this.keys = [];
  this.length = 0;
};

Map.prototype.get = function(val) {
  // Serialize to a key
  var key = this.map(val);
  if (typeof this.mem[key] !== 'undefined') {
    this.mem[key].key = key;
    return this.mem[key];
  } else {
    return false;
  }
}

Map.prototype.exportArray = function(val) {
  var output = [];
  for (var i = 0; i < this.length; i++) {
    var key = this.keys[i];
    output.push(this.mem[key]);
  }
  return output;
}



/**
 * ////////////////////////////////////////
 * // Export Functions
 * ////////////////////////////////////////
 */
function exportTerritories() {
  var html = HtmlService.createHtmlOutputFromFile('export_territories')
    .setSandboxMode(HtmlService.SandboxMode.NATIVE);
  SpreadsheetApp.getUi() // Or DocumentApp or FormApp.
    .showModalDialog(html, 'Export Territories');
}

var territories;

function loadTerritories(list) {
  if (typeof list === 'undefined')
    list = 'test';
  if (typeof territories === 'undefined')
    territories = new TerritoryMap();
  territories.load(list);
  Logger.log('Territory definitions loaded.');

  // This will add adjancency/neighbour information on to each object
  loadAdjacencies(list);

  return territories.exportArray();
};

function loadAdjacencies(list) {

  var sheet = SpreadsheetApp.getActiveSpreadsheet().getSheetByName(sheets[list].adjacencies);
  var range = sheet.getDataRange();
  var numRows = range.getNumRows();
  var numCols = range.getNumColumns();
  range = sheet.getRange(2, adj_cols['id'] + 1, numRows - 1, numCols - (adj_cols['name'] + 1)); // assume id and name are adjacent
  var values = range.getValues();

  var t;
  var offset = adj_cols['tbl_start'] - adj_cols['id']; // This is the offset from the range to the start of the actual table we're examining
  for (var i = 0; i < values.length; i++) {
    t = null;

    for (var k = 0; k < values[i].length; k++) { // the magic 2 is where the data starts

      if (k == 0) {
        t_id = values[i][k];
        if (t_id === '') break;

        Logger.log('Fetching territory for t_id=%s', t_id);
        t = territories.get(t_id); // fetch by ID
        if (t === null) {
          Logger.log('Error Fetching territory for t_id=%s', t_id);
          break;
        }
        continue;
      } else if (k < offset)
        continue;

      var j = k - offset; // Offset to the beginning of the table.  k is the matrix index (starting at 0),
      // Neighbour we're examing should have the ID (j+1)
      var n_id = j + 1;
      //Logger.log("t_id=%s", t_id);


      // On the diagonal?
      if (i == j) {

        // TMP
        cell = sheet.getRange(i + 2, k + (adj_cols['id'] + 1));
        cell.setFontColor('#00FF00');
        cell.setValue(0);

        break;
      }

      if (parseInt(values[i][k]) == 1) {
        t.addNeighbour(n_id); // relies on the ID's being sequential.  This assumption is used many times here
      }
    }
  }

}

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
