<?php

use MediaWiki\Rest\SimpleHandler;
use MediaWiki\Rest\Handler;  
use Wikimedia\ParamValidator\ParamValidator;
use MediaWiki\MediaWikiServices;

class Kundry {
  
  static function debugLog ($text) {if($tmpFile = fopen( "/var/www/html/myExtensions/Kundry/LOGFILE", 'a')) {fwrite($tmpFile, $text);  fclose($tmpFile);}  else {throw new Exception ("debugLog could not log"); }}
  
  public static function onParserFirstCallInit( Parser $parser ) {                                                             // Register parser callback hooks   
    $parser->setHook ( 'json',  function ($in, $ar, $parser, $frame) { return  Kundry::json ($in, $ar, $parser);}    );        // implement a <json> construct    
    $parser->setHook ( 'href',  function ($in, $ar, $parser, $frame) { return  Kundry::href ($in, $ar, $parser);}    );        // implement a <href>  hovering reference tag:.. h ref for Hovering REFerence
    $parser->setHook ( 'kexd',  function ($in, $ar, $parser, $frame) { return  Kundry::kexd ($in, $ar, $parser);}    );        // TO TEST Kundry marriage with DPL3   TODO
  }

// adds export actions to the page
public static function onSkinTemplateNavigationUniversal( SkinTemplate $sktemplate, array &$links ) {
  // Make sure that this is not a special page, the page has contents, and the user can push.
  $title = $sktemplate->getTitle();
  if ( $title && $title->getNamespace() !== NS_SPECIAL && $title->exists() ) {
    $links['actions']['exportXML']  = [ 'text' => 'Export as XML',       'class' => '', 'href' => 'javascript:exportPage (undefined, "xml");', 'title' => 'Export this page in XML format' ];
    $links['actions']['exportWiki'] = [ 'text' => 'Export as Wikitext',  'class' => '', 'href' => 'javascript:exportPage (undefined, "wki");', 'title' => 'Export this page in wikitext format'];
  }
}


// inject a selector for other dantewikis provided MediaWiki:Dantewikis is properly populated
public static function onSkinAfterPortlet ( $skin, $portlet, &$html ) {
  global $wgSitename, $wgOut;  

  // inject a selector to other sites depending on the configuration of the page Mediawiki:Dantewikis
  // window.selectorChanged (event) in kundry.js takes care of this in Javascscript side
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
       
     self::debugLog ("Before matching: " . $code . "\n");  
      preg_match ('/<pre>(.*)<\/pre>/ism', $code, $matches);  
      self::debugLog ("MATCH: " . print_r ($matches, true). "\n");
      
      $arr = json_decode ($matches[1], true);
      
      // ensure that array contains the current site
      $found = false;
      foreach ($arr as $val) {if (strcmp ($val["name"], $wgSitename) == 0) {$found = true;}}
        if (!$found) {      
        $obj = array();
        $obj["name"] = $wgSitename;       $obj["class"] = "";   $obj["base"] = "";
        array_unshift ( $arr, $obj );      
      }
            
      if (is_array ($arr) && count ($arr) > 0) {
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
      else {
        return false;
      }
    }  
  }
}


public static function onLinkerMakeExternalLink( &$url, &$text, &$link, &$attribs, $linktype ) {
  //self::debugLog ("MakeExt: before text=" . HtmlArmor::getHtml ($text) ."\n"); 
  //self::debugLog ("MakeExt: before link=" . $link ."\n");     
  //self::debugLog ("MakeExt: before linktype=" . $linktype ."\n");  
  self::extractAttributes ($text, $attribs);      // implements the neccessary modifications in $text and $attribs
  return true;                                    // yes, we want to modify the produced html
}




