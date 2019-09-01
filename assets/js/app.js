var xhr = new XMLHttpRequest();
var nexusData;

xhr.onreadystatechange = function() {
  if (this.readyState == 4 && this.status == 200) {
    var response = JSON.parse(xhr.responseText);
    nexusData = JSON.parse(response.data);
  }
};

xhr.open("GET", "/Nexus/Nexus/GetContent", false);
xhr.setRequestHeader("Cache-Control", "public, max-age=600");
xhr.send();

function formatFileSize(fileSize, decimalPoint) {
    if(fileSize == 0) return '0 Bytes';
    var k = 1024,
        dm = decimalPoint || 2,
        sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
        i = Math.floor(Math.log(fileSize) / Math.log(k));
    return parseFloat((fileSize / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
};

function formatUnixtime(unixtime) {
    return new Date(unixtime * 1000).toLocaleString("de-DE", {
        hour: "2-digit",
        minute: "2-digit",
        day: "2-digit",
        month: "2-digit",
        year: "numeric"
    });
};

function formatNexusData(data) {
    var entryCounter = data.length;

    for(var i = 0; i < entryCounter; i++) {
        data[i]["size"] = formatFileSize(data[i]["size"]);
        data[i]["atime"] = formatUnixtime(data[i]["atime"]);
        data[i]["mtime"] = formatUnixtime(data[i]["mtime"]);
    }
};

formatNexusData(nexusData);

var table = document.getElementById("nexus");

for (var i = 0; i < nexusData.length; i++) {
  var currentRow = table.insertRow(i + 1);
  var cellKeys = Object.keys(nexusData[i]);

  for (var y = 0; y < cellKeys.length; y++) {
    var cellData = nexusData[i][cellKeys[y]];
    var cell = currentRow.insertCell(y);

    cell.innerText = cellData;
  }
}