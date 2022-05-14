<?php

### THIS stays in while not in production
$wgShowExceptionDetails = true;
$wgShowDBErrorBacktrace = true;
$wgShowSQLErrors = true;







/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Keep this in only while workign on code !
error_reporting( -1 );
ini_set( 'display_errors', 1 );
$wgShowExceptionDetails = true;

$wgEnableParserCache = false; // deprecated method
$wgParserCacheType = CACHE_NONE;
$wgCachePages = false;
opcache_reset();                    ////////////////////////////////////////////  MUST REMOVE THIS ON PRODUCTION SITE, MAKES IT VERY SLOW BUT READS PHP FROm SCRATCH 





















###
### Do some Dantewiki configuration
###
$wgUseRCPatrol = false;                                  // turn off page patrolling, we are not using this

# set permissions: only admin may edit
$wgGroupPermissions['*']['edit']     = false;
$wgGroupPermissions['user']['edit']  = false;
$wgGroupPermissions['sysop']['edit'] = true;

# set permissions: only admin may create pages
$wgGroupPermissions['*']['createpage']     = false;
$wgGroupPermissions['user']['createpage']  = false;
$wgGroupPermissions['sysop']['createpage'] = true;

# only admin may create accounts
$wgGroupPermissions['*']['createaccount'] = false;

# remove a number of rights for the users
$wgGroupPermissions['user']['changetags']  = false;
$wgGroupPermissions['user']['applychangetags']  = false;
$wgGroupPermissions['user']['editcontentmodel']  = false;
$wgGroupPermissions['user']['move-categorypages']  = false;
$wgGroupPermissions['user']['movefilel']  = false;
$wgGroupPermissions['user']['move']  = false;
$wgGroupPermissions['user']['move-subpages']  = false;
$wgGroupPermissions['user']['move-rootuserpages']  = false;
$wgGroupPermissions['user']['reupload-shared']  = false;
$wgGroupPermissions['user']['reupload']  = false;
$wgGroupPermissions['user']['purge']  = false;
$wgGroupPermissions['user']['upload']  = false;
$wgGroupPermissions['user']['writeapi']  = false;
$wgGroupPermissions['user']['spamblacklistlog']  = false;



$wgCapitalLinks = false;     // turn of the mechanism which forces the first letter of a title to be capitalized



wfLoadExtension( 'CategoryTree' );
wfLoadExtension( 'Cite' );
// wfLoadExtension( 'CiteThisPage' );         // usage not clear
wfLoadExtension( 'CodeEditor' );
wfLoadExtension( 'ConfirmEdit' );
wfLoadExtension( 'Gadgets' );
wfLoadExtension( 'ImageMap' );
wfLoadExtension( 'InputBox' );
// wfLoadExtension( 'Interwiki' );          // use in our use case not clear
wfLoadExtension( 'LocalisationUpdate' );
wfLoadExtension( 'MultimediaViewer' );
// wfLoadExtension( 'Nuke' );               // use in our use case not clear

// wfLoadExtension( 'OATHAuth' );              // needs a run of update

wfLoadExtension( 'PageImages' );
wfLoadExtension( 'ParserFunctions' );
wfLoadExtension( 'PdfHandler' );
wfLoadExtension( 'Poem' );
wfLoadExtension( 'Renameuser' );
wfLoadExtension( 'ReplaceText' );
wfLoadExtension( 'Scribunto' );
wfLoadExtension( 'SecureLinkFixer' );
// wfLoadExtension( 'SpamBlacklist' );        // this is a private edit wiki only, so we have no spam blocking
wfLoadExtension( 'SyntaxHighlight_GeSHi' );
wfLoadExtension( 'TemplateData' );
wfLoadExtension( 'TextExtracts' );
wfLoadExtension( 'TitleBlacklist' );
// wfLoadExtension( 'VisualEditor' );    // does not work, see    https://www.mediawiki.org/wiki/Topic:Vv35plp6g16qno0s
wfLoadExtension( 'WikiEditor' );