// $text is what Mediawiki believes should be shown as anchor text
// $target is what Mediawiki believes should be shown as target
// Samples:  [[target]]  make $target and $text equal to  target
// [[target | text]]  overwrites $text with the given text, some special stuff with underlines however.
//public static function onHtmlPageLinkRendererBegin( MediaWiki\Linker\LinkRenderer $linkRenderer, &$target,  &$text, &$attribs, &$query, &$ret ) {
//public static function onHtmlPageLinkRendererBegin( MediaWiki\Linker\LinkRenderer $linkRenderer, &$target,  &$text, &$attribs, &$query, &$ret ) {  
public static function onHtmlPageLinkRendererEnd( MediaWiki\Linker\LinkRenderer $linkRenderer, $target, $isKnown, &$text, &$attribs, &$ret ) {  
  global $wgScript;
  // self::debugLog ("LinkRendererBegin: before text=" . HtmlArmor::getHtml ($text) ."\n");  self::debugLog ("LinkRendererBegin: before target=" . $target ."\n");   
  
  if ( strpos (HtmlArmor::getHtml($text), "¦") === false ) {return true;}  // text contains no broken pipe: keep anchor as it is
  $flag = self::extractAttributes ($text, $attribs);
  
  // self::debugLog ("LinkRendererBegin: after text=" . HtmlArmor::getHtml ($text) ."\n"); self::debugLog ("LinkRendererBegin: after target=" . $target ."\n\n");     
  
  if ($flag) {
    $attribText = "";
    $title = $target;
    foreach ($attribs as $key => $value) {
      self::debugLog ("Attrib: " . $key. " IS: " . $value ."\n\n");
      if ( strcmp ($key, "title") == 0 ) { $title = $value;}
      else { $attribText .= " " . $key . "='" . addslashes ($value) . "' "; }
    }
    if (!$isKnown) {  // for unknown internal links we need a special formatting
      $ret = "<a href='".$wgScript."?title=".$target."&action=edit&redlink=1' class='new' title='". $target. " (page does not exist)'>".$title."</a> ";  }
    else           { $ret = "<a href='".$text."' title='". $title ."' " . $attribText. " >". HtmlArmor::getHtml ($text)."</a>"; }
    return false; 
  }
  
  else { return true; }  // true: keep the original anchor as it is 
}


// $text is a string or Armor object which may contain a broken pipe symbol
// $attribs is an array of attributes
private static function extractAttributes ( &$text, &$attribs ) {
  $text = HtmlArmor::getHtml ($text);                    // need a text here but text might also be an HtmlArmor object.
  if ( $text === null ) {return false;} 

  $arr  = preg_split( '/¦/u', $text );     // split the input text on the pipe symbol;  need u for unicode matching
  $text = array_shift( $arr );             // the text to be used for the link is the part before the first vertical bar
    
  foreach ( $arr as $a ) {                // iterate over the remaining portions as they are seperated by a pipe symbol ¦
    $pair = explode( '=', $a );           
    if ( isset( $pair[1] ) ) {            // we found an x=y form
      if (  in_array( trim($pair[0]), array ('class', 'style') ) ) {  // for class and style we AMEND existing values
        if ( isset( $attribs[trim($pair[0])] ) ) {$attribs[trim($pair[0])] = $attribs[trim($pair[0])] . ' ' . trim($pair[1]);}  // if set, amend
        else                                     {$attribs[trim($pair[0])] = trim($pair[1]); }                                  // if not yet set: set freshly                          
      }
      else if ( in_array( trim($pair[0]), array ('title', 'target') ) ) { $attribs[trim($pair[0])] = trim($pair[1]); }    // found an attribute for a set freshly strategy     
      else {}                                                                                                             // other attribute names are ignored
    }
    else { $attribs["data-other"]= trim($a); }  // if it is only a value without an x=y structure, so place the value into data-other 
  }
  return true;
}



// go from directory to url path independently from all other things. This is helpful when extensions are located at a different place than usual.
private static function get_current_file_url() { return  ( array_key_exists ("HTTPS", $_SERVER) ? "https://" : "http://" ) . $_SERVER['HTTP_HOST'].str_replace( realpath ($_SERVER['DOCUMENT_ROOT']) , '', realpath(__DIR__) )."/" ;  }


