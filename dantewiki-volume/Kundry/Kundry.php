<?php

  use MediaWiki\Rest\SimpleHandler;
 use MediaWiki\Rest\Handler;  
  use Wikimedia\ParamValidator\ParamValidator;

use MediaWiki\MediaWikiServices;

// Example class to echo a path parameter
// We want to check if we can verify user rights in a REST API endpoint



class Kundry {
  
  static function debugLog ($text) {if($tmpFile = fopen( "/var/www/html/myExtensions/Kundry/LOG", 'a')) {fwrite($tmpFile, $text);  fclose($tmpFile);}  else {throw new Exception ("debugLog could not log"); }}
  
  public static function onParserFirstCallInit( Parser $parser ) {                                                             // Register parser callback hooks   
    $parser->setHook ( 'json',  function ($in, $ar, $parser, $frame) { return  Kundry::json ($in, $ar, $parser);}    );        // implement a <json> construct
    
    $parser->setHook ( 'href',  function ($in, $ar, $parser, $frame) { return  Kundry::href ($in, $ar, $parser);}    );        // implement a <href>  hovering reference tag:.. h ref for Hovering REFerence
    
    $parser->setHook ( 'kex',  function ($in, $ar, $parser, $frame) { return  Kundry::kex ($in, $ar, $parser);}    );          // provide a kundry export link      
    $parser->setHook ( 'kexs',  function ($in, $ar, $parser, $frame) { return  Kundry::kexs ($in, $ar, $parser);}    );          // provide a kundry export link special        
    
    $parser->setHook ( 'kexd',  function ($in, $ar, $parser, $frame) { return  Kundry::kexd ($in, $ar, $parser);}    );          // TO TEST Kundry marriage with DPL3
    
  }
 /////////////////////////////// TODO: this is a clash with something we also do in Tree And Menu =================================== ?? is the hook active here ?
// HOOK, called when the skin finds a portlet to render; used to attach stuff to portlets
public static function onSkinAfterPortlet ( $skin, $portlet, &$html ) {
  global $wgSitename;
//  $html = $html . "<script>console.error ('found $portlet');</script>";    // use only during development to check the name of the portles to use
  // we inject the name of the specific dantepedia 
  if ( strcmp ("personal", $portlet) == 0) {
    $html = $html . "<script>console.error ('$wgSitename $portlet');".
       "var span = document.createElement ('span');".
       "span.innerHTML = '$wgSitename'; span.className='personal-wiki-name' ;var par = document.getElementById ('pt-userpage');".
       "par.parentNode.insertBefore (span,par ); </script>";
  }
 
  return true;
}



// add export actions to the page
public static function onSkinTemplateNavigationUniversal( SkinTemplate $sktemplate, array &$links ) {
  // Make sure that this is not a special page, the page has contents, and the user can push.
  $title = $sktemplate->getTitle();
  if ( $title && $title->getNamespace() !== NS_SPECIAL && $title->exists() ) {
    $links['actions']['exportXML']  = [ 'text' => 'Export as XML',       'class' => '', 'href' => 'javascript:exportPage (undefined, "xml");', 'title' => 'Export this page in XML format' ];
    $links['actions']['exportWiki'] = [ 'text' => 'Export as Wikitext',  'class' => '', 'href' => 'javascript:exportPage (undefined, "wki");', 'title' => 'Export this page in wikitext format'];
  }
}



public static function onLinkerMakeExternalLink( &$url, &$text, &$link, &$attribs, $linktype ) {
  self::extractAttributes ($text, $attribs);
  return true;  // we want to modify the produced html
}

/*
public static function onHtmlPageLinkRendererBegin( MediaWiki\Linker\LinkRenderer $linkRenderer, $target,  &$text, &$attribs, &$query, &$ret ) {
  global $wgRequest; 
  $text = HtmlArmor::getHtml ($text);                    // need a text here but text might also be an HtmlArmor object.
  if ( $text === null ) {return false;} 
  $arr  = preg_split( '/¦/u', $text );    // split the input text on broken pipe symbols;  need u for unicode matching
  $text = array_shift( $arr );            // the text of the link is the first portion
  
  return true; // we want an anchor element to be the result of the process
}
*/

public static function onHtmlPageLinkRendererEnd( MediaWiki\Linker\LinkRenderer $linkRenderer, $target, $isKnown, &$text, &$attribs, &$ret ) {
  self::extractAttributes ($text, $attribs);
  return true; // we want an anchor element to be the result of the process
}




private static function extractAttributes ( &$text, &$attribs ) {
  $text = HtmlArmor::getHtml ($text);                    // need a text here but text might also be an HtmlArmor object.
  if ( $text === null ) {return false;} 

  $arr  = preg_split( '/¦/u', $text );    // split the input text on broken pipe symbols;  need u for unicode matching
  $text = array_shift( $arr );            // the text of the link is the first portion
  
  foreach ( $arr as $a ) {                // iterate over the remaining portions as seperated along ¦
    $pair = explode( '=', $a );           
    if ( isset( $pair[1] ) ) {            // found an x=y form
      if (  in_array( trim($pair[0]), array ('class', 'style') ) ) {  // found an attribute for the amend strategy   
        if ( isset( $attribs[trim($pair[0])] ) ) {$attribs[trim($pair[0])] = $attribs[trim($pair[0])] . ' ' . trim($pair[1]);}  // if set, amend, otherwise set freshly
        else {$attribs[trim($pair[0])] = trim($pair[1]); }                          
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
  

  public static function kex ($in, $ar, $parser) {
    //$thisPage = ;
    $ret = "<a href='javascript:window.exportPage();'  >**KUNDRY EXPORT TEST LINK**</a>";
    return $ret;
  }
  
  
  public static function kexs ($in, $ar, $parser) {
    //$thisPage = ;
    $ret = "<a href='javascript:window.exportPages();'  >**KUNDRY EXPORT SPECIAL TEST LINK**</a>";
    return $ret;
  }
  
  
  public static function href ($in, $ar, $parser) {
    ( array_key_exists ("p",$ar) ? $page =  "data-page='" . $ar["p"]. "'" : "");
    $anchor = "<span data-hashref='".$in."' $page>REF</span>";
    
    $url = "/images/$in.pdf";
    
    $preload      = "<link rel='preload' href='$url'  as='fetch'>";  // prepare for a call of the hovering link
    $preloadEmb   = "<link rel='preload' href='/myExtensions/Kundry/embedPresent.html' as='document'>";
    $preloadEmbJS = "<link rel='preload' href='/myExtensions/Kundry/common.js' as='script'>";
    
    // $ret = "<iframe height='900' width='900' id='".$in."' src='http://localhost:8081/images/".$in.".pdf'  style='display:none;position:relative;' ></iframe>";
      
   // $ret = "<embed height='900' width='900' id='".$in."' src='http://localhost:8081/images/".$in.".pdf'  style='display:none;position:relative;' ></embed>";  
       
    // hovering is added in kundry.js in javascript portion 
//      $ret = "<embed height='900' width='900' id='".$in."' src='/myExtensions/Kundry/embedPresent.html?hash=".$in."'   ></embed>";   // TODO PATH ANPASSEN ??????????????????????
    
    
    //  $ret = "<iframe height='900' class='iframe-hover' style='display:none;' width='900' id='".$in."' src='/myExtensions/Kundry/embedPresent.html?hash=".$in."'   ></iframe>"; 
    
    return $preload.$preloadEmb.$preloadEmbJS.$anchor;
  } 

  
  // $in is a string in json format representing the meta data 
  public static function json ($in, $ar, $parser) {
    $jsonObject = json_decode ($in);                     //  go from string to PHP object modelling json
    $jsonText = json_encode (json_decode ($in));         //  go from PHP value to json string
    
    $title = $parser->getTitle();
    $link = "<a href='javascript:openKundryWindow(\"$title\", {}, $jsonText);'>Edit bibliographic data of $title</a>";
    
    $txt = "<pre>";
    $txt .=json_encode ( json_decode ($in), JSON_PRETTY_PRINT);
    $txt .= "</pre>";
    
    $val = json_decode ($in);
    
    $tab = "<table class='json-table'>";
    foreach ($val as $key => $value) {
      $tab .= "<tr><td class='json-key'>$key</td><td class='json-value'>$value</td></tr>";
    }
    $tab .= "</table>";
    
    $final = $tab . $txt;
    return   $link . $final;
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