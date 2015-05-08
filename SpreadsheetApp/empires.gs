/** Indices from empire definitions, so use cols+1 when using getRange */
var def_cols = {
  id: 0, // string
  name_official: 1,
  name_full: 2,
  name_short: 3,
  comments: 4,
};

function Empire() {
  this.id = '';
  name_official = '';
  name_full     = '';
  name_short    = '';
  comments      = '';
}
Empire.prototype.toString = function() {
  return this.id;
};


var EmpireMap = function() {
  Map.apply(this);

  this.collection = "Players";
  this.loaded = false;
}
EmpireMap.prototype = new Map();
EmpireMap.prototype.constructor = EmpireMap;

//EmpireMap.prototype.map = Map.prototype.map; // shouldn't have to do this.

EmpireMap.prototype.load = function(list) {
  if (this.loaded) return;
  var sheet = SpreadsheetApp.getActiveSpreadsheet().getSheetByName(sheets[list].empire_defs);
  var range = sheet.getDataRange();
  var numRows = range.getNumRows();

  range = sheet.getRange(2, def_cols['id'] + 1, numRows - 1, 2); // assume id and name are adjacent
  var values = range.getValues();
  for (var i = 0; i < values.length; i++) {
    var t = new Empire;
    for (var j = 0; j < values[i].length; j++) {
      switch (j) {
        case 0:
          t.id = values[i][j];
          break;
        case 1:
          t.name_official = values[i][j];
          break;
        case 2:
          t.name_long = values[i][j];
          break;
        case 3:
          t.name_short = values[i][j];
          break;
        case 4:
          t.comments = values[i][j];
          break;
      }
    }
    this.insert(t);
  }
  this.loaded = true;
};


function loadEmpires(list) {
  if (typeof list === 'undefined')
    list = 'test';
  if (typeof territories === 'undefined')
    empires = new EmpireMap();
  empires.load(list);

  return empires.exportArray();
};

// vim: sw=2 ts=2 expandtab :
