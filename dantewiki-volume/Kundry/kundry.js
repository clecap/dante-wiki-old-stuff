/** BEGIN KUNDRY **/



// hidden web print as in   https://developer.mozilla.org/en-US/docs/Web/Guide/Printing
// // TODO ?????????????????????????????????????????? must try to go from frame to non-frame - particularly for those cases where we are not allowed to frame a page !!
function closePrint () {document.body.removeChild(this.__container__);}

function setPrint () {
  console.log ("setPrint");
 //  this.contentWindow.__container__ = this;
  console.log ("set 1");
 // this.contentWindow.onbeforeunload = closePrint;
  console.log ("set 2");
 // this.contentWindow.onafterprint = closePrint;
  console.log ("set 3");
 // this.contentWindow.focus(); // Required for IE
  console.log ("set 4");
  this.contentWindow.print();
  console.log ("set 5");  
  console.log ("completed print");  
}

function printPage (sURL) {
  console.log ("printpage");
  var oHideFrame = document.createElement("iframe");
  oHideFrame.onload = setPrint;
  Object.assign (oHideFrame.style, {position: "fixed", left:"0px", top:"0px", width:"800px", height:"800px", border:"4px solid black" });
  oHideFrame.src = sURL;
  document.body.appendChild(oHideFrame);
}



function dealWithUri (str) {
  /*
  if (str.endsWith (".pdf")) {
    fetch ()
  
  }  
  
  */
  
}



