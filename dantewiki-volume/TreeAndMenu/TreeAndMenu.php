<?php


class TreeAndMenu {
  const TREE = 1;
  const MENU = 2;

public static function onParserFirstCallInit (Parser $parser) {                        // set the parser functions for  #tree   and  #menu
  $parser->setFunctionHook('tree', array('TreeAndMenu', 'expandTreeX') );
  $parser->setFunctionHook('menu', array('TreeAndMenu', 'expandMenuX') );
  return true;
}

// go from directory to url path independently from all other things. This is helpful when extensions are located at a different place than usual.
private static function get_current_file_url() { return  ( array_key_exists ("HTTPS", $_SERVER) ? "https://" : "http://" ) . $_SERVER['HTTP_HOST'].str_replace( realpath ($_SERVER['DOCUMENT_ROOT']) , '', realpath(__DIR__) )."/" ;  }


public static function onSkinAfterPortlet ( $skin, $portlet, &$html ) {
  global $wgSitename, $wgOut;
  
 
  
  
  
  
  $wgOut->addModules  ('ext.fancytree');                                                          // this effectively adds the JS files
  $wgOut->addStyle    (self::get_current_file_url()."fancytree/fancytree.css");                   // we need the style sheet earlier than the module loader would make it available; without this we have a flickering in the site name
  $wgOut->addHeadItem ("preload", "<link rel='preload' href='/myExtensions/TreeAndMenu/fancytree/icons.gif?eff18' as='image'>");  // TODO:  ?????????????????????? should preload as this is requested late - but the issue with the query number is still open !!

  // $html = $html. "<script> console.error ('".self::get_current_file_url()."');</script>";      // in development: display the path
   //$html = $html. "<script> console.error ('$portlet');</script>";  print_r ($portlet);         // in development: display the $portlet names

  if ( in_array ( $portlet,  array ("namespaces", "variants", "views", "cactions",  "lang") ) ) { return true; }   // we do not touch these portlets here, they are not in the sidebar or not used by us - so we EXIT this function

  // INJECT the name of the specific dantepedia   // TODO: THIS probably could go to a different place  /////////////////////////////////// TODO: we might want to place this above the "Areas" in the Sidebar - this is the more natural place to see it there !!!!!!!
  if ( strcmp ("personal", $portlet) == 0) {
    $selector = "<select class='personal-wiki-select' onchange='window.selectorChanged(event);' title='Name of the specific dantewiki to distinguish multiple variants. Click to display links to others as registered in MediaWiki:DanteWikis'>";    
    $configPage = "Dantewikis";                                                                 // name of the MediaWiki:Dantewikis configuration page of this thing
    $title      = Title::newFromText( $configPage, NS_MEDIAWIKI );                              // build title object for MediaWiki:SidebarTree
    $wikipage   = new WikiPage ($title);                                                        // get the WikiPage for that title
    $contentObject = $wikipage->getContent();                                                   // and obtain the content object for that
    if ($contentObject ) {                                                                      // IF we have found a content object for this thing
      $parserOutputObject = $contentObject->getParserOutput ($title, null, null, true);         // parse the content object on this page
      $options = array( 'unwrap' =>true, 'wrapperDivClass' => "myWRAPPER" );
      $code = $parserOutputObject->getText ( $options );  
      $code = trim ($code);
      $code = substr ($code, 3, strlen($code)-7);  // remove the leading <p> and the trailing </p> portions
      // self::debugLog ("\n\n Dantewikis configuration text is: " . $code . "  \n\n");
      $arr = json_decode ($code, true);
      foreach ($arr as $val) {
        $name  = $val["name"];
        $class = $val["class"];
        $base  = $val["base"];
        $selected = ($name == $wgSitename ? "selected"  : "");
        $selector .= "<option data-class='$class' data-base='$base' data-name='$name' value='$name' $selected>$name</option>";
      }
      $selector .= "</select>";    
      $html =  $selector . $html;
      return true;
    }
  }

   $html = $html . self::overridePortlet ( $portlet );


  // the category tree is specified as an array of objects of which there are two variants    {tree: CatName, depth:4}   or   {node: CatName}
  // every object may also have an additional   className   and an additional  style  attribute  to be used for styling the entry
  if (strcmp ("catus", $portlet) == 0) {
    $cHtml = "<ul>";                     // start rendering the category tree information
    $catConfig = self::getCatConfig ();  // array of objects {name, style} or {root, depth, style}
    
    foreach ($catConfig as $obj) {       // iterate all objects in the configuration array
      $style = ( array_key_exists ("style", $obj) ? $obj["style"]: "");
      if (array_key_exists ("name", $obj))  { $cHtml = $cHtml . "<li>".self::buildLink($obj["name"], $style)."</li>"; }
      if (array_key_exists ("root", $obj))  { 
        $kids  = self::getChildren ($obj["root"]);

        foreach ($kids as $key => $value) {$cHtml .= self::renderCatKids ($key, $obj["depth"], $style );};
      }
    }   
    $html  =    "<div class=\"fancytree todo\" id=\"sidebar-cattree\" style=\"display:none;\" >$cHtml </div>";  // style: show only after cats are adjusted; todo: MUST mark for later tree expansion  fancytree: MUST mark as being a fancytree base 
  }
  
  // the JS loading process is asynchronous so we do not know if the fancytree.js already has loaded before or after and we want to decouple us from its construction as well - so we just place a marker which may be used by that fancytree.js glue code if appropriate
  if (strcmp ("tb", $portlet) == 0) { $html = $html. "<script>window.sidebarCompleted = true;</script>"; }  // informt js that the sidebar is completed now  
      
  return true;
}

// deprecated 
/*
private static function renderHelper ($cat, $depth, $sty) {  // helper function translating the cat/dep/sty preference into 
  $cat   = trim ($cat);
  if ( $cat === '') {return "";}  // skip an entry if the name of the category is empty 
  if ( trim($depth) === '' ) {return "<li>".self::buildLink($cat, $sty)."</li>";}  // depth preference is empty string: render only the given name 
  
  // we are still here, so depth was not the empty string  
  $depth = intval ( trim($depth) );

  $kids  = self::getChildren ($cat);
  $cHtml = "";
  foreach ($kids as $key => $value) {$cHtml .= self::renderCatKids ($key, $dep, $color[0] );};     ///// TODO STYLE / color !!!!
  return $cHtml;
  
  
}
*/




// if a $configPage exists in MediaWiki namespace, return portlet content according to this page; if not: return 
private static function overridePortlet ($name) {
  $configPage = "Sidebar/$name";                                                              // name of the MediaWiki:Sidebar$name configuration page of this treelet
  $title      = Title::newFromText( $configPage, NS_MEDIAWIKI );                              // build title object for MediaWiki:SidebarTree
  $wikipage   = new WikiPage ($title);                                                        // get the WikiPage for that title
  $contentObject = $wikipage->getContent();                                                   // and obtain the content object for that
  if ($contentObject ) {                                                                      // IF we have found a content object for this thing
    $parserOutputObject = $contentObject->getParserOutput ($title, null, null, true);         // parse the content object on this page
    $options = array( 'unwrap' =>true, 'wrapperDivClass' => "myWRAPPER" );
    $code = $parserOutputObject->getText ( $options );  
 //   return "<script>window.patchinArea(document.currentScript);</script><div data-ixxi>$code </div>";    // data-ixxi is a debug indicator to find this more easily
    return "<div data-ixxi>$code </div>";    // data-ixxi is a debug indicator to find this more easily   
  }   
  else { return "";}
}


// get the JSON config object for categories
private static function getCatConfig () {
  $configPage = "Sidebar/Categories";                                                         // name of the MediaWiki:Sidebar$name configuration page of this treelet
  $title      = Title::newFromText( $configPage, NS_MEDIAWIKI );                              // build title object for MediaWiki:SidebarTree
  $wikipage   = new WikiPage ($title);                                                        // get the WikiPage for that title
  $contentObject = $wikipage->getContent();                                                   // and obtain the content object for that
  if ($contentObject ) {                                                                      // IF we have found a content object for this thing
    $parserOutputObject = $contentObject->getParserOutput ($title, null, null, true);         // parse the content object on this page
    $options = array( 'unwrap' =>true, 'wrapperDivClass' => "myWRAPPER" );
    $code = $parserOutputObject->getText ( $options );  
    $code = trim ($code);
    $code = substr ($code, 3, strlen($code)-7);  // remove the leading <p> and the trailing </p> portions
    // self::debugLog ("\n\n Categories configuration text is: " . $code . "  \n\n");
    $obj = json_decode ($code, true);
    if ($obj == null) { // self::debugLog ("\n\n Categories configuration object could not be parsed  \n\n"); 
      return "Could not parse MediaWiki:Sidebar/Categories";} 
    else {  // self::debugLog ("\n\n Categories configuration object in php is: " . print_r ($obj, true) . "  \n\n");
    return $obj;}
  }   
  else { 
    // self::debugLog ("\n\n Categories configuration object could not be found \n\n");
    return "Could not find MediaWiki:Sidebar/Categories";}
}




////// TODO:  PATH FIX ??????????????
///// TODO: myExtensions path names must be removed !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!  Several places here !!




static function debugLog ($text) {if($tmpFile = fopen( "/var/www/html/myExtensions/TreeAndMenu/LOGFILE", 'a')) {fwrite($tmpFile, $text);  fclose($tmpFile);}  else {throw new Exception ("debugLog could not log"); }}


// format the $name of a category in an <a> and <span> for fancy tree
// note: we need the span wrapping in order for the <a> to make it into the tree
static  function buildLink ($name, $style = "") {
  $name = str_replace ("_", " ", $name);          // we get the category names with _ from the DB but we render them without the _ to the tree
  return "<span ><a style='$style' target='_blank' title='Open category page in new window or tab'  onclick='window.openAsPopup(event);'  href='./Category:".$name."'>".$name."</a></span>";} 


// $depth <= 0 number of ul's we are still allowed to open
//
// Render the category tree
// $names
// $depth number of levels we are still going to descend 
// $me    true means: also render myself, i.e. $name
//        false means: only render my kids
static function renderCat ($name, $depth, $me) {
  if ($depth <= 0) {return "<li>" . self::buildLink($name) . "</li>" ;}                  

  $local = "";
  if ($me) {$local = "<li>".self::buildLink($name);}
  $categs = self::getChildren($name);                        // get categories which are children of $name
  if ( count ($categs) > 0 ) {                               // if we found children of $name, warp them in <ul>...</ul>
    $local .= "<ul>";
    foreach ($categs as $key => $value) { $local .=   self::renderCat ( $key, $depth - 1, true) ; }
    $local .= "</ul>";
  }
  else {}
  if ($me) {$local .= "</li>";}
  return $local;
}


// MORE complex render mechanism then renderCat
// render first the categories in array $prefix without going down into the hierarchy
// then for every cat name in the array infix, render all subcategories up to depth $depth, changing colors for every element from $infix 
// and finally render the categories in array $postfix without going down in to the hierarchy
static function renderCatTop ($depth, $prefix, $infix, $postfix) {
  $ret = "<ul>";      // open the entire rendering
  foreach ($prefix as $val)  { $ret .= "<li>".self::buildLink($val, "font-weight:bold;")."</li>"; }
  
  $num = 0;
  $color = array ("color:red;", "color:blue;"); 
  foreach ($infix as $val)  {
    $kids = self::getChildren ($val);
    foreach ($kids as $key => $value) {$ret .= self::renderCatKids ($key, $depth, $color[$num % 2]);};
    $num++;
  }
    
  foreach ($postfix as $val) { $ret .= "<li>".self::buildLink($val)."</li>"; }
  
  $ret = $ret . "</ul>";   // close the entire rendering
  //self::debugLog ("renderCatTop returns: " . $ret);
  return $ret;
}


static function renderCatKids ($name, $depth, $style="") {
  if ($depth <= 0) {return "<li>" . self::buildLink($name, $style) . "</li>" ;}    // we have reached the depth limit, only return a packaged <li> ... </li> link   
  $local = ""; 
  $local = "<li>".self::buildLink($name, $style);                    // render the $name itself and then descend into its children
  $categs = self::getChildren($name);                                     // get categories which are children of $name
  if ( count ($categs) > 0 ) {                                            // if we found children of $name, warp them in <ul>...</ul>
    $local .= "<ul>";
    foreach ($categs as $key => $value) { $local .=   self::renderCatKids ( $key, $depth - 1, $style) ; }  // and descend into them
    $local .= "</ul>";
  }
  else {}
  $local .= "</li>";
  return $local;
}

