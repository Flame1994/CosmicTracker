/**
* Describes the orientations for the 0th row and 0th column of image data in relation to their visual positions.
* See http://sylvana.net/jpegcrop/exif_orientation.html for more information.
*/
var ExifOrientation = {
1: 'TOP-LEFT',
2: 'TOP-RIGHT',
3: 'BOTTOM-RIGHT',
4: 'BOTTOM-LEFT',
5: 'LEFT-TOP',
6: 'RIGHT-TOP',
7: 'RIGHT-BOTTOM',
8: 'LEFT-BOTTOM'
};
ExifOrientation.UNROTATED = 1;

var idx = 0;
var files;

onmessage = function(event) {
if (event['data']['next'] === true) {
idx++;
} else {
files = event['data'];
}

if (idx >= files.length) {
close();
}

var file = files[idx];
if (file) {
switch (file.type) {
case 'image/jpeg':
var orientation = getExifRotation(file);
if (orientation !== ExifOrientation.UNROTATED) {
handleRotatedFile(file, orientation);
} else {
handleImageFile(file);
}
break;
case 'image/png':
case 'image/svg+xml':
handleImageFile(file);
break;
case 'image/raw':
case 'image/x-fuji-raf':
handleRawFile(file);
break;
default:
handleError(file);
break;
}
} else {
handleError(null);
}
};

/**
* @param {!File} file
*/
function getExifRotation(file) {
importScripts('https://static.canva.com/static/lib/exif.min.2.js');
try {
var exifData = EXIF.getData(file);
var orientation = exifData['Orientation'];
if (orientation === undefined) {
return ExifOrientation.UNROTATED;
} else {
return orientation;
}
} catch (error) {
// Assume EXIF data is corrupt and treat this image like any other JPG.
return ExifOrientation.UNROTATED;
}
}

function handleRotatedFile(file, orientation) {
var message = {
'fileName': file.name,
'specialFile': 'exifRotated',
'orientation': ExifOrientation[orientation]
};
var fileReaderSync = new FileReaderSync();
try {
message['imageData'] = fileReaderSync.readAsDataURL(file);
} catch (error) {
message['status'] = 'error';
}
postMessage(JSON.stringify(message));
}

/**
* This uses a JS port of a C library for reading raw files
* to convert a raw file to a jpg
*/
function handleRawFile(file) {
var raw = {};
var fileReaderSync = new FileReaderSync();
importScripts('https://static.canva.com/static/lib/dcraw.min.2.js');
raw.fs = FS;
raw.run = run;
raw.fs.createDataFile(
'/',
file.name,
fileReaderSync.readAsBinaryString(file),
true,
true
);
raw.run(['-e', '/' + file.name]);
var fileName = file.name.slice(0, file.name.lastIndexOf('.')) + '.thumb.jpg';
var tdata = raw.fs.root.contents[fileName].contents;
var base64str = '';
for (var i = 0; i < tdata.length; i++) {
base64str += String.fromCharCode(tdata[i]);
}
var message = {
'imageData': "data:image/jpeg;base64," + btoa(base64str),
'specialFile': 'raw',
'fileName': fileName
};
postMessage(JSON.stringify(message));
}

/**
* This reads image data and/or svg text
*/
function handleImageFile(file) {
var message = {
'fileName': file.name
};
var fileReaderSync = new FileReaderSync();
var imageData, svgText;
var isSvg = file.type === 'image/svg+xml';
try {
imageData = fileReaderSync.readAsDataURL(file);
if (isSvg) {
svgText = fileReaderSync.readAsText(file);
}
message['svgText'] = svgText;
message['imageData'] = imageData;
} catch (error) {
message['status'] = 'error';
}
postMessage(JSON.stringify(message));
}

function handleError(file) {
var message = {
'fileName': file ? file.name : '',
'status': 'error'
};
postMessage(JSON.stringify(message));
}
