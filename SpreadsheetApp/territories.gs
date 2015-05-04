/** Indices from territory definitions, so use cols+1 when using getRange */
var def_cols = {
  id: 0,
  name: 1,
  is_land: 2,
  has_supply: 3,
  empire_start: 4,
  starting_forces: 5,
};


/** Indices from territory definitions, so use cols+1 when using getRange */
var adj_cols = {
  id: 2,
  name: 3,
  tbl_start: 4,
};

var TYPES = {
  IS_LAND: 1,
  IS_WATER: 2,
};

function Territory() {
  this.name = '';
  this.id = 0;
  this.type = TYPES['IS_LAND']; // territory type
  this.has_supply = 0;
  this.empire_start = 0;
  this.starting_forces = 0;

  this.neighbours = []; // IDs of neighbouring territories.
}
Territory.prototype.toString = function() {
  return this.id + ':' + this.name;
};
Territory.prototype.addNeighbour = function(id) {
  Logger.log('Adding neighbour %s to %s', id, this.toString());
  this.neighbours.push(id);
}




var TerritoryMap = function() {
  Map.apply(this);

  this.loaded = false;
}
TerritoryMap.prototype = Map.prototype;
TerritoryMap.prototype.constructor = TerritoryMap;

Map.prototype.get = function(val) {
  if (val === '') {
    Logger.log('Error, requested .get with blank key');
    return false;
  }

  if (parseInt(val) > 0) {
    // Dealing with IDs
    for (var i = 0; i < this.length; i++) {
      if (this.mem[this.keys[i]].id == val) {
        return this.mem[this.keys[i]];
      }
    }
    return false;
  } else {
    // Serialize to a key
    var key = this.map(val);
    if (typeof this.mem[key] !== 'undefined') {
      this.mem[key].key = key;
      return this.mem[key];
    } else {
      return false;
    }
  }
}

TerritoryMap.prototype.map = function(val) {
  if (val === '' || val === null) {
    Logger.log('Received invalid value to hash.');
    return false;
  }
  var hash = val.name.replace(/^[\s\W]+/g, '');
  hash = hash.replace(/[\s\W]+$/g, '');
  hash = hash.replace(/[^a-zA-Z0-9]+/g, '_');
  hash = hash.toLowerCase();
  return hash;
};
TerritoryMap.prototype.load = function(list) {
  if (this.loaded) return;
  var sheet = SpreadsheetApp.getActiveSpreadsheet().getSheetByName(sheets[list].territory_defs);
  var range = sheet.getDataRange();
  var numRows = range.getNumRows();

  range = sheet.getRange(2, def_cols['id'] + 1, numRows - 1, 2); // assume id and name are adjacent
  var values = range.getValues();
  for (var i = 0; i < values.length; i++) {
    var t = new Territory;
    for (var j = 0; j < values[i].length; j++) {
      switch (j) {
        case 0:
          t.id = values[i][j];
          break;
        case 1:
          t.name = values[i][j];
          break;
        case 2:
          t.type = (values[i][j] ? TYPES['IS_LAND'] : TYPES['IS_WATER']);
          break;
        case 3:
          t.has_supply = values[i][j];
          break;
        case 4:
          t.empire_start = values[i][j];
          break;
        case 5:
          t.starting_forces = values[i][j];
      }
    }
    this.insert(t);
  }
  this.loaded = true;
};

// vim: sw=2 ts=2 expandtab :