(()=>{ // BEGIN SCOPE PROTECTION



// A universal drag-and-drop handler
//   <dropZone> is an area inside of the page on which we want to drop some stuff
//   Files are given, one by one, to a fileHandler (arrayBuffer, name, mimetype)
//   Items are givem all at once, to an itemsHandler ()
// if <protect> is true then we want to protect the remaining page area from any drops
const installDropZone = ( dropZone, fileHandler, itemsHandler, protect) => {
  const VERBOSE  = false;    
  const VVERBOSE = false;                 // UI detail verbosity: Very Verbose
  
  // we want to protect the non-dropzone area from an unwanted drop
  if (protect) {
    const NO_DND = (e) => {  e.preventDefault();   e.dataTransfer.dropEffect = 'none'; } ;
    document.body.addEventListener ('dragenter', NO_DND); document.body.addEventListener ('dragover',  NO_DND);  document.body.addEventListener ('drop',      NO_DND);
  }
  
  var count = 0;  // when the dropZone element has children (eg dropping on body) then we get leave and enter events for the children as well; count checks if we are leaving the dropZone or just moving to a child 
  dropZone.addEventListener ('dragenter',  (e) => {   if (VVERBOSE) {console.log ("dragenter", e.target);} 
    count++;  
    e.dataTransfer.dropEffect = 'copy';  dropZone.classList.add ("dropzone-active");   
  });
  
  dropZone.addEventListener ('dragleave',  (e) => {   if (VVERBOSE) {console.log ("dragleave", e.target);} 
    count--;
    if (count==0) {e.dataTransfer.dropEffect = 'none'; dropZone.classList.remove ("dropzone-active");}   
  });
    
  dropZone.addEventListener ('dragover',   (e) => {   if (VVERBOSE) {console.log ("dragover",  e.target);}   
    dropZone.classList.add ("dropzone-active");
    e.stopPropagation(); e.preventDefault();    // must prevent defaults on dragover (or the chrome extension kicks in)                  // TODO: maybe superfluous ?????????????????????
    e.dataTransfer.dropEffect = 'copy';         // Show the copy icon when dragging over.  Seems to only work for chrome.
  });

  dropZone.addEventListener('drop', function(e) { if (VVERBOSE) { console.log ("drop", e.target); }
    dropZone.classList.remove ("dropzone-active");          // remove marking again !
   /* e.stopPropagation(); */ e.preventDefault();               // do not do the defaults of a drop but do our stuff
    
    console.log (`kundry.js dropzone received ${e.dataTransfer.items.length} ITEMS, ${e.dataTransfer.types.length} TYPES, ${e.dataTransfer.files.length} FILES`);
    
    if (e.dataTransfer.items.length > 1) {if (itemsHandler) {itemsHandler (e.dataTransfer.items);}}

    var files = e.dataTransfer.files;
    for (var i=0, file; file=files[i]; i++) {
      if (VERBOSE) {console.log (`  file ${i} is ${file.name} and ${file.kind}`, file);}
      var name   = file.name; var type = file.type;                                                 // CAVE: file no longer available in onload handler due to asynchonicity, so copy value here
      var reader = new FileReader();
      reader.onload = function(e2) { if (fileHandler) { fileHandler (e2.target.result, name, type);} }      // if we have a handler, present the result to the handler as an ArrayBuffer
      reader.readAsArrayBuffer(file);                                                               // note: this is MUCH faster than readAsDataURL
    } // for
  });
};



// DUMMY itemsHandler   TODO: must be expanded to properly handle reasonable drops from several places !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// 
// 
async function itemsHandler (items) {
  console.log ("kundry:itemsHandler received items: " , items.length, items);
  for (let i = 0; i < items.length; i++) {
    let item = items[i], type = item.type, kind = item.kind;                            // MUST keep those via let because otherwise they will get lost in a momend where the DataTransfer item is no longer available
    //console.log ("kundry:itemsHandler:  item " + i + " is of kind=" + item.kind + " and type="+item.type, item);
    item.getAsString ( (str) => { 
      console.log ("kundry:itemsHandler:  item " + i + " is kind=" + kind + " type=" + type + " string=" + str);
      if (type == "text/uri-list") {dealWithUri (str);} else {console.log ("skipped type=" + type);}
      
    } );
  }
}


// handler for dropped files; expects to receive an array buffer <buf> with the file content
async function dropHandler (buf, name, mimetype) {
  var hash = await HASH (buf);                  // determine hash of dropped content
  console.log ("kundry.js: dropHandler: got buf: ", buf, " and hash ", hash);
  var meta = await promise2ReadMetaData(hash);         // check Mediawiki for any existing meta data
  meta.filename     = name;
  meta.mimetype     = mimetype;
  meta.suppliedHash = hash;
  console.log ("kundry.js: dropHandler: existing metadata found: ", meta);
  openMediumEmbedded (buf, {}, meta);                  // open the browser to display the medium
}




///////////////////////////////////////////// ?????????????????????????????????? TODO: adjust this to the correct location
const DOCU_WINDOW_URL = document.location.protocol + "//" + document.location.host + "/myExtensions/Kundry/embedPresent.html";    /// TODO: PATH into config ////////////////////////////////  
////////////////////////////////////////////   The query portion below is used to force a reload and non-caching while we are developing !!!!!!!!!!!!!!!!!!!!!!!!!


var DATA;
var SLAVE;   // reference to a SLAVE window

const pingForSlaveWindow = () => {
  SLAVE = window.open ("", "fresh");  
  if (SLAVE) {console.warn ("found slave window"); SLAVE.postMessage ("REFRESH_CONNECTION", "*"); }  //////////////////////// CHECK AUTHENTICIY - DEBUG THE ENTIRE CONCEPT of refinding a window - ENSURE we are in the SAME Dantepedia and not a fifferent one !!!!!!!!
};

// MESSAGE HANDLER for all messages we might receive from somewhere
window.onmessage = async (e) => {  // listen to messages
  const VERBOSE = true;
  console.log ("Message received (from slave?) has e.data=", e.data);
  console.log ("origin of message is: ", e.origin);
 
 
//  if (e.data === "PARENT_REFRESHED") { setTimeout(() => {event.source.postMessage("REFRESH_CONNECTION", "*"); }, 500); }  ///// TODO: origin  checken !!! ///////////////////// DO WE STILL NEED THIS ?????
  ///// TODO: is it really for us and from our correct source window ??
  
  var hash;
  if (e.data.flag) {          // flag is set, so it is a message from our slave
    SLAVE = e.source;         // MUST NOT forget to pick up the SLAVE now (or we might lose it due to the relaod we do in the master in the mean time)
    if (e.data.hashKey) {hash = e.data.hashKey; if (VERBOSE) {console.log ("got hash from slave: ", hash);} } 
    else {if (VERBOSE) {console.log ("got no hash from slave, calculating one myself from ", e.data.buf);} hash = await HASH (e.data.buf); if (VERBOSE) {console.log ("  ...and computed it to ", hash);}} 
    
    switch (e.data.flag) {
      case 1: uploadToWiki  (e.data.buf);                                       break;  
      case 2: writeMetaData (e.data.meta, hash);                                break;
      case 3: uploadToWiki  (e.data.buf); writeMetaData (e.data.meta, hash);    break;
    }
  }
  
  if (e.data.extension) {  // if extension is set, then it is a message from the content script controlled by our extension
    console.log ("got from extension", e.data);
    
    
  }
  
  
  
};



// open a window with a browser suitable for displaying the medium contained in specification <spec>
// <spec> can be (1) a string URL leading via the web server to the content or (2) an array buffer containing the content
// called by (1) drag-drop handler or (2) openKundryWindow
function openMediumEmbedded (spec, opt, meta) {
  console.log ("kundry.js: openMediumEmbedded called with content spec=", spec, "opt=", opt, "meta=", meta);
  // was the media window ever positioned or resized - then pick up the respective values
  var {width, height, left, top} = getSize ("drop");     // pick up persisted size and position of "drop" window (we have different stuff in Parsifal for the Categories and here as well displayed media in text via click links)
  SLAVE = window.open (DOCU_WINDOW_URL + "?" + Math.random(), "fresh", "width="+width+",height="+height);   ////////////////////////   what about removing random ??????????????? TODO
  if ( !SLAVE ) { alert ("If you want to use this feature, please enable popups for this site.");return;}
  SLAVE.moveTo (left, top);    
  opt = getOptionString ( opt );                                                      // adjust the provided opt parameters to a form acceptable to the embedded viewer
  SLAVE.onload = (e) => { 
    console.log ("kundry.js: openMediumEmbedded: SLAVE.onload executing");
    SLAVE.postMessage ({spec, opt, meta}, DOCU_WINDOW_URL);};    // as soon as that window is open, transfer the document to that window
}

// open a window with ONLY the pdf file, convey the parameter object to the embedded plugin
async function openKundryWindow (titleName, opt, meta ) { 
   console.log ("openKundryWindow");
  var url = "/images/" + titleName.substring (5);                             // remove the "File:" prefix
  var response = await fetch (url); var buf = await response.arrayBuffer();   // we can also feed in buf = url but then installed chrome extensions kick in and interfere with out goal
  // console.log ("did fetch and got ", buf);
  openMediumEmbedded ( buf, opt, meta);
   console.error ("qwe4");
  /////////////////// TODO: what happens when we already have a downloader slave open and then open another slave for editing meta data here ??????????????????????????????  or we have one window open and then do a drag and DROP ???
};


function TEST1 () {
  var xhr = new XMLHttpRequest();
  var url = "/api.php";
  var title = "One_Page";
  var params = {action: "purge", titles: title, format: "json"};
  xhr.open ('POST', url+"?action=purge&titles=One_Page&format=json", true);      
    xhr.setRequestHeader("Content-Type", "form-data");
   xhr.setRequestHeader("Content-Length", "0"); 
                                           
  xhr.send( null );
  xhr.onload = (e) => {
    console.error("----------------------------------------TEST1");
    console.error ("Parsifal:helper.js:imageIsMissing: The purge request returned ", e.target.response);
    console.error ("Parsifal:helper.js:imageIsMissing: Will now reload ", e.target.response);
  }
}


function TEST2 () {
  var xhr = new XMLHttpRequest();
  xhr.open('POST', "/api.php", true); /////////////////// PATH !!
  var formData = new FormData();
  var title = "One_Page";
  formData.append("action", "purge");
  formData.append("titles", title);
  formData.append("format", "json");
  xhr.setRequestHeader("Content-Disposition", "form-data");
  xhr.send(formData);  
  xhr.onload = (e) => {
       console.error ("-----------------------------------------TEST2");
    console.error ("Parsifal:helper.js:imageIsMissing: The purge request returned ", e.target.response);
    console.error ("Parsifal:helper.js:imageIsMissing: Will now reload ", e.target.response);
   // window.location.reload();
  } 
  
}








//////////////// TODO: ??? not clear: do we have to know the mime type for this to work ???

function promise2ReadMetaData (hashKey, ext = "pdf") {
  const VERBOSE = true;
  if (VERBOSE) {console.log ("kundry: promise2ReadMetaData: will now promise to check if there is already metadata for hash " + hashKey);}
  return new Promise ( (resolve, reject) => {
    var params = {  action: "query",  prop:"revisions", titles: "File:"+hashKey+"."+ext, rvprop:"content", rvslots:"*"};  
    var api = new mw.Api();
    api.get(params)
    .done ( data => { 
      if (VERBOSE) {console.log   ("kundry: promise2ReadMetaData api call replied with: " , data);}
      var meta = data.query.pages;
      var keys = Object.keys (meta);
      var key  = keys[0];
      if (key == -1) { if (VERBOSE) {console.log ("kundry: looks like page is missing, resolving to falsish");} resolve (false); return; }
      var revs = meta[key].revisions;
      var rev = revs[0];
      var content = rev.slots.main["*"]; 
      if (VERBOSE) {console.log   ("kundry: promise2ReadMetaData content found is: " , content);}
      var regex = /<json>((.|\s)*)<\/json>/mg;
      console.log ("***********************************************");
      var resu = regex.exec (content);
      console.log ("------------------------------- promise2ReadMetaData: regular expression gave: ", resu[0]);
      console.log ("------------------------------- promise2ReadMetaData: regular expression gave: ", resu[1]);
      console.log ("------------------------------- promise2ReadMetaData: regular expression gave: ", resu[2]);          
      
      var obj = JSON.parse (resu[1]);
        
      resolve (obj); })
    .fail ( data => { console.error ("kundry: promise2ReadMetaData: API call failed: ", data); reject  (data); });
  });    }




// writes the data in object <obj> into the File:<hashKey>.pdf page of dantewiki
function writeMetaData (obj, hashKey, ext="pdf") {
  obj.sha = hashKey;                                                                                // inject sha into object which will be written as metadata to dantewiki
  var text = `==Metadata Store==\n<json>\n${JSON.stringify(obj)}\n</json>\n`;                       // heading and 
  var params = { action: 'edit', title: 'File:'+hashKey+"."+ext, text: text, format: 'json'};
  var api = new mw.Api();
  api.postWithToken( 'csrf', params )
    .done(  ( data ) => {
      console.log ("writeMetaData: API returned when writing metadata: ", data, JSON.stringify (data));
      if ( mw.config.get("wgNamespaceNumber") == 6 ) { window.location.reload(); }         // if we are on a page in File namespace, we might consider reloading since the page might have changed
      promise2ReadMetaData(hashKey).then ( meta => {
        console.log ("writeMetaData sending to SLAVE: ", meta);
        console.log ("SLAVE IS:" , SLAVE);
        SLAVE.postMessage ({meta}, "*");
        console.log ("------------------------------------ psot send");
      });         // now read metadata again from dantewiki and push them back to the slave to have a check for their correctness and correct colors in the mask //////// TODO "*" verbessern
    })
    .fail ( data => {alert ("Writing metadata failed: " + error);});
}





// pick up data from the options object and return it in a form acceptable for the chrome embedded pdf viewer
// for details see https://stackoverflow.com/questions/7126089/how-to-specify-parameters-to-google-chrome-adobe-pdf-viewer
const getOptionString = (opt) => {
  opt = opt || {};
  var param1 = "";  if (opt.zoom) {param1 = `zoom=${opt.zoom}`;} else if (opt.view) {param1 = `view=${opt.view}`} else {param1 = `view=FitH`;}   // size information
  var param2 = "";  if (opt.nameddest) {param2 = `nameddest=${opt.nameddest}`} else if (typeof opt.page != "undefined") {param2 = `page=${opt.page}`;}  // page informations
  var param3 = "";  if (typeof opt.toolbar != "undefined") {param3 += `toolbar=${toolbar}`;}
  var param = "#"  + param1 + "&" + param2 + "&" + param3;  
  return param;
};



/////////////////////// WHAT IS THIS NEEDED FOR ????
function hoverKundry (url) {
  var ifra = document.createElement ("iframe");
  ifra.setAttribute ("src", url);
  ifra.setAttribute ("style", "position:absolute; top:30px; left:30px;");
  document.body.appendChild (ifra);
  
};

// translate mime-type into the extension for the file which we will store
const MIME2EXT = {
  "application/pdf": "pdf",
  "image/png": "png",
  "image/gif": "gif",
  "image/jpeg": "jpg",
  "image/svg+xml": "svg"
};

// assume arr is an array consisting of objects of the kind {name, value}, return the values for <name> as an array of values found (there might be several)
function pickMetadata (arr, name) { arr.filter ( ele => (ele.name==name) ).map( ele => ele.value); }


// given an arbitrary string return a close string which may be used as mediawiki title for a file - also the name must not be too long
function sanitizeTitle (name) { return name.replace (/[^a-zA-Z0-9\_\-\ ]/g, " ").substring(0,220); }


async function importWikitextToWiki () {}


async function decompressBlob(blob) {
  const ds = new DecompressionStream('gzip');
  const decompressedStream = blob.stream().pipeThrough(ds);
  return await new Response(decompressedStream).blob();
}



// assume <buf> contains a file in mediawiki export/import xml, then importToWIki imports this 
async function importToWiki (buf, onResolve, onReject) { 
  var api = new mw.Api();
  var fileInput = $( '<input/>' ).attr( 'type', 'file' ).value = new Blob ([buf], {type: "application/xml"});
  var param = {action:'import', xml:fileInput, format: 'json', interwikiprefix: "en", errorformat:"plaintext"};  
  var ajax = {contentType: "multipart/form-data"};
  api.postWithToken ( 'csrf', param , ajax)
    .done ( data =>  {
       console.log ("importToWiki: done: ", data);
       if (onResolve) {onResolve (data);}
    })
    .fail ( data => {
      
      console.log (" fail: ", data);
      
    });
}


async function pushPage () {}

async function pullPage () {}



function saveStringAsFile (stringData, fileName, mimeType) {
  var a = document.createElement("a");  a.style = "display: none";         // generate a link for download only
  document.body.appendChild(a);
  var blob = new Blob([stringData], {type: mimeType});
  var url = window.URL.createObjectURL(blob);
  a.href = url;
  a.download = fileName;
  a.click();
  window.URL.revokeObjectURL(url);
  // a.parentNode.removeChild (a);
}




// References see  https://github.com/nikolas/mediawiki-export/blob/master/wiki-export.  and  https://www.mediawiki.org/wiki/API:Query
// 
window.exportPage = async function exportPage (title, format="xml") {  // export for use in javascript: links
  var api = new mw.Api();
  // not clear: parameter continue  and  rawcontinue
  if (title===undefined) {title = mw.config.get ("wgPageName");}  // if no title given, get name of current page including namespace prefix
  var fileName = title + "." + format;

 var param = {action:"query", format:"json", prop:"revisions",   list: "", titles: title};  

  switch (format) {
    case "xml": Object.assign (param, {export:true, exportnowwrap:false, exportschema:"0.11"}); type = "application/xml";  break;
    case "wki": Object.assign (param, {prop:"revisions", rvprop:"content", rvslots:"*"});       type = "text/x-wiki";        break;     
    default: 
  }

  api.get (param)  
    .done ( data => {
      var txt;
      console.log ("exportPage: ", data);
      switch (format) {  // now decode the response and extract the text we are interested in
        case "xml":  txt = data.query.export["*"]; break;
        case "wki":
          var resu = data.query.pages;
          var keys = Object.keys (resu);
          var key  = keys[0];
          if (key == -1) { if (VERBOSE) {console.log ("kundry: looks like page is missing, resolving to falsish");} resolve (false); return; }
          var revs = resu[key].revisions;
          var rev = revs[0];
          txt = rev.slots.main["*"]; 
          break;
      }
      saveStringAsFile (txt, fileName, type);      
    })
    .fail ( data => {
       console.error ("exportPage: ", data);
    });
};




// when as Blob  TODO !!!!!
window.exportPagesBlob = async function exportPagesBlob () {
  var response = await fetch ( "/index.php?title=Special:Export&exportall&action=submit", {method: 'POST', body:" "} );
  var txt = await response.text();
  console.log ("export pages produced: ", txt);
  
};

////////////////// TODO: STILL IMPROVAE THIS by allowing to have some more criteria for selection !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
window.exportPages = async function exportPages () {
  var response = await fetch ( "/index.php?title=Special:Export&exportall&action=submit", {method: 'POST', body:" "} );
  var readableStream = response.body;
  
  var suggestedName = "page-export-NAME.xml"; // TODO ??????????????????????????????? improve
  const newHandle = await window.showSaveFilePicker( {suggestedName} );
  const writeableStream = await newHandle.createWritable();                   // create a FileSystemWritableFileStream to write to
  await readableStream.pipeTo (writeableStream);
  
};




async function quickDelete () {}










// upload the contents of buffer <buf> under mime-type <mime> (e.g. application/pdf) using filename <filename> to the wiki
// if <filename> is missing, use HASH of the file content
// due to the non-standard implementation not using promises we here have onResolve and onReject handlers
async function uploadToWiki (buf, mime, filename, onResolve, onReject) {
  if (!mime)     { mime = "application/pdf";}                 
                                   
  if (!filename) { filename = await HASH (buf); } else {
    filename = removeExtension (filename);
    var sanitizedName = sanitizeTitle (filename);
    if (sanitizedName != filename) {
      var ok = window.confirm (`The following filename is rejected by Mediawiki:\n\n ${filename}\n\n Will use replacement:\n\n ${sanitizedName}`);
      if (ok) {filename = sanitizedName}  else { alert ("Upload was aborted by user."); return;}
    }
  }
  filename += "." + MIME2EXT[mime];
  var comment = "Uploaded to dantewiki via Kundry extension";
  console.log ("uploadToWiki: will now upload " + filename);
  var param = {filename, format: 'json', comment, errorformat:"plaintext", ignorewarnings: true};         // ignorewarnings needed to avoid error on a reupload attempt
  var	fileInput = $( '<input/>' ).attr( 'type', 'file' ).value = new Blob ([buf], {type: mime});
  var api = new mw.Api();
  var stuff = api.upload( fileInput, param )
    .done( data => {
      console.log ("DONE");
      console.log ("API upload returned, as object:   ", data, JSON.stringify (data));
      
      var usedFilename = ( data.upload && data.upload.filename ? data.upload.filename: "");
      
      /*
      var title  =  (data.upload && data.upload.imageinfo && data.upload.imageinfo.metadata ? pickMetadata  (data.upload.imageinfo.metadata,  "Title")  : "");  ////7  <TODO: where and how do we use this. also CAVE: this is an array !!  see pcikMetadata
      var author =  (data.upload && data.upload.imageinfo && data.upload.imageinfo.metadata ? pickMetadata  (data.upload.imageinfo.metadata,  "Author") : "");
      var text   =  (data.upload && data.upload.imageinfo && data.upload.imageinfo.metadata ? pickMetadata  (data.upload.imageinfo.metadata,  "text")   : []);  // full text conversion of the entire document 
      var pageCount = data.upload.imageinfo.pagecount;
      var timestamp = data.upload.imageinfo.timestamp;
      console.log ("API upload returned, stringified: ", JSON.stringify (data));
      mw.notify( 'The upload was successfull' );
      console.log( data.upload.filename + ' has sucessfully uploaded.' ); */
      
      if (onResolve) { onResolve (usedFilename + " uploaded "); }
      
     })
    .fail( data => {
      console.error ("API upload returned error: ", JSON.stringify (data), data);
      if (onReject) {onReject (data);}
     });
};



// route dropped files to their ultimate destination
//       TODO ???????????????????????????????????????????????????? who generates the correct mime types ????
//       TODO:    where do we get xml gzip files FROM the wiki ???
//       TODO:     gzip is for transport reasonable - BETTER idea is to ONLY use tjis for transport and thus configure browser and server to do it as part of their transport work !!!!
function routeFiles (buf, mime, filename, target) {
  var report = (data) => addInfoToKundry(document.getElementById ("kundry-hash-drop"), data);
  if      (mime == "application/xml")       { importToWiki (buf, report, report); }  
  else if (mime == "application/xml+gzip")  {  }
  else if (mime == "application/tar")       {  }
  else if (mime == "application/tar+gzip")  {  }
  else                                      { uploadToWiki (buf, mime, filename, report, report);}
  
  
}




///////////////// PUT THIS IN LATER 
// if we are in the File:namespace:  remove the edit UI opportunities for the entire page and the Metadata Store section 


function addInfoToKundry (area, info) {
  var br = document.createElement ("br");
  area.appendChild (br);
  var textNode = document.createTextNode(info);
  area.appendChild (textNode);
}




/** DRAG functions for kundryContainer */
function dragElement(elem, handle) {
  var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
  handle.onpointerdown = dragpointerDown;
  
  function dragpointerDown(e) {
    e.preventDefault(); e.stopPropagation();
    handle.setPointerCapture (e.pointerId);        // need to capture the pointer or we risk losing it in a fast move
    pos3 = e.clientX; pos4 = e.clientY;
    document.onpointerup   = closeDragElement;
    document.onpointermove = elementDrag;
  }

  function elementDrag(e) {
    e.preventDefault(); e.stopPropagation();
    pos1 = pos3 - e.clientX; pos2 = pos4 - e.clientY;
    pos3 = e.clientX;        pos4 = e.clientY;
    elem.style.top = (elem.offsetTop - pos2) + "px"; elem.style.left = (elem.offsetLeft - pos1) + "px";
    console.log ("will persist at drag of iframe");
    persistWindow ("iframe-", elem);
  }

  function closeDragElement(e) {elem.releasePointerCapture (e.pointerId);document.onpointerup = null; document.onpointermove = null;} //////////////// TODO: do we HAVE to release it from elem or rather from handle ???????
}

// CAVE: ***** we build this via DOM API and inject it dynamically at the end, since otherwise we get nasty issues with z-indexOf
// CAVE: we have the first line consisting of three parts, Handle, Meta and Close, since we otherwise get interference with the events as handled in dragElement
function buildKundryContainer () {
    var kCon    = document.createElement ("div");     kCon.setAttribute    ("id", "kundryContainer" );
    var kLine   = document.createElement ("div");     kLine.setAttribute   ("id", "kundryLine"      );
    var kHandle = document.createElement ("div");     kHandle.setAttribute ("id", "kundryHandle"    );   
    var kMeta   = document.createElement ("button");  kMeta.setAttribute   ("id", "kundryMeta"      );    kMeta.setAttribute  ("title","Toggle media meta data area");        kMeta.innerHTML  = "Meta";
    var kClose  = document.createElement ("button");  kClose.setAttribute  ("id", "kundryClose"     );    kClose.setAttribute ("title", "Close Kundry media viewer window");  kClose.innerHTML = "Close";
    var kMin    = document.createElement ("button");  kMin.setAttribute    ("id", "kundryMin"       );    kMin.setAttribute   ("title","Minimize to reasonable size");         kMin.innerHTML = "Min";
    
    //kClose.addEventListener ( "click", (e) => { document.getElementById ("kundryContainer").style.display="none";} );
    kClose.onclick = (e) => { document.getElementById ("kundryContainer").style.display="none";};
    kMin.onclick   = (e) => { Object.assign (document.getElementById ("kundryContainer").style, {width:"300px", height:"300px"});};
    
    kLine.appendChild (kHandle);  kLine.appendChild (kMin); kLine.appendChild (kMeta); kLine.appendChild (kClose); 
    dragElement (kCon, kHandle);
    var kIfra = document.createElement ("iframe");   kIfra.setAttribute ("id", "kundryIframe");
    kMeta.onclick = (e) => {
      console.log ("metaclick");
      var myForm = kIfra.contentWindow.document.getElementById("myForm");
      if (myForm.style.display=="block") {myForm.style.display="none";} else {myForm.style.display="block";}
      //myForm.style.maxHeight="1px";
      
      
    };
    
    // pointer down on a non-button is captured for the sake of a more smooth resize event in cases where the meta data is not shown. Necessary since in this case, usually, the resize mouse movement
    // gets captured by the embed element
    kCon.onpointerdown = (e) => { console.log ("container pointer down ", e.target, e.currentTarget); if (e.target.tagName=="BUTTON") {} else {kCon.setPointerCapture (e.pointerId);  e.stopPropagation();}    };
    kCon.onpointerup   = (e) =>  { console.log ("container pointer up");kCon.releasePointerCapture (e.pointerId);  };
    
    kCon.appendChild (kLine);   kCon.appendChild (kIfra);
    document.body.appendChild (kCon);
   
   (new ResizeObserver ( e => { 
      console.log ("kundry.js: ResizeObserver: sees a resize ", e); 
      if (document.getElementById ("kundryContainer").style.display=="none") { console.log ("kundry.js: not persisting, probably was closed ");} else {console.log ("will persist at resize of iframe"); persistWindow ("iframe-", kCon);}
    }) ).observe ( kCon);
}

// a kundry link has obtained a mouseover event and should be presented now
function kundryMouseover (e) {
  console.log ("kundryMouseover called");
  var hash = e.target.dataset.hashref;     
  var page = e.target.dataset.page;
  var cont = document.getElementById ("kundryContainer");
  var ifra = document.getElementById ("kundryIframe");
  
  var url = "/myExtensions/Kundry/embedPresent.html?hash="+hash + (page ? "&page="+page : "");  ///////////////////  PATH ADJUST !!!!
  console.log ("kundryMouseover will now activate url: " + url);
  
  if (cont.style.display == "flex" && ifra.getAttribute ("src") ==  url ) { console.log ("already showing kundry container as intended, exiting to prevent flashing of screen"); return;}   // already showing      
  cont.style.display="flex";           // CAVE: not "block", since we must have a flex layout here !
  ifra.setAttribute ("src", url);
  
  var sizeObj = getSize ("iframe-");
  console.log ("kundry size object found in localStorage is ", sizeObj);
  
  var kCon = document.getElementById ("kundryContainer");
  Object.assign (kCon.style, { left: sizeObj.left+"px", top: sizeObj.top+"px", width: sizeObj.width+"px", height:sizeObj.height+"px"  } );
};

// initialization code for the quickupload special page - called from a script tag injected by QuickUpload.php
window.kundrySpecialInit = function kundrySpecialInit () { 
  var ele;
  ele = document.getElementById ("kundry-original-drop");  installDropZone (ele,  (buf, filename, mime) => {routeFiles (buf, mime, filename, ele);} , itemsHandler, true);
  ele = document.getElementById ("kundry-hash-drop");      installDropZone (ele,  (buf, fileName, mime) => {routeFiles (buf, mime, filename, ele);} , itemsHandler, true);
  ele = document.getElementById ("kundry-hash-meta-drop"); installDropZone (ele, openMediumEmbedded, itemsHandler,  true);
};


window.initializeHoverLinks = function initializeHoverLinks () { // initialize function of showing references inline when hovering the referencing link  
  var list = document.querySelectorAll ("span[data-hashref]");   // initializes the hover links
  // console.log ("kundry.js: initalizeHoverLinks found: ", list);
  list.forEach ( ele => {
    // console.log ("instrumenting with kundryMouseover: ", ele);
    ele.addEventListener ("mouseover", kundryMouseover); }); 
  
  if ( mw.config.get ('wgNamespaceNumber') ==  6 ) { //////////////// TODO WHAT IS THIS ??????????????????
   // $("#Metadata_Store").next().hide();
   // $("#ca-edit").hide();
  TEST1();
  }   
  buildKundryContainer();
};


// links with a target of "_popup"   should open in a seperate window 
window.initializeTargetLinks = function initializeTargetLinks () {
  //document.querySelectorAll ('a[target="_tab"]').forEach ( ele => {});

//////////////////////// TODO: show warning / info in case a popup blocker is active !

  document.querySelectorAll ('a[target="_popup"]').forEach ( ele => {ele.onclick = (e) => {e.preventDefault(); window.open (e.target.href, "_blank", "popup");} });
};


// THIS STUFF not yet working - needed it for dual views


/*


$(".removeTargetClass").find('a[target]').removeAttr("target");
$(".blankTargetClass").find('a').attr("target","_blank");
$(".showReferrer").find('a[rel]').each( (idx,ele) => {var newAtt = ele.getAttribute("rel").split(" ").filter( x => (x != 'noreferrer') ).join(" "); ele.setAttribute("rel", newAtt)});



*/



function initializeKundry () {  // initialization function 
  if ( typeof mw != "undefined" && mw.config.get ('wgNamespaceNumber') != -1) {    // if mw is defined (by Mediawiki) and we are not in special namespace
    installDropZone (document.documentElement, dropHandler, itemsHandler, false); 
    initializeHoverLinks ();   // in all cases we can initialize the hover links; when they are missing on a page they will nto be instrumented 
    initializeTargetLinks ();
   // mw.util.addPortletLink ('p-cactions', 'javascript:alert(1);', 'Export', 'ca-export', 'Export page in xml format');
  }
  else {}
}



$(document).ready ( initializeKundry );
//initializeKundry();






window.openKundryWindow = openKundryWindow;
window.hoverKundy = hoverKundry;

})(); // END SCOPE PROTECTION