// On some occasions, mediawiki produces two comments in a row and the browser then interprets this as a paragraph. This leads to
// unwanted additional distance between some tree constructions and headers in the sidebar. As a solution we turn off these parser reports.
$wgEnableParserLimitReporting= false;

// enable subspaces in the MAIN namespace
$wgNamespacesWithSubpages = array( NS_MAIN);


$wgSpamRegex=[];   // this is a private only edit wiki, so we have no spam blocking


// installed additionally by CHC (if from different directory, need different form of installation
// 
// Group Administrators is "sysop". Not so clear from the documentation.
$wgGroupPermissions['sysop']['dumprequestlog'] = true;
$wgGroupPermissions['sysop']['dumpsondemand'] = true;
$wgGroupPermissions['sysop']['dumpsondemand-limit-exempt'] = true;
$wgDumpsOnDemandUseDefaultJobQueue=true;


// CHANGE the paths since dantewiki has its own extensions in /myExtensions
$wgExtensionDirectory = "/var/www/html/myExtensions/";
$wgExtensionassetsPath = "$wgScriptPath/myExtensions";


wfLoadExtension( 'DumpsOnDemand', '/var/www/html/myExtensions/DumpsOnDemand/extension.json' );


wfLoadExtension( 'Push', '/var/www/html/myExtensions/Push/extension.json' );

//         Config of Push must come after loading Push
$egPushTargets['English Wikipedia'] = 'http://en.wikipedia.org/w';
$egPushTargets['Local MW 1.16']     = 'http://localhost/mw116';
$egPushTargets['Local MW trunk']    = 'http://localhost/phase3';




// $wgEnableParserLimitReporting=false;


wfLoadExtension( 'TreeAndMenu', '/var/www/html/myExtensions/TreeAndMenu/extension.json' );  //////// NAME: NG !?!?!?




wfLoadExtension ('Kundry', '/var/www/html/myExtensions/Kundry/extension.json' );
$wgGroupPermissions['sysop']['read-kundry-endpoint'] = true;


/* configure SVG support */
$wgSVGConverter="rsvg";
$wgSVGConverters = array('rsvg' => '/usr/bin/rsvg-convert -w $width -h $height $input -o $output');   


/////////////////////////////////////////////////////////  ADD for comments:  either use disqus or apply here for a login. In both cases: Subject to my censorship. 



/* configure uploads */
$wgEnableUploads = true;           // enable uploads
$wgMaxUploadSize = 100000000;      // allow uploads up to 100MB size
$wgFileExtensions[] = 'pdf';
$wgHashedUploadDirectory=false;     // we need 

$wgFileExtensions = array( 'png', 'gif', 'jpg', 'jpeg', 'mpp', 'pdf', 'tiff', 'bmp', 'svg');  // file extensions which may be uploaded

$wgGroupPermissions['sysop']['reupload'] = true;  // ensure sysop/admin is allowed to reupload images which ahd been deleted



$wgGroupPermissions['sysop']['deletelogentry'] = true;
$wgGroupPermissions['sysop']['deleterevision'] = true;





// EXPORTING: configure hidden options of Special:Export,   see https://www.mediawiki.org/wiki/Category:Import/Export_variables
$wgExportAllowAll = true;                      // allow exporting the entire wiki into a single file
$wgExportAllowHistory = true;                  // allow to select full-history in Special:Export
$wgExportFromNamespaces = true;                // allow to select export from various namespaces
$wgExportAllowListContributors = true;
$wgExportPagelistLimit = 100000;               // may export up to 100.000 pages per category
$wgExportMaxLinkDepth = 10;                    // maximum value in pagelink-depth fro Special:Export
$wgExportMaxHistory = 0;                       // if nonzero, requests for history of pages with more revisions than this will be rejected


/////////////////////////  TODO: TODO: TODO: ALL The mediawiki images are served by the webserver - we must !!!!!!!!!!!!!!!!!!!!!!!!!!!!! look at https://www.mediawiki.org/wiki/Manual:Image_authorization  to restrict free access to images 
// otherwise who has the hash can check if we have the file !!!!!!!!!!!!!!!!