  // get all the categories which are children of $root
  public static function getChildren($root, $depth = 1) {
    $allCats    = array();                                  // Initialize return value
    $dbObj      = wfGetDB(DB_MASTER);                       // Get a database object  CHC changed to master for freshness
    $CATEGORYLINKS = $dbObj->tableName('categorylinks');    // Get table names to access them in SQL query
    $PAGE    = $dbObj->tableName('page');

    // The normal query to get all children of a given root category
    $sql =
  'SELECT tmpSelectCatPage.page_title AS title FROM ' . $CATEGORYLINKS . ' AS tmpSelectCat
   LEFT JOIN ' . $PAGE . ' AS tmpSelectCatPage ON tmpSelectCat.cl_from = tmpSelectCatPage.page_id
   WHERE tmpSelectCat.cl_to LIKE ' . $dbObj->addQuotes($root) . ' AND tmpSelectCatPage.page_namespace = 14 ORDER BY tmpSelectCatPage.page_title ASC;';

    $res = $dbObj->query($sql, __METHOD__);          // Run the query
    while ($row = $dbObj->fetchRow($res)) {          // Process the resulting rows
      if ($root == $row['title']) {continue;}        // Survive category link loops
      $allCats += array($row['title'] => $depth);    // Add current entry to array
   //   $allCats += self::getChildren($row['title'], $depth + 1);  // NOTE: THIS IS ABOUT DOING IT TRANSITIVELY OR NOT !!!!
    }
    $dbObj->freeResult($res); // Free result object
    // Afterwards return the array to the upper recursion level

    return $allCats;
  }

