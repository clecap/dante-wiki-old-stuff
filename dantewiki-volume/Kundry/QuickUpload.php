<?php

class QuickUpload extends SpecialPage {
  function __construct() {
    parent::__construct( 'QuickUpload' );  // name of the special page, will be translated in the alias file
  }

  function getGroupName() {return 'media';}  // in which group should this be placed in the list of all special pages

  function execute( $par ) {
    $request = $this->getRequest();
    $output  = $this->getOutput();
    
    ///// TODO: PROBLEM EXTENSION PATH
    $output->addLink ( array  ("href" => "/myExtensions/Kundry/kundry.css", "type"=> "text/css", "rel" => "stylesheet"));  // cave: MUST have a leading / or the server injects an index.php and delivers a Wiki Page instead :-(
    $output->addHeadItem("kundryScript", '<script type="text/javascript" src="/myExtensions/Kundry/kundry.js"></script>');
    $output->addHeadItem("kundryCommon", '<script type="text/javascript" src="/myExtensions/Kundry/common.js"></script>');    
    
   // $output->addModules ( ["ext.kundry"] );  // add script file; this would add style  and script file, but *BOTH* too late :-(
    $this->setHeaders();

    # Get request data from, e.g.
    $param = $request->getText( 'param' );

    $output->addHTML ("<div>Drag and drop file(s) on the respective areas to upload them to Dantewiki</div>");
   
    $output->addHTML ("<div id='kundry-hash-drop'>Store media files under their <b>hash value</b> (recommended)</div>");

    $output->addHTML ("<div id='kundry-hash-meta-drop'>Store media files under their <b>hash value and enter meta data</b> (recommended)</div>");
    
    $output->addHTML ("<div id='kundry-original-drop'>Store media files under their <b>original filename</b> with default meta data (use only for 'good' filenames)</div>");
    
    $output->addHTML ("<div id='kundry-restore'>For restore, <b>drop backups here</b> (XML-archives of exported pages and TARs of exported files)</div>");    
    
    $output->addHTML ("<button id='kundry-page-archive'>Get Page Archive</button><button id='kundry-file-archive'>Get File Archive</button>");    
    
    $output->addHTML ("<script>window.kundrySpecialInit();</script>");

/*
    $wikitext = 'Hello world!';
    $output->addWikiTextAsInterface( $wikitext );
    
  */  
    
  }
}