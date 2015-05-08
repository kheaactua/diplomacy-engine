// Sheet we're using
var sheets = {
  real: {
    adjacencies: 'Adjacencies',
    territory_defs: 'Province names and IDs',
    empire_defs: 'Empires',
  },
  test: {
    adjacencies: 'TEST.Adjacencies',
    territory_defs: 'TEST.Province names and IDs',
    empire_defs: 'TEST.Empires',
  },
};

function onOpen() {
  var spreadsheet = SpreadsheetApp.getActiveSpreadsheet();
  var entries = [{
    name: "Export Data",
    functionName: "exportData"
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
var territories;
var empires;

function exportData() {
  var html = HtmlService.createHtmlOutputFromFile('export_data')
    .setSandboxMode(HtmlService.SandboxMode.NATIVE);
  SpreadsheetApp.getUi() // Or DocumentApp or FormApp.
    .showModalDialog(html, 'Export Territories');
}


function loadData(list, dataset) {
  if (typeof list === 'undefined')
    list = 'test';
  if (typeof dataset === 'undefined')
    dataset = 'territories';

  if (dataset === "territories") {
    Logger.log('Loading %s', dataset);
    return loadTerritories(list);
  } else if (dataset === "empires") {
    Logger.log('Loading %s', dataset);
    return loadEmpires(list);
  } else {
    Logger.log('Invalid dataset: %s', dataset);
  }
};

// vim: sw=2 ts=2 expandtab :