// this provides an early insert into the body 
public static function onOutputPageBeforeHTML( OutputPage &$out, &$text ) {
  $out->addStyle    (self::get_current_file_url()."kundry.css");    
  $out->addModules ( ["ext.kundry"] ); // adds style file too late and the element styled via data-hashref in kundry.css flashes upon a reload  
}

  
  
  // renders a href hovering reference tag
  public static function href ($in, $ar, $parser) {
    global $wgUploadDirectory;
    ( array_key_exists ("p",$ar) ? $page =  "data-page='" . $ar["p"]. "'" : "");
    
    $url = "/images/$in.pdf";                                                                            // construct url to fetch file
    $filePath = $wgUploadDirectory."/".$in.".pdf";                                                       // construct file system path
    
    if (file_exists ($filePath)) {
      $anchor = "<span data-hashref='".$in."' $page>REF</span>";
      $preload      = "<link rel='preload' href='$url'  as='fetch'>";                                      // preload the file in preparation of a hovering
      $preloadEmb   = "<link rel='preload' href='/myExtensions/Kundry/embedPresent.html' as='document'>";  // preload the html file
      $preloadEmbJS = "<link rel='preload' href='/myExtensions/Kundry/common.js' as='script'>";            // preload the common javascript portion
      return $preload.$preloadEmb.$preloadEmbJS.$anchor;                                                   // return the rendering result
    }
    else {
      return "<span class='referencedNotFound'>Referenced file ".$in." could not be found in dantepedia</span>";
    }
  } 

  
  // $in is a string in json format representing the meta data 
  public static function json ($in, $ar, $parser) {
    $jsonObject = json_decode ($in);                                     //  go from string to PHP object modelling json
    $jsonTxt =json_encode ( json_decode ($in), JSON_PRETTY_PRINT);  
    $jsonTxt = preg_replace_callback ('/^ +/m', function ($m) {return str_repeat (' ', strlen ($m[0]) / 2);}, $jsonTxt);  // adjust 4-indentation to 2-indentation
    return "<pre>" . $jsonTxt . "</pre>";;
  }
  
  
  private static function json2Html ($val) {
    if (is_array ($val)) {
      $tab = "<table class='json-table'>";
      foreach ($val as $key => $value) { 
        $tab .= "<tr><td class='json-key'>$key</td><td class='json-value'>$value</td></tr>";
      }
      $tab .= "</table>";
    }
    
  }
  
}





class RestApiExample extends Handler {
   private const VALID_ACTIONS = [ 'echo', 'reverse', 'shuffle', 'md5' ];
  
  public function execute () {
    
    $responseFactory = self::getResponseFactory();
      return $responseFactory->createTemporaryRedirect ("http://www.cnn.com");   ///// PROTECTED against access
  }
  
    public function needsWriteAccess() {return false;}

    public function getParamSettings() {

      return [
        'value_to_echo' => [
          self::PARAM_SOURCE => 'path',
          ParamValidator::PARAM_TYPE => 'string',
          ParamValidator::PARAM_REQUIRED => true,
        ],
        'text_action' => [
          self::PARAM_SOURCE => 'path',
          ParamValidator::PARAM_DEFAULT => 'echo',
          ParamValidator::PARAM_TYPE => self::VALID_ACTIONS,
          ParamValidator::PARAM_REQUIRED => false,
        ],
      ];
    }
  
  
}


class RestApiExampleOld extends SimpleHandler {
  private const VALID_ACTIONS = [ 'echo', 'reverse', 'shuffle', 'md5' ];

// MUST set the structure in extension.json
// 
  //private function userHasRight ( ) { return false;}


  public function run( $valueToEcho ) {
    
    $pm = MediaWikiServices::getInstance()->getPermissionManager();
    if ( $pm->userHasRight( $this->getAuthority()->getUser(), "read-kundry-endpoint" ) ) {
    
   //   $responseFactory = self::getResponseFactory();
   //   return $responseFactory->createRedirectBase ("http://www.cnn.com");   ///// PROTECTED against access
       return [ 'echo' => "dada".$valueToEcho ];
    }
    else {
      return new MediaWiki\Rest\Response (  "You do not have sufficient rights for invoking this URL" );
  }
    
   // die ($valueToEcho);
    
    /*
    switch ( $this->getValidatedParams()['text_action'] ) {
      case 'echo':    return [ 'echo' => "dada".$valueToEcho ];
      case 'reverse': return [ 'echo' => strrev( $valueToEcho ) ];
      case 'shuffle': return [ 'echo' => str_shuffle( $valueToEcho ) ];
      case 'md5':     return [ 'echo' => md5( $valueToEcho ) ];
    }
    */
  }


  public function needsWriteAccess() {return false;}

  public function getParamSettings() {

    return [
      'value_to_echo' => [
        self::PARAM_SOURCE => 'path',
        ParamValidator::PARAM_TYPE => 'string',
        ParamValidator::PARAM_REQUIRED => true,
      ],
      'text_action' => [
        self::PARAM_SOURCE => 'path',
        ParamValidator::PARAM_DEFAULT => 'echo',
        ParamValidator::PARAM_TYPE => self::VALID_ACTIONS,
        ParamValidator::PARAM_REQUIRED => false,
      ],
    ];
  }
}