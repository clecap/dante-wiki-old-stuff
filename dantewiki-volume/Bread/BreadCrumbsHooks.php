<?php

class BreadCrumbsHooks {

  public static function onSiteNoticeAfter ( &$siteNotice, $skin) {
    // patch style and script directly into the site notice - guarantees that the lazy loading of MW does not ruin things by flickering
    $style = "<style>#breadcrumbinsert {position:relative; top:-11px; height:17px; max-height:17px; min-height:17px; text-align:left;overflow:hidden;}";
    $style .= "#breadcrumbinsert {background-color: #f6f6f6;border-color: #dcdcdc;border-radius: 3px;border-style: solid;border-width: 1px;padding-left:0.2em;}</style>";
    $style .= "<script src='/myExtensions/Bread/breadCrumbs.js'></script>";
    $siteNotice .=  $style.'<div id="breadcrumbinsert"></div><script>window.doBreadNow()</script>';  
    return false;
  }

  // at this moment in the build process we have easy access to the current page name and we add the current page name into the crumbs for the next occasion
  public static function onBeforePageDisplay( $output, $skin ) {
    $title = $output->getTitle();
    if ( self::getDisplayTitle( $title, $displayTitle ) ) {$pagename = $displayTitle;}  else {$pagename = $title->getPrefixedText();}  
    $output->addInlineScript ("window.addFreshCrumb('".$pagename."')");
  }


// Get displaytitle page property text.
// $title the Title object for the page
// &$displaytitle to return the display title, if set
// return bool true if the page has a displaytitle page property that is different from the prefixed page name, false otherwise
  private static function getDisplayTitle( Title $title, &$displaytitle ) {
    $pagetitle = $title->getPrefixedText();
    $title     = $title->createFragmentTarget( '' );
    
    if ( $title instanceof Title && $title->canExist() ) {
      $values = PageProps::getInstance()->getProperties( $title, 'displaytitle' );
      $id = $title->getArticleID();
      if ( array_key_exists( $id, $values ) ) {
        $value = $values[$id];
        if ( trim( str_replace( '&#160;', '', strip_tags( $value ) ) ) !== '' && $value !== $pagetitle ) {
          $displaytitle = $value;
          return true;
        }
      }
    }
    return false;
  }
  
}