   // #tree parser-functions
  public static function expandTreeX() { $args = func_get_args(); return TreeAndMenu::expandTreeAndMenuX( 'TREE', $args); }

  // #menu parser-functions
  public static function expandMenuX()  {$args = func_get_args(); return TreeAndMenu::expandTreeAndMenuX( 'MENU', $args);}

  // Render a bullet list for either a tree or menu structure
  // CALLED ONLY by expandTreeX or expandMenuX which provide the #tree and #menu parser functions 
  private static function expandTreeAndMenuX($type, $args) {
    global $wgTreeAndMenuPersistIfId;

    // First argument in $args array is parser, last is the bullet structure
    $parser  = array_shift($args);
    $bullets = array_pop($args);    

    // self::debugLog ( "\n\n Bullets: " . print_r ($bullets, true). "\n");     // contains the parse of the contents of the #tree function with parser functions such as fullurl already applied; json portion prefixes of the nodes are still there
    // self::debugLog ( "\n\n Args: " . print_r ($args, true). "\n");           // is an array reflecting the parameters contained in the pipe-seperated portions of the parser function calls

    // Convert other args (except class, id, root) into named opts to pass to JS (JSON values are allowed, name-only treated as bool)
    $opts = array();
    $atts = array();
    foreach ($args as $arg) {
      if (preg_match('/^(\\w+?)\\s*=\\s*(.+)$/s', $arg, $m)) {
        if ($m[1] == 'class' || $m[1] == 'id' || $m[1] == 'root') {$atts[$m[1]] = $m[2];}
        else {$opts[$m[1]] = preg_match('|^[\[\{]|', $m[2]) ? json_decode($m[2]) : $m[2];}
      }
      else {$opts[$arg] = true;}
    }

    // If the $wgTreeAndMenuPersistIfId global is set and an ID is present, add the persist extension
    if (array_key_exists('id', $atts) && $wgTreeAndMenuPersistIfId) {
      if (array_key_exists('extensions', $opts)) {$opts['extensions'][] = 'persist';}
      else {$opts['extensions'] = array( 'persist' );}
    }

    // Sanitise the bullet structure (remove empty lines and empty bullets)
    $bullets = preg_replace ('|^\*+\s*$|m', '', $bullets);
    $bullets = preg_replace ('|\n+|', "\n", $bullets);

    // If it is a tree, wrap the item in a span so FancyTree treats it as HTML and put nowiki tags around any JSON props
    if ($type == 'TREE') {
      $bullets = preg_replace('|^(\*+)(.+?)$|m', '$1<span>$2</span>', $bullets);
      $bullets = preg_replace('|^(.*?)(\{.+\})|m', '$1<nowiki>$2</nowiki>', $bullets);
    }
    
    // Parse the bullets to HTML
    $opt  = $parser->getOptions();
    $html = $parser->parse($bullets, $parser->getTitle(), $opt, true, false)->getText( ['unwrap' => true] ); 

    // Determine the class and id attributes
    $class = ($type == 'TREE' ? 'fancytree' : 'suckerfish');
    if (array_key_exists('class', $atts)) {$class .= ' ' . $atts['class'];}
    $id = array_key_exists('id', $atts) ? ' id="' . $atts['id'] . '"' : '';

    if ($type == 'TREE') {
      // Mark the structure as tree data, wrap in an unclosable top level if root arg passed (and parse root content)
      // style below was: display:none; CHC changed to visibility:hidden, which makes it more smooth in display (no flicker)

      $tree = '<ul id="treeData" style="visibility:hidden;">';

      if (array_key_exists('root', $atts)) {
        $root = $parser->parse($atts['root'], $parser->getTitle(), $parser->getOptions(), false, false)->getText();
        $html = $tree . '<li class="root">' . $root . $html . '</li></ul>';
        if (! array_key_exists('minExpandLevel', $opts)) $opts['minExpandLevel'] = 2;
      }
      else {$html = preg_replace('|<ul>|', $tree, $html, 1); }

      // Replace any json: markup in nodes into the li
      $html = preg_replace('|<li(>\s*\{.*?\"class\":\s*"(.+?)")|',     "<li class='$2'$1", $html);
      $html = preg_replace('|<(li[^>]*)(>\s*\{.*?\"id\":\s*"(.+?)")|', "<$1 id='$3'$2", $html);
      $html = preg_replace('|<(li[^>]*)>\s*(.+?)\s*(\{.+\})\s*|',      "<$1 data-json='$3'>$2", $html);

      // Incorporate options as json encoded data in a div
      $opts = count($opts) > 0 ? '<div class="opts" style="display:none">' . json_encode($opts, JSON_NUMERIC_CHECK) . '</div>' : '';

      $html = "<div data-from='TreeAndMenuBody' class=\"$class todo\"$id>$opts$html</div>";                // Assemble it all into a single div
    } // If its a menu, just add the class and id attributes to the ul
    else { $html = preg_replace('|<ul>|', "<ul class=\"$class todo\"$id>", $html, 1); }
    return array($html, 'isHTML' => true,  'noparse' => true );
  }


} // class
