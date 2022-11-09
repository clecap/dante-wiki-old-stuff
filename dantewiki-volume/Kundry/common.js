
/* common.js contains code which is used as part of 
  (1) the mediawiki web pages
  (2) the embedPresent.html page when it is used in a stand alone window
  (3) the embedPresent.html page when it is used inside of an iframe on the mediawiki page
  (4) the system which is used for inspecting local file systems (dragdrop.html)

  It is included into the mediawiki page via the extension.json bundle and into embedPresent.html via script tag

*/


// show html text for a short moment as a notification of the user
function notify (content) {
  var ele = document.createElement ("div");
  ele.id = ele.className = "kundryNotification";
  ele.innerHTML = content;
  document.body.appendChild (ele);
  window.setTimeout (() => {
    var parent = ele.parentNode;
    parent.removeChild (ele);
  }, 50000);
}

function clearNotify () { var ele = document.getElementById ("kundryNotification"); if (ele && ele.parentNode) {ele.parentNode.removeChild (ele);} }





// promise form of function writing text into clipboard
function text2Clipboard (text) {
  return new Promise ( (resolve, reject) => {
    navigator.clipboard.writeText (text).then(function() {console.log ("clipboard write worked"); resolve (text);}, 
     function() {console.error ("clipboard write failed"); reject ();});    
  });
}

// pick up size from localStore, using a specific prefix
function getSize (pfx) {
  var width  = localStorage.getItem (pfx + "Width");  width  = parseInt (width);  /* console.log ("w", width);  */ if ( isNaN (width) )   {width=window.screen.availWidth/5;}
  var height = localStorage.getItem (pfx + "Height"); height = parseInt (height); /* console.log ("h", height); */ if ( isNaN (height) )  {height = window.screen.availHeight;}
  var left   = localStorage.getItem (pfx + "Left");   left   = parseInt (left);   /* console.log ("l", left);   */ if ( isNaN (left) )    {left= 0;}
  var top    = localStorage.getItem (pfx + "Top");    top    = parseInt (top);    /* console.log ("t", top);    */ if ( isNaN (top ) )    {top = 0;} 
  width = (width <= 100 ? 100 : width);  height = (height <= 100 ? 100 : height);   // set to a resonable minimal size 
  left  = (left > 0 ? left : 0); top = (top > 0 ? top : 0);                         // should not be negative in order not to lose window 
  left  = (left > window.innerWidth -10 ? window.innerWidth - 10 : left); top = ( top > window.innerHeight -10 ? window.innerHeight - 10 : top);  // should not be too large in order not to lose window
  console.log ("------------------getSize for prefix=" + pfx + " obtained return ", {width, height, left, top} );
  return {width, height, left, top};
}

// persist size and position of the opened window, using a specific prefix; called below
function persistWindow (pfx, elem) {
  console.log ("persistWindow called");
  if (elem instanceof Window) {localStorage.setItem (pfx+"Width",  elem.outerWidth);  localStorage.setItem (pfx+"Height", elem.outerHeight);  localStorage.setItem (pfx+"Left", elem.screenX);     localStorage.setItem (pfx+"Top", elem.screenY);   } 
  else                        {localStorage.setItem (pfx+"Width",  elem.clientWidth); localStorage.setItem (pfx+"Height", elem.clientHeight); localStorage.setItem (pfx+"Left", elem.style.left);  localStorage.setItem (pfx+"Top", elem.style.top); }
};



const HASH = async (obj) => {  // generate hex string hash for <obj> which may be: FileSystemFileHandle, File, ArrayBuffer, Blob, String
  var hashBuffer;    
  //console.log ("called HASH:", obj, typeof obj);
  if (obj instanceof FileSystemFileHandle) {obj = await obj.getFile();     }                    // result: obj is File
  if (obj instanceof File)                 {obj = await obj.arrayBuffer(); }                    // result: obj is ArrayBuffer
  if (typeof obj == "string")              {obj = (new TextEncoder()).encode(obj).buffer;}      // result: obj is Uint8Array
  if (obj instanceof Blob)                 {obj = await new Response(blob).arrayBuffer();}      // result: obj is ArrayBuffer
  if (obj instanceof ArrayBuffer) {
    hashBuffer         = await crypto.subtle.digest('SHA-1', obj);                                       // hash
    const hashArray    = Array.from(new Uint8Array(hashBuffer));                                         // convert buffer to byte array
    const hashHex      = hashArray.map(b => b.toString(16).padStart(2, '0')).join('').toUpperCase();     // convert bytes to hex string and go to upper case (needed, since mediawiki capitalizes page titles anyhow)
    return hashHex;
  }
  throw new Error ("common.js: incorrect type submitted to HASH");  // this should not happen - but somehow the logic did not work out
};


const removeExtension = (txt) => {
  var pos = txt.lastIndexOf ("."); 
  if (pos == -1) {return txt;} else {return txt.substring (0, pos);}
};