$wgUseImageMagick            = true;
$wgImageMagickConvertCommand = "/usr/bin/convert";
$wgGenerateThumbnailOnParse  = true;



// PATCH the TOOLBOX
$wgHooks['SidebarBeforeOutput'][] = function ( Skin $skin, &$sidebar ) {
  global $wgOut;
  $revisionId = $wgOut->getRevisionId();
  
  
  
  static $keywords = array( 'WHATLINKSHERE', 'RECENTCHANGESLINKED', 'FEEDS', 'CONTRIBUTIONS', 'LOG', 'BLOCKIP', 'EMAILUSER', 'USERRIGHTS', 'UPLOAD', 'SPECIALPAGES', 'PRINT', 'PERMALINK', 'INFO' );
  
  $modifiedToolbox = array();                                // this will be the new toolbox
  
 // $parserOptions = $wgOut->mParserOptions;
 // $tit = $parserOptions->getRedirectTarget();
 // $artid = $tit->getArticleId();
  // outputpage -> $mParserOptions  liefert ParserOptions;    $redirectTarget liefert Title  , der liefert PageIdentity
  
  $modifiedToolbox['asadqwf'] = ["text" =>  "RevisionId: ".$revisionId];
 $modifiedToolbox['asadqwfhuhiu'] = ["text" =>  "Timestamp: ".$wgOut->getRevisionTimestamp()];  
  $modifiedToolbox['asadqwfhuhiuqjjwqwq'] = ["text" =>  "Canonical: ".$wgOut->getCanonicalUrl()];  
  $modifiedToolbox['asadqwfhuhiuqjjwqwqad'] = ["text" =>  "Title: ".$wgOut->getPageTitle()];     ///// getPageTitle looks like it only is the string variant - need the full object for the ID !!!!!
  
  
  $modifiedToolbox['slow']  = ["text" => "Form Upload",   "href" => "/index.php/Special:Upload", "title" => "Upload using drag and drop with faster Kundry method"];  
  $modifiedToolbox['quick'] = ["text" => "Quick Upload",  "href" => "/index.php/Special:Quick_Upload", "title" => "Upload using drag and drop with faster Kundry method"];
  
  $modifiedToolbox['pagedump'] = ["text" => "Page Dump",  "href" => "/index.php/Special:PageDump", "title" => "Upload using drag and drop with faster Kundry method"];
  
  $modifiedToolbox['back1'] = ["text" => "Backup (pages)",     "href" => "/index.php/Special:Quick_Upload", "title" => "Upload using drag and drop with faster Kundry method"];  
  $modifiedToolbox['back2'] = ["text" => "Backup (files)",     "href" => "/index.php/Special:Quick_Upload", "title" => "Upload using drag and drop with faster Kundry method"];  
  $modifiedToolbox['back3'] = ["text" => "Backup (database)",  "href" => "/index.php/Special:Quick_Upload", "title" => "Upload using drag and drop with faster Kundry method"];  
  
  $modifiedToolbox['rest1'] = ["text" => "Restore (pages) - PUT INTO QUICKUPLOAD !!!",     "href" => "/index.php/Special:Quick_Upload", "title" => "Upload using drag and drop with faster Kundry method"];  
  $modifiedToolbox['rest2'] = ["text" => "Restore (files)",     "href" => "/index.php/Special:Quick_Upload", "title" => "Upload using drag and drop with faster Kundry method"];  
  $modifiedToolbox['rest3'] = ["text" => "Restore (database)",  "href" => "/index.php/Special:Quick_Upload", "title" => "Upload using drag and drop with faster Kundry method"];    
  
  foreach ( $sidebar['TOOLBOX'] as $key => $value ) {        // iterate existing toolbox links
    if ( strcmp ($key, "print") == 0 )  { continue;}
    if ( strcmp ($key, "upload") == 0 ) { continue;}
    $modifiedToolbox[$key] = $value;
  }
  $sidebar['TOOLBOX'] = $modifiedToolbox;
};














?>